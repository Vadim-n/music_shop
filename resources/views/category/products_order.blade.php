@extends('layouts.app')

@section('content')
    <div id="home" class="container">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">Сортировка товаров категории {{$category->name}}</div>

                    <product-order
                        :category-id="{{$categoryId}}">
                    </product-order>

                </div>
            </div>
        </div>
    </div>
@endsection
