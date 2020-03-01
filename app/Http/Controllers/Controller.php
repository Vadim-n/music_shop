<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\DB;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Parse and apply standard $orderBy string
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array                    $mapping
     *
     * @return \Closure
     */
    protected function applyOrderByClosure($request, $mapping, $db = 'mysql')
    {
        return function ($q) use ($request, $mapping, $db) {
            foreach (explode(',', $request->input('$orderBy')) as $v) {
                list($field, $direction) = explode(' ', $v);
                if (array_key_exists($field, $mapping)) {
                    $dbField = $mapping[$field];
                } else {
                    continue;
                }
                if($db === 'pgsql'){
                    if ($direction == "desc"){
                        $direction .= ' nulls last';
                    }
                    elseif ($direction == "asc"){
                        $direction .=' nulls first';
                    }
                }
                if ($field === 'id') {
                    $q->orderByRaw("$dbField $direction");
                }
                else{
                    if($db === 'pgsql'){
                        $q->orderByRaw("$dbField $direction, id");
                    }else{
                        $q->orderByRaw("$dbField $direction");
                    }
                }
            }
            return $q;
        };
    }

    /**
     * Parse and apply standard $filter string
     *
     * @param  \Illuminate\Http\Request $request
     * @param  array                    $mapping
     * @param  string                   $filter_name
     *
     * @return \Closure
     */
    protected function applyFilterClosure($request, $mapping, $filter_name='$filter', $db = 'mysql')
    {
        return function ($q) use ($request, $mapping, $filter_name, $db) {
            $orArray = explode(' or ', $request->input($filter_name));
            if (count($orArray) > 1) {
                $q->where(function ($query) use ($request, $mapping, $orArray,$db) {
                    foreach ($orArray as $v) {
                        list($field, $op, $value) = explode(' ', $v, 3);
                        if ($value === 'true' || $value === 'false') {
                            $value = ($value === 'true');
                        } else {
                            if (preg_match("/^'(.*)'$/i", $value)) {
                                $isLiteral = true;
                                $value = '%' . substr($value, 1, -1) . '%';
                            } else {
                                $isLiteral = false;
                            }
                        }
                        if (array_key_exists($field, $mapping)) {
                            $dbField = $mapping[$field];
                        } else {
                            continue;
                        }
                        switch ($op) {
                            case 'eq':
                                if ($isLiteral) {
                                    $query->orWhere($dbField, $db==='pgsql' ? 'ILIKE' : 'LIKE', $value);
                                } else {
                                    $query->orWhere($dbField, '=', $value);
                                }
                                break;
                            case 'btw':
                                $dates = explode('*', $value);
                                $query->whereBetween($dbField, $dates);
                                break;
                            case '>=':
                                $query->where($dbField,'>=', $value);
                                break;
                            case '<=':
                                $query->where($dbField,'<=', $value);
                                break;
                            case '=':
                                if ((int)$value!==0)
                                    $query->orWhere($dbField, '=', (int)$value);
                                break;
                            case '>':
                                if ($value !== 'period') {
                                    switch ($value) {
                                        case 'hour':
                                            $three = 'CURRENT_DATE - INTERVAL \'1 HOUR\'';
                                            break;
                                        case 'day':
                                            $three = 'CURRENT_DATE - INTERVAL \'1 DAY\'';
                                            break;
                                        case 'week':
                                            $three = 'CURRENT_DATE - INTERVAL \'7 DAY\'';
                                            break;
                                        case 'month':
                                            $three = 'CURRENT_DATE - INTERVAL \'30 DAY\'';
                                            break;
                                        case 'quarter':
                                            $three = 'CURRENT_DATE - INTERVAL \'90 DAY\'';
                                            break;
                                        case 'year':
                                            $three = 'CURRENT_DATE - INTERVAL \'365 DAY\'';
                                            break;
                                        case 'undefined':
                                            $three = 'CURRENT_DATE - INTERVAL \'7 DAY\'';
                                            break;
                                        default:
                                            break;
                                    }
                                    $query->orWhere($dbField, '>=', DB::raw($three));
                                } else {

                                }
                                break;
                            default:
                                break;
                        }
                    }
                    return $query;
                });
            }
            else
            {
                $q->where(function ($query) use ($request, $mapping, $filter_name, $db) {
                    foreach (explode(' and ', $request->input($filter_name)) as $v) {
                        list($field, $op, $value) = explode(' ', $v, 3);
                        if ($value === 'true' || $value === 'false') {
                            $value = ($value === 'true');
                        }
                        else {
                            if (preg_match("/^'(.*)'$/i", $value)) {
                                $isLiteral = true;
                                $value = '%' . substr($value, 1, -1) . '%';
                            } else {
                                $isLiteral = false;
                            }
                        }
                        if (array_key_exists($field, $mapping)) {
                            $dbField = $mapping[$field];
                        } else {
                            continue;
                        }
                        switch ($op) {
                            case 'eq':
                                if ($isLiteral) {
                                    $query->where($dbField, $db==='pgsql' ? 'ILIKE' : 'LIKE', $value);
                                } else {
                                    $query->where($dbField, '=', $value);
                                }
                                break;
                            case 'btw':
                                $dates = explode('*', $value);
                                $query->whereBetween($dbField, $dates);
                                break;
                            case '>=':
                                $query->where($dbField,'>=', $value);
                                break;
                            case '<=':
                                $query->where($dbField,'<=', $value);
                                break;
                            case '=':
                                if ($value === '~')
                                    $query->whereNull($dbField);
                                else {
                                    if (is_numeric($value) && $value == 0) {
                                        $query->whereRaw("($dbField = $value or $dbField is null)");
                                    }
                                    else {
                                        $query->where($dbField, '=', $value);
                                    }
                                }
                                break;
                            case '>':
                                if ($value!=='period'){
                                    switch ($value){
                                        case 'hour':
                                            $three = 'CURRENT_DATE - INTERVAL \'1 HOUR\'';
                                            break;
                                        case 'day':
                                            $three = 'CURRENT_DATE - INTERVAL \'1 DAY\'';
                                            break;
                                        case 'week':
                                            $three = 'CURRENT_DATE - INTERVAL \'7 DAY\'';
                                            break;
                                        case 'month':
                                            $three = 'CURRENT_DATE - INTERVAL \'30 DAY\'';
                                            break;
                                        case 'quarter':
                                            $three = 'CURRENT_DATE - INTERVAL \'90 DAY\'';
                                            break;
                                        case 'year':
                                            $three = 'CURRENT_DATE - INTERVAL \'365 DAY\'';
                                            break;
                                        case 'undefined':
                                            $three = 'CURRENT_DATE - INTERVAL \'7 DAY\'';
                                            break;
                                        default:
                                            break;
                                    }
                                    $query->where($dbField, '>=', DB::raw($three));
                                }
                                else{

                                }
                                break;
                            default:
                                break;
                        }
                    }
                    return $query;
                });
            }
        };
    }
}
