<template>
    <div class="musicShop-table" :class="{'table-min': min_table}" style="position:relative;">
        <div v-show="loading" style="position:absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255, 0.7); z-index: 99;">
            <div style="position: absolute; top : 50%; left: 50%;">
                Загрузка...
            </div>
        </div>
        <div class="row mb-0">
            <div v-if="settings.searchable !== false && settings.global_search !== false"  class="col-lg-3 col-md-4 col-sm-6 col-xs-6">

                <label class="d-md-none control-label">
                    <!--<div v-if="loading"><span><i-->
                    <!--class="fa fa-cogs fa-spin txt-color-blueDark"></i> Загрузка...</span></div>-->
                    <!--<div v-else>&nbsp;</div>-->
                    <div></div>
                </label>
                <div class="input-group input-group-sm">
                    <div class="input-group-prepend">
                        <span class="input-group-text" rel="tooltip"  :title="advanced_search_title" ><i class="fa fa-search"></i> </span>
                    </div>
                    <input id="search_string" type="search" @keyup.enter.exact="defaultSearch()" @keyup.ctrl.enter="advancedSearch()" class="form-control form-control-sm"
                           v-model="searchQuery">
                </div>
            </div>
            <div v-if="settings.global_search === false && (settings.date_filter === true || settings.excel_downloading === true || settings.goods_filter === true)" class="row row-lg col-lg-5">
                <div class="d-none d-md-block col-sm-5" v-if="settings.date_filter === true">
                    <v-date-filter v-if="settings.date_filter === true"
                                   v-model="filters_additional.name"
                                   :refresh_indicator="refresh_indicator"
                                   @click="onclick">
                    </v-date-filter>
                </div>
                <div v-if="settings.goods_filter === true" class="d-none d-md-block col-sm-4">
                    <v-goods-filter
                                    :storage_key="storage_key"
                                    :goods_filters="goods_filters"
                                    :custom_fields_url="custom_fields_url"
                                    :refresh_indicator="refresh_indicator"
                                    :show_custom_fields_filter="show_custom_fields_filter"
                                    @click="goodsFilter"
                                    @apply_params="applyParams">
                    </v-goods-filter>
                </div>
                <div class="d-none d-md-block col-sm-3" v-if="settings.excel_downloading === true">
                    <div v-if="settings.excel_downloading === true" class="dropdown stay-open">
                        <a href="" class="tx-gray-800" data-toggle="dropdown">
                            <div class="ht-25 pd-x-20 bd d-flex align-items-center justify-content-center">
                                <span class="mg-r-10 tx-13 tx-medium"><i style="font-size: 18px;" class="fa fa-download" aria-hidden="true"></i></span>
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu pd-10 wd-200">
                            <a :href="export_excel" target="_blank">
                                <div class="row float-left" style="width: 100%;">
                                    <label class="col-form-label col-md-5 text-md-right p-0" style="max-width: 15%;"><i
                                            class="fa fa-file-excel-o" aria-hidden="true"></i></label>
                                    <div class="col-md-4">
                                        Excel
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- dropdown-menu -->
                    </div><!-- dropdown -->
                </div>
        </div>
            <div v-if="settings.global_search !== false && settings.excel_downloading" class="row row-lg col-lg-3">
                <div class="d-none d-md-block">
                    <div v-if="settings.excel_downloading === true" class="dropdown stay-open">
                        <a href="" class="tx-gray-800" data-toggle="dropdown">
                            <div class="ht-25 pd-x-20 bd d-flex align-items-center justify-content-center">
                                <span class="mg-r-10 tx-13 tx-medium"><i style="font-size: 18px;" class="fa fa-download" aria-hidden="true"></i></span>
                                <i class="fa fa-angle-down"></i>
                            </div>
                        </a>
                        <div class="dropdown-menu pd-10 wd-200">
                            <a :href="export_excel" target="_blank">
                                <div class="row float-left" style="width: 100%;">

                                    <label class="col-form-label col-md-5 text-md-right p-0" style="max-width: 15%;"><i
                                            class="fa fa-file-excel-o" aria-hidden="true"></i></label>
                                    <div class="col-md-4">
                                        Excel
                                    </div>
                                </div>
                            </a>
                        </div>
                        <!-- dropdown-menu -->
                    </div><!-- dropdown -->
                </div>
            </div>
            <div v-if="settings.global_search === false" class="col-md-1"></div>
            <div v-if="settings.global_search !== false && settings.date_filter !== true && settings.excel_downloading !== true && settings.goods_filter !== true" class="col-sm-3"></div>
            <div v-if="settings.global_search === false && settings.date_filter !== true && settings.excel_downloading !== true && settings.goods_filter !== true" class="col-sm-5"></div>
            <div class="col-lg-2 col-md-2 col-sm-3 col-xs-6">
                <label class="d-md-none control-label">&nbsp;</label>
                <a :class="loading ? 'disabled' : ''" class="btn-sm btn btn-primary col-xs-12" style="min-width: 116px;color:white" @click="refreshDefault"><i
                        class="fa fa-refresh"></i> Очистить фильтры</a>
            </div>
            <div v-if="settings.directory" class="col-lg-1 col-md-2 col-sm-3 col-xs-6">
                <label class="d-md-none control-label">&nbsp;</label>
                <a class="btn-sm btn btn-success col-xs-12" style="color:white" @click="show_modal"><i
                        class="fa fa-list-alt"></i>  Справочник</a>
            </div>
            <div v-if="!settings.directory" class="col-lg-1 col-md-2 col-sm-3 col-xs-6"></div>
            <div class="col-lg-3 col-md-4 col-sm-3 col-xs-12">
                <div class="row form-horizontal">
                    <label class="col-md-8 text-right" style="margin-top: 10px;">Показывать записей</label>
                    <div class="col-md-4">
                        <select v-model.number="step" class="form-control form-control-sm" name="pagesize" id="pagesize" :disabled="selectable">
                            <option :value="10">10</option>
                            <option :value="20">20</option>
                            <option :value="50">50</option>
                            <option :value="100">100</option>
                            <!--<option :value="9999">All</option>-->
                        </select>

                    </div>

                </div>
            </div>
        </div>
        <div class="row">&nbsp;</div>
        <div class="vtable smart-style-2">
            <div class="table-overflow-x">
                <table class="table table-hover table-bordered musicShop-table table-striped">
                    <thead class="thead-colored thead-info">
                    <tr>
                        <td class="vtable-plus d-lg-none"></td>
                        <th v-for="col in columns"
                            :rel="col.header_tooltip ? 'tooltip' : false"
                            :data-html="col.header_tooltip ? col.html_tooltip : false"
                            :title="col.header_tooltip ? col.header_tooltip : false"
                            style="cursor:pointer; padding-top:9px; padding-bottom: 9px;"
                            :style="{ width: col.width,  color: col.color, 'min-width':col.min_width  }"
                            :class="getHeaderClass(col)"
                            @click="switchSort(col)"><span class="title">{{ col.title }}</span>
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr v-if="settings.searchable !== false">
                            <td class="vtable-plus d-lg-none active"></td>
                            <td v-for="col in columns" :class="getHeaderClass(col)"
                                style="padding-right:5px; padding-left:5px;  position:relative;  background-color: #f5f5f5;">
                                <input v-if="(col.searchable && !col.search_dict && !col.search_date && !col.search_tree)" @keyup.enter="refresh" class="form-control form-control-sm" style="width:100%; min-width: 45px;" v-model="local_filters_input[col.name]"
                                       type="text">
                                <div class="dropdown stay-open" v-if="(col.searchable && col.search_date)"
                                     style="">
                                    <a href="" data-toggle="dropdown">
                                        <input @keyup.enter="refresh" class="form-control form-control-sm"
                                               style="width:100%; min-width: 45px;" v-model="local_date_filters_show[col.name]"
                                               type="text">
                                    </a>
                                    <div class="dropdown-menu pd-10 wd-200">
                                        <v-date-picker v-model="local_filter_date_from[col.name]"
                                                       @input="compareFilterDates(col, 1)"
                                                       style="width:100%; z-index: 2;"
                                                       class="date-boot-musicShop form-control form-control-sm"></v-date-picker>
                                        По:
                                        <v-date-picker v-model="local_filter_date_to[col.name]"
                                                       @input="compareFilterDates(col, 2)"
                                                       style="width:100%; z-index: 2;"
                                                       class="date-boot-musicShop form-control form-control-sm"></v-date-picker>
                                        <hr>
                                        <button @click="setPeriod(col)" class="btn btn-success btn-sm" >OK</button>
                                        <button @click="clearDatepicker(col)" class="btn btn-primary btn-sm">Очистить
                                        </button>
                                    </div>

                                </div>

                                <!--<v-date-picker v-if="(col.searchable && col.search_date)" v-model="local_filters[col.name]" style="width:100%;" class="date-boot-musicShop form-control form-control-sm"></v-date-picker>-->
                                <!--<input v-if="(col.searchable && col.search_date)" v-model="local_filters[col.name]" style="width:100%" name="calendar" type="date" >-->

<!--                                <v-select :class="{'table-item-right': col.dropdown_right}" v-if="(col.searchable && col.search_dict)" -->
<!--                                          class="dropdown-search-table form-control form-control-sm" data-vv-as=" " -->
<!--                                          :maxHeight="select_height" :width="col.width" :min-width="col.min_width" v-model="local_filters[col.name]" :options="dicts[col.search_dict]"/>-->
                                <custom-select v-if="(col.searchable && col.search_dict)" :isSm="true" v-model="local_filters[col.name]" :options="dicts[col.search_dict]"></custom-select>
                                <ul v-if="(col.searchable && !col.search_dict && !col.search_date && col.search_tree)" class="tree-children" style="width:100%; height: 28px; margin :0; padding: 0;">
                                    <v-tree-input
                                            v-for="(item, index) in parent_node"
                                            :key="getItemKey(item)"
                                            :parent_field_name="col.parent_field_name"
                                            :text_field_name="col.text_field_name"
                                            :children_field_name="col.children_field_name"
                                            :key_field_name="col.key_field_name"
                                            :index="index"
                                            :item="item"
                                            :maxHeight="treeHeight"
                                            @selected="selectedTree"
                                            :data="data">
                                    </v-tree-input>
                                </ul>
                                <div v-if="(col.type === 'checkbox' && items.length)" class="smart-form" style="min-height: 40px;width: 10px;margin-top: 4px; margin-left: 5px;">
                                    <label class="checkbox" style="padding-left: 7px;max-width: 10px; margin:auto; margin-top: -20px; top:16px;">
                                        <input tabindex='-1' :class="{ part: selected_not_all_items }" v-model="select_all_items" type="checkbox">
                                        <i data-swchon-text="YES" data-swchoff-text="NO"></i>
                                    </label>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-for="(item, index) in items" track-by="$index">
                        <tr :style="{'background-color': item.bg_color}">
                            <td class="vtable-plus d-lg-none"><i
                                    :class="{'fa fa-plus':(!item.vm_show_details), 'fa fa-minus':item.vm_show_details}"
                                    @click="item.vm_show_details = !item.vm_show_details"></i></td>
                            <td v-for="col in columns" :class="cellStyle(col, item)">
                                <i v-if="col.format === 'toggle'" :class="iconToggle(col, item)"
                                               aria-hidden="true"></i>
                                <a v-if="col.type === 'callback'" v-show="showByRule(col,item)" href="#"
                                   @click="buttonCallback($event, col,item)"
                                   :class="(col.class === undefined ? '' : col.class)">
                                    {{ displayCell(col, item) }}
                                </a>
                                <a v-if="col.type === 'button'" v-show="showByRule(col,item)" href="#"
                                   @click="buttonCallback($event, col,item)"
                                   :class="'btn btn-xs'+(col.class === undefined ? ' btn-primary' : ' '+col.class)">
                                    <i :class="'fa '+(col.icon !== undefined ? col.icon : '')"></i> {{ col.title }}
                                </a>
                                <div  v-if="col.type === 'checkbox'" class="smart-form" style="margin-top: -3px;margin-left: 5px;">
                                    <label class="checkbox">
                                        <input type="checkbox" v-show="showByRule(col,item)" :disabled="!showByRule(col,item)" v-model="item.is_selected" />
                                        <i data-swchon-text="YES" data-swchoff-text="NO"></i>
                                    </label>
                                </div>

                                <span style="display: inline-flex" v-if="col.type === 'buttons'">
                                    <span v-for="btn in col.items">
                                        <a :title="btn.tooltip"
                                           rel="tooltip"
                                           data-placement="top"
                                           v-if="showByRule(btn,item)" href="#"
                                           @click="buttonCallback($event, btn,item)"
                                           :class="'btn btn-group btn-xs'+(btn.class === undefined ? ' btn-primary' : ' '+btn.class)">
                                            <i :class="'fa '+(btn.icon !== undefined ? btn.icon : '')"></i>
                                            {{ btn.title }}
                                        </a>
                                    </span>
                                </span>
                                <span v-if="col.type === 'badge'">
                                    <b v-if="showByRule(col, item)"
                                        :class="'badge badge-primary'">
                                        {{ displayCell(col, item) }}
                                    </b>
                                    <span v-else>{{ displayCell(col, item) }} </span>
                                </span>
                                <a v-if="col.type === 'toggle'" @click="toggleCallback(col,item)">
                                     <v-toggle v-show="showByRule(col, item)"
                                             @click="toggleCallback(col,item)"
                                             v-model="item[col.name]"
                                             :options="{ size: 'small', on: 'Да', off: 'Нет' }"/>
                                </a>
                                <span style="display: inline-flex;right: -7px;position: relative;" v-if="col.type === 'badges'">
                                    <span v-for="badge in col.items">
                                        <b v-if="showByRule(badge, item) && badge.badge !== false"
                                            :class="'badge badge-new bg-color-'+(badge.color===undefined ? 'red' : badge.color)+' bounceIn animated'">
                                            {{ displayCell(badge, item) }}
                                        </b>
                                        <span v-if="showByRule(badge, item) && badge.badge === false">
                                            {{ displayCell(badge, item) }}
                                        </span>
                                    </span>
                                </span>
                                <a v-if="(col.type === undefined && col.href !== undefined && col.tooltip === undefined && createHref(col, item) !== '#' && col.tips !== true)"
                                   :href="createHref(col, item)"
                                   :target="col.target ? col.target : col.name">
                                    {{ displayCell(col, item) }}
                                </a>

                                <!-- new-->
                                <a v-if="(col.type === undefined && col.href !== undefined && col.tooltip === true && createHref(col, item) !== '#' && data_prop_name !== 'promo' && col.tips !== true)"
                                   :href="createHref(col, item)"
                                   rel="tooltip" :title="item[col.name]"
                                   :onclick="'window.location.href=\'' + createHref(col, item)+ '\''"
                                   :target="col.target ? col.target : col.name">
                                    {{ displayCell(col, item) }}
                                </a>

                                <a v-if="(col.tips === true && col.type === undefined && col.href === undefined)"
                                   rel="tooltip" :title="col.title">
                                    {{ displayCell(col, item) }}
                                </a>

                                <a v-if="(col.type === undefined && col.href !== undefined && col.tooltip === true && createHref(col, item) !== '#' && data_prop_name === 'promo')"
                                   :href="createHref(col, item)"
                                   :title="item.goods"
                                   :onclick="'window.location.href=\'' + createHref(col, item)+ '\''"
                                   data-placement="right"
                                   rel="tooltip"
                                   data-html="true"
                                   :target="col.target ? col.target : col.name">
                                    {{ displayCell(col, item) }}
                                </a>

                                <span v-if="(col.type === undefined && col.href !== undefined && col.tips !== true && createHref(col, item) === '#')">
                                    {{ displayCell(col, item) }}
                                </span>

                                <div v-if="col.tooltip === true"  rel="tooltip" :title="item[col.name]">
                                <span v-show="showByRule(col,item)"
                                      v-if="col.href === undefined && col.type === undefined  && col.format !== 'toggle' && col.tips !== true">
                                      {{displayCell(col, item)}}
                                </span>
                                </div>

                                <div v-else>
                                    <span v-show="showByRule(col,item)"
                                          v-if="col.href === undefined && col.type === undefined  && col.format !== 'toggle' && col.tips !== true">
                                      {{displayCell(col, item)}}
                                </span>
                                </div>
                            </td>
                        </tr>
                        <tr v-if="item.vm_show_details" class="d-lg-none">
                            <td :colspan="(columns.length+1)" style="background: whitesmoke;">
                                <table style="margin-left: 22px;">
                                    <tr v-for="col in columns" :class="cellDetailsStyle(col, item)">
                                        <th class="pull-right" style="padding-top: 7px;">{{col.title}}:</th>
                                        <td style="padding-left: 10px; text-align:left;">
                                            <i v-if="col.format === 'toggle'" :class="iconToggle(col, item)"
                                                   aria-hidden="true"></i>
                                            <a v-if="col.type === 'callback'" v-show="showByRule(col,item)" href="#"
                                               v-on:click.stop.prevent="buttonCallback($event, col,item)"
                                               :class="(col.class === undefined ? '' : col.class)">
                                               {{displayCell(col, item) }}
                                            </a>
                                            <a v-if="col.type === 'button'" v-show="showByRule(col,item)" href="#"
                                                v-on:click.stop.prevent="buttonCallback($event, col,item)"
                                                :class="'btn btn-xs'+(col.class === undefined ? ' btn-primary' : ' '+col.class)">
                                                <i :class="'fa '+(col.icon !== undefined ? col.icon : '')"></i>{{ col.title }}
                                            </a>
                                            <span style="display: inline-flex" v-if="col.type === 'buttons'">
                                                <span v-for="btn in col.items">
                                                    <a v-if="showByRule(btn,item)" href="#"
                                                       @click="buttonCallback($event, btn,item)"
                                                       :class="'btn btn-group btn-xs'+(btn.class === undefined ? ' btn-primary' : ' '+btn.class)">
                                                        <i :class="'fa '+(btn.icon !== undefined ? btn.icon : '')"></i>
                                                        {{ btn.title }}
                                                    </a>
                                                </span>
                                            </span>
                                            <span v-if="col.type === 'toggle'">
                                             <v-toggle v-show="showByRule(col, item)"
                                               @click="toggleCallback(col,item)"
                                               :value="item[col.name]==='Да'"
                                               :options="{ size: 'small', on: 'Да', off: 'Нет' }"/>
                                              </span>
                                            <span v-if="col.type === 'badge'">
                                                <b v-if="showByRule(col, item)"
                                                    :class="'badge bg-color-'+(col.color===undefined ? 'red' : col.color)+' bounceIn animated'">
                                                    {{ displayCell(col, item) }}
                                                </b>
                                                <span v-else>{{ displayCell(col, item) }} </span>
                                            </span>
                                            <span style="display: inline-flex;right: -7px;position: relative;" v-if="col.type === 'badges'">
                                                <span v-for="badge in col.items">
                                                    <b v-if="showByRule(badge, item) && badge.badge !== false"
                                                        :class="'badge bg-color-'+(badge.color===undefined ? 'red' : badge.color)+' bounceIn animated'">
                                                        {{ displayCell(badge, item) }}
                                                    </b>
                                                    <span v-if="showByRule(badge, item) && badge.badge === false">
                                                        {{ displayCell(badge, item) }}
                                                    </span>
                                                </span>
                                            </span>
                                            <a v-if="(col.type === undefined && col.href !== undefined && createHref(col, item) !== '#')"
                                               :href="createHref(col, item)"
                                               :target="col.target ? col.target : col.name">
                                                {{ displayCell(col, item) }}
                                            </a>
                                            <span v-show="showByRule(col,item)"
                                                  v-if="col.href === undefined && col.type === undefined  && col.format !== 'toggle'">
                                                {{ displayCell(col, item) }}
                                            </span>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot style="background-color: whitesmoke">
                    <tr>
                        <td :colspan="(columns.length+1)">
                            <div class="row">
                                <div class="col-sm-6">
                                    <b>
                                        <i v-if="step > 0">Показано с {{ gbNumber(fromRec) }} по {{ gbNumber(toRec) }} из {{ gbNumber(total) }} записей</i>
                                        <i v-if="step < 0">Показано с {{ gbNumber(fromRec) }} по {{ gbNumber(total) }} из {{gbNumber(total) }} записей</i>
                                    </b>
                                </div>
                                <div v-if="total > step && step > 0 && step != 9999" class="col-sm-6">
                                    <ul class="pagination pagination-circle mg-b-0">
                                        <li class="page-item previous">
                                            <a href="#" @click="page = page > 0 ? page - 1 : 0" aria-label="Previous"
                                               class="page-link">
                                                <i class="fa fa-angle-left"></i>
                                            </a>
                                        </li>
                                        <template v-if="lastPage < 10">
                                            <li class="page-item" :class="{ active: page == i }" v-for="i in _range(0, lastPage+1)"><a class="page-link"
                                                    v-on:click="page = i" href="#">{{ i + 1 }}</a></li>
                                        </template>
                                        <template v-if="lastPage >= 10">
                                            <li class="page-item" :class="{ active: page == 0 }"><a class="page-link" @click="page = 0" href="#">{{ 1 }}</a></li>
                                            <template v-for="i in _range(1, 5)">
                                                <li class="page-item" :class="{ active: page == i }" v-if="page < 4"><a class="page-link" @click="page = i" href="#">{{ i + 1 }}</a></li>
                                            </template>
                                            <li class="page-item"><span class="ellipsis p-2">...</span></li>
                                            <template v-for="i in _range(page - 1, page + 2)">
                                                <li class="page-item" :class="{ active: page == i }" v-if="page < lastPage - 3 && page > 3"><a class="page-link"  @click="page = i" href="#">{{ i + 1 }}</a></li>
                                            </template>
                                            <li class="page-item" v-if="page < lastPage - 3 && page > 3"><span class="ellipsis p-2">...</span></li>
                                            <template v-for="i in _range(lastPage - 4, lastPage)">
                                                <li class="page-item" :class="{ active: page == i }" v-if="page > lastPage - 4"><a class="page-link" @click="page = i" href="#">{{ i + 1 }}</a></li>
                                            </template>
                                            <li class="page-item" :class="{ active: page == lastPage }"><a class="page-link" @click="page = lastPage" href="#">{{ lastPage + 1 }}</a></li>
                                        </template>
                                        <li class="page-item next">
                                            <a href="#"  @click="page = page < lastPage ? page + 1 : lastPage" aria-label="Next" class="page-link">
                                                <i class="fa fa-angle-right"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </td>
                    </tr>
                    </tfoot>
                </table>
                <br/>
            </div>
        </div>
    </div>
</template>

<script>
    export default  {
        props: [
            'id',
            'data_src',
            'de_data',
            'data_prop_name',
            'columns',
            'filters',
            'filters_input',
            'filters_additional',
            'filters_default',
            'dicts',
            'advanced_search_title',
            'active_tips',
            'settings',
            'reload',
            'format',
            'start_step',
            'no_empty',
            'with_header',
            'export_excel',
            'storage_key',
            'goods_filters',
            'show_custom_fields_filter',
            'custom_fields_url'
        ],
        data: function () {
            return {
                refreshing: false,
                items: {},
                sortCol: null,
                sortDir: null,
                total: 0,
                step: 20,
                page: 0,
                directory: false,
                searchQuery: null,
                advanced_search: false,
                value: null,
                loading: false,
                first: false,
                loadingg: true,
                highlight: false,
                change_active_tips: false,
                select_height: '400px',
                default_sort_col: '',
                default_sort_dir: '',
                default_filters: {},
                treeHeight: '',
                width: 1000,
                color: '#f9f9f9',
                columns_as_object: {},
                collapsed: false,
                window_width: 10000,
                show_details: {},
                select_all_items: false,
                selected:[],
                min_table: false,
                data:[],
                bold: false,
                refresh_indicator: false,
                parent_field_name: 'parent_uuid',
                text_field_name: 'name',
                children_field_name: 'children',
                key_field_name: 'uuid',
                local_filters: this.filters,
                local_filters_input: {},
                local_date_filters_show: {},
                local_filter_date_from: {},
                local_filter_date_to: {},
                local_filters_additional: this.filters_additional
            };
        },
        created: function () {
            this.checkPageFilters();
            var table_page = localStorage.getItem(this.storageIndex('page'));
            if (table_page !== null) {
                this.page = JSON.parse(table_page);
            }
            if (musicShop.isEmpty(this.settings)) {
                this.settings = {
                    refresh_default: false
                };
            }
            var _this= this;
            _.forEach(this.columns, function (item) {
                if (item.src){
                    _this.tree_data(item);
                }

                if (item.new_width) {
                    item.width = item.new_width;
                }
            });
            // Init sorting column and direction
            var savedSortCol = localStorage.getItem(this.storageIndex('sortCol'));
            if (savedSortCol !== null) {
                this.sortCol = savedSortCol;
                if (this.settings.sort_on_init === undefined) {
                    this.default_sort_col = this.columns[0].name;
                } else {
                    this.default_sort_col = this.columns[this.settings.sort_on_init.col].name;
                }
            } else {
                if (this.settings.sort_on_init === undefined) {
                    this.sortCol = this.columns[0].name;
                }
                else {
                    this.sortCol = this.columns[this.settings.sort_on_init.col].name;
                }
                this.default_sort_col = this.sortCol;
            }
            var savedSortDir = localStorage.getItem(this.storageIndex('sortDir'));
            if (savedSortDir !== null) {
                this.sortDir = savedSortDir;
                if (this.settings.sort_on_init === undefined) {
                    this.default_sort_dir = this.columns[0].sortDir === undefined ? 'desc' : this.columns[0].sortDir;
                }
                else {
                    this.default_sort_dir = this.settings.sort_on_init.dir === undefined ? 'desc' : this.settings.sort_on_init.dir;
                }
            } else {
                if (this.settings.sort_on_init === undefined) {
                    this.sortDir = this.columns[0].sortDir === undefined ? 'desc' : this.columns[0].sortDir;
                }
                else {
                    this.sortDir = this.settings.sort_on_init.dir === undefined ? 'desc' : this.settings.sort_on_init.dir;
                }
                this.default_sort_dir = this.sortDir;
            }
            if (this.start_step != null) {
                this.step = parseInt(this.start_step);
            }
            var savedStepJson = localStorage.getItem(this.storageIndex('step'));
            if (savedStepJson !== null) {
                this.step = JSON.parse(savedStepJson);
            }
            // Init filtering
            var savedFiltersJson = localStorage.getItem(this.storageIndex('local_filters'));
            if (savedFiltersJson !== null) {
                this.local_filters = JSON.parse(savedFiltersJson);
            } else {
                if (this.filters_default === undefined) {
                    this.default_filters = _.clone(this.local_filters);
                } else {
                    this.default_filters = this.filters_default;
                }
            }
            let c_obj = this.columns_as_object;
            _.map(this.columns, function (item) {
                c_obj[item.name] = item;
            });
            if (this.local_filters_additional === undefined) {
                this.local_filters_additional = {};
            }
            // var savedFiltersAddJson = localStorage.getItem(this.storageIndex('local_filters_additional'));
            // if (savedFiltersAddJson !== null) {
            //     this.local_filters_additional = JSON.parse(savedFiltersAddJson);
            // }
            if(this.total < 8)
                this.min_table = true;
            else this.min_table = false;
            var savedFiltersInputJson = localStorage.getItem(this.storageIndex('local_filters_input'));
            if (savedFiltersInputJson !== null) {
                this.local_filters_input = JSON.parse(savedFiltersInputJson);
            } else {
                if (this.filters_default === undefined) {
                    if(this.local_filters_input !== undefined)
                        this.default_filters = _.clone(this.local_filters_input);
                    else
                        this.local_filters_input={};
                } else {
                    this.default_filters = this.filters_default;
                }
            }

            let savedFiltersShowJson = localStorage.getItem(this.storageIndex('local_date_filters_show'));
            if (savedFiltersShowJson !== null) {
                this.local_date_filters_show = JSON.parse(savedFiltersShowJson);
            }
            let savedFiltersDateFromJson = localStorage.getItem(this.storageIndex('local_filter_date_from'));
            if (savedFiltersDateFromJson !== null) {
                this.local_filter_date_from = JSON.parse(savedFiltersDateFromJson);
            }
            let savedFiltersDateToJson = localStorage.getItem(this.storageIndex('local_filter_date_to'));
            if (savedFiltersDateToJson !== null) {
                this.local_filter_date_to = JSON.parse(savedFiltersDateToJson);
            }

        },
        // mounted:function(){
        //     $('[data-toggle="tooltip"]').tooltip({
        //         trigger : 'hover',
        //         boundary : "viewport"
        //     });
        // },
        computed: {
            parent_node: function () {
                var $this = this;
                return _.filter(this.data, function (item) {
                    return (item[$this.parent_field_name] === null);
                })
            },
            q_path: function() {
                return this.q_fast + this.q_slow;
            },
            lastPage: function () {
                return Math.ceil(this.total / this.step) - 1;
            },
            q_input: function () {
                let q = '';
                let q_arr = [];
                let c_obj = this.columns_as_object;
                _.forEach(this.local_filters_input, function (v, k) {
                    if (v !== null && v !== '' && v !== undefined) {
                        if ((c_obj[k] === undefined || c_obj[k].search_dict === undefined) && c_obj[k].format !== 'number' && c_obj[k].format !== 'currency' && c_obj[k].format !== 'raw_number' && c_obj[k].format !== 'date') {
                            q_arr.push(k.replace('.', '_') + ' eq ' + '\'' + v + '\'');
                        }
                        else {
                            if (c_obj[k].format === 'number' || c_obj[k].format === 'currency' || c_obj[k].format === 'raw_number') {
                                q_arr.push(k.replace('.', '_') + ' = ' + Number(v));
                            }
                            else{
                                if (c_obj[k].format === 'date') {
                                   let arr= v.split(':');
                                   if(arr.length > 1){
                                       if(arr[0] === 'btw'){
                                           q_arr.push(k.replace('.', '_') + ' btw ' + arr[1]);
                                       }
                                       if(arr[0] === '>='){
                                           q_arr.push(k.replace('.', '_') + ' >= ' + arr[1]);
                                       }
                                       if(arr[0] === '<='){
                                           q_arr.push(k.replace('.', '_') + ' <= ' + arr[1]);
                                       }

                                   }else{
                                        q_arr.push(k.replace('.', '_') + ' eq ' + '\'' + v + '\'');
                                   }

                                } else {
                                    q_arr.push(k.replace('.', '_') + ' = ' + v.value);
                                }
                            }
                        }
                    }
                });
                if (q_arr.length > 0) {
                    if (this.q_slow === '')
                        q += '&$filter=' + q_arr.join(' and ');
                    else
                        if(this.q_slow.split('$filter').length<2)
                            q += '&$filter=' + q_arr.join(' and ');
                        else
                            q += ' and ' + q_arr.join(' and ');
                }
                if (this.searchQuery !== null && this.searchQuery !== '') {
                    q += '&search=' + encodeURI(this.searchQuery);
                }
                if (this.advanced_search === true) {
                    q += '&advanced_search=' + encodeURI('true');
                }
                return q;
            },
            q_slow: function () {
                let q = '';
                let q_arr = [];
                let c_obj = this.columns_as_object;
                _.forEach(this.local_filters, function (v, k) {
                    if (v !== null && v !== '' && v !== undefined) {
                        if ((c_obj[k] === undefined || c_obj[k].search_dict === undefined) && c_obj[k].format !== 'number' && c_obj[k].format !== 'currency' && c_obj[k].format !== 'raw_number') {
                            if(c_obj[k].format!=='date'){
                            q_arr.push(k.replace('.', '_') + ' eq ' + '\'' + v + '\'');
                            }
                            else{
                                v = musicShop.formatDateToBase(v);
                                q_arr.push(k.replace('.', '_') + ' eq ' + '\'' + v + '\'');
                            }
                        }
                        else {
                            if (c_obj[k].format === 'number' || c_obj[k].format === 'currency' || c_obj[k].format === 'raw_number') {
                                q_arr.push(k.replace('.', '_') + ' = ' + Number(v));
                            }
                            else{
                                q_arr.push(k.replace('.', '_') + ' = ' + v.value);
                            }
                        }
                    }
                });
                // if (this.searchQuery !== null && this.searchQuery !== '') {
                //     q += '&search=' + encodeURI(this.searchQuery);
                // }
                if (q_arr.length > 0) {
                    q += '&$filter=' + q_arr.join(' and ');
                }
                return q;
            },
            q_fast: function () {
                let q = 'limit=' + this.step + '&offset=' + this.page * this.step;
                if (this.local_filters_additional !== undefined) {
                    _.forEach(this.local_filters_additional, function(v,k){
                        if (!Array.isArray(v)){
                            q += '&'+k+'='+v;
                        }
                        else{
                            _.forEach(v,function (vv, kk) {
                                if (vv instanceof Object){
                                    _.forEach(vv, function(vvv,kkk){
                                        q += '&' + k + '[' + kk + '][' + kkk + ']=' + vvv;
                                        // console.log(vvv, kk)
                                    })
                                }
                                else {
                                    q += '&' + k + '[]=' + vv;
                                }
                            });
                        }
                    });
                }
                if (this.sortCol !== null) {
                    q += '&$orderBy=' + encodeURI(this.sortCol.replace('.', '_') + ' ' + this.sortDir);
                }
                return '?' + q;
            },
            fromRec: function () {
                return _.min([this.page * this.step + 1, this.total]);
            },
            toRec: function () {
                return _.min([this.page * this.step + this.step, this.total]);
            },
            filters_change: function () {
                let q = '';
                _.forEach(this.local_filters, function (v, k) {
                    let type = typeof v;
                    if (v !== null && v !== '') {
                        if(type === 'object') {
                            q += '&' + k + '=' + encodeURI(v.value);
                        } else q += '&' + k + '=' + encodeURI(v);
                    }
                });
                return q;
            },
            filters_change_input: function () {
                let q = '';
                _.forEach(this.local_filters_input, function (v, k) {
                    if (v !== null && v !== '') {
                        q += '&' + k + '=' + encodeURI(v);
                    }
                });
                return q;
            },
            filters_change_add: function () {
                let q = '';
                _.forEach(this.local_filters_additional, function (v, k) {
                    if (v !== null && v !== '') {
                        q += '&' + k + '=' + encodeURI(v);
                    }
                });
                return q;
            },
            refresh_default: function () {
                if (!musicShop.isEmpty(this.settings) && this.settings.refresh_default) {
                    return this.settings.refresh_default;
                }
                else {
                    return false;
                }
            },
            selected_items_count: function() {
                return _.filter(this.items, function(item){
                   return item.is_selected;
                }).length;
            },
            selected_not_all_items: function() {
                return (this.selected_items_count > 0 && this.selected_items_count < this.items.length);
            },
            selectable: function () {
                let is_true = (_.filter(this.columns, function(col){
                    return col.type === 'checkbox';
                }).length > 0);
                if (is_true) {
                    this.step = 9999;
                }
                return is_true;
            },
        },
        watch: {
            loading: _.debounce(function () {
                $('[rel="tooltip"]').tooltip('dispose');
                $('[rel="tooltip"]').tooltip({
                    trigger : 'hover',
                    boundary : "viewport"
                });
            },500),
            // filters: function (val) {
            //     if (this.filters.created_at != null) {
            //         this.filters.created_at = val.created_at.substr(6,4)+'-'+val.created_at.substr(3,2)+'-'+val.created_at.substr(0,2);
            //     }
            // },
            q_path: musicShop.debounceWithId(function(newVal, oldVal) {
                    this.$emit('path',newVal + this.q_input);
                    if(newVal !== '' && newVal !== null && newVal !== oldVal){
                        if(!this.first){
                           if(this.getFromStorageTree()){
                               return 1
                           }
                        }
                        this.refresh();
                    }
                },500,false),
            selected_not_all_items: function(newVal) {
                if (newVal === false) {
                    this.select_all_items = (this.selected_items_count === this.items.length);
                }
            },
            select_all_items: function(newVal) {
                if (newVal === true) {
                    _.forEach(this.items, function(item) {
                        if (this.settings.selCallback !== undefined) {
                            if (!this.settings.selCallback(item)) {
                                return;
                            }
                        }
                        item.is_selected = true;
                    });
                }
                else {
                    _.forEach(this.items, function(item) {
                        item.is_selected = false;
                    });
                }
            },
            page: _.debounce(function () {
                if (this.page !== null) {
                    localStorage.setItem(this.storageIndex('page'), JSON.stringify(this.page));
                }
            }, 500),
            refresh_default: function (newVal) {
                if (newVal) {
                    this.refreshDefault();
                }

            },
            total: function () {
                if(this.total < 8)
                    this.min_table = true;
                else this.min_table = false;
            },
            filters_change: _.debounce(function (newVal, oldVal) {
                if (newVal !== oldVal) {
                    localStorage.setItem(this.storageIndex('local_filters'), JSON.stringify(this.local_filters));
                    this.page = 0;
                }
            }, 500),
            filters_change_input: _.debounce(function (newVal, oldVal) {
                if (newVal !== oldVal) {
                    localStorage.setItem(this.storageIndex('local_filters_input'), JSON.stringify(this.local_filters_input));
                    this.page = 0;
                }
            }, 500),
            filters_change_add: _.debounce(function (newVal, oldVal) {
                if (newVal !== oldVal) {
                    localStorage.setItem(this.storageIndex('local_filters_additional'), JSON.stringify(this.local_filters_additional));
                    this.page = 0;
                }
            }, 500),
            active_tips: function(newVal){
                _.forEach(this.columns, function (item) {
                    if (item.tips !== undefined){
                        item.tips = newVal;
                    }
                });
                localStorage.setItem(('musicShopTable_' + this.storage_key + '_table_active_tips'), this.active_tips);
                if(this.change_active_tips === true)
                    this.change_active_tips = false;
                else this.change_active_tips = true;
            },
            change_active_tips: _.debounce(function(newVal) {
                $('[rel="tooltip"]').tooltip('dispose');
                $('[rel="tooltip"]').tooltip({
                    trigger : 'hover',
                    boundary : "viewport"
                });
            }, 500),
           step: _.debounce(function (newVal, oldVal) {
                if (newVal !== oldVal) {
                    localStorage.setItem(this.storageIndex('step'), JSON.stringify(this.step));
                    this.page = 0;
                    this.page = Math.floor(oldVal * (this.page) / newVal);
                    for (var i = 0; i < newVal - 1; i++) {
                        this.show_details[i] = false;
                    }
                }
            }, 500),
            q_slow: _.debounce(function (newVal, oldVal) {
                if (newVal !== oldVal) {
                    // this.refresh();
                }
            }, 500),
            q_input: function (newVal, oldVal) {
                this.$emit('path',this.q_path + newVal);
            },
            q_fast: _.debounce(function (newVal, oldVal) {
                if (newVal !== oldVal) {
                    // this.refresh();
                }
            }, 500),
            reload: function (newVal, oldVal) {
                if (newVal && oldVal !== newVal) {
                    this.refresh();
                }
            },
            items: function (newVal) {
                //console.log(newVal);
            },
        },
        methods: {
            defaultSearch: function(){
                this.advanced_search = false;
                this.refresh();
            },
            advancedSearch: function(){
                this.advanced_search = true;
                this.refresh();
            },
            clearDatepicker: function (col) {
                this.local_filter_date_to[col.name] = null;
                this.local_filter_date_from[col.name] = null;
                this.local_filters_input[col.name] = null;
                this.local_date_filters_show[col.name] = null;
                this.setPeriod(col);
                this.refresh();
            },
            goodsFilter: function (event) {
                this.filters_additional.jjj = event.jjj;
                this.filters_additional.jjp = event.jjp;
                this.filters_additional.aap = event.aap;
                this.filters_additional.jjv = event.jjv;
            },
            applyParams: function(event){
                this.filters_additional.custom_fields = _.clone(event);
            },
            setPeriod: function (col) {
                if ((this.local_filter_date_to[col.name] === undefined && this.local_filter_date_from[col.name] === undefined)|| (this.local_filter_date_to[col.name] === null && this.local_filter_date_from[col.name] === null)) {
                    return;
                }
                if (this.local_filter_date_to[col.name] === this.local_filter_date_from[col.name]) {
                    let val = this.local_filter_date_to[col.name].replace(/(\d+).(\d+).(\d+)/, '$3-$2-$1');
                    this.$set(this.local_filters_input, col.name, val);
                }
                if(this.local_filter_date_to[col.name] === undefined || this.local_filter_date_to[col.name] === null ) {
                    this.local_date_filters_show[col.name] = this.local_filter_date_from[col.name] + ' =>';
                    let val = '>=' + ':' + this.local_filter_date_from[col.name].replace(/(\d+).(\d+).(\d+)/, '$3-$2-$1');
                    this.$set(this.local_filters_input, col.name, val);
                }
                else if (this.local_filter_date_from[col.name] === undefined || this.local_filter_date_from[col.name] === null) {
                    this.local_date_filters_show[col.name] = '=> ' + this.local_filter_date_to[col.name];
                    let val = '<=' + ':' + this.local_filter_date_to[col.name].replace(/(\d+).(\d+).(\d+)/, '$3-$2-$1');
                    this.$set(this.local_filters_input, col.name, val);
                }else{
                    this.local_date_filters_show[col.name] = this.local_filter_date_from[col.name] + ' - ' + this.local_filter_date_to[col.name];
                    let val = 'btw' + ':' + this.local_filter_date_from[col.name].replace(/(\d+).(\d+).(\d+)/, '$3-$2-$1') + '*' + this.local_filter_date_to[col.name].replace(/(\d+).(\d+).(\d+)/, '$3-$2-$1');
                    this.$set(this.local_filters_input, col.name, val);
                }
               this.saveDateFilters();
               this.refresh();
            },
            compareFilterDates: function (col, last) {
                if (this.local_filter_date_to[col.name] === undefined || this.local_filter_date_from[col.name] === undefined || this.local_filter_date_to[col.name] === null || this.local_filter_date_from[col.name] === null) {
                    return;
                }
                let date_from = new Date(this.local_filter_date_from[col.name].replace(/(\d+).(\d+).(\d+)/, '$3/$2/$1'));
                let date_to = new Date(this.local_filter_date_to[col.name].replace(/(\d+).(\d+).(\d+)/, '$3/$2/$1'));
                if (date_to < date_from) {
                    if (last === 1) {
                        this.local_filter_date_to[col.name] = this.local_filter_date_from[col.name];
                    } else {
                        this.local_filter_date_from[col.name] = this.local_filter_date_to[col.name];
                    }
                }
            },
            saveDateFilters: function(){
                localStorage.setItem(this.storageIndex('local_date_filters_show'), JSON.stringify(this.local_date_filters_show));
                localStorage.setItem(this.storageIndex('local_filter_date_to'), JSON.stringify(this.local_filter_date_to));
                localStorage.setItem(this.storageIndex('local_filter_date_from'), JSON.stringify(this.local_filter_date_from));
                localStorage.setItem(this.storageIndex('local_filters_input'), JSON.stringify(this.local_filters_input));
            },

            getFromStorageTree(){
                var savedFiltersAddJson = localStorage.getItem(this.storageIndex('local_filters_additional'));
                let name;
                _.forEach(this.columns, function (item) {
                    if (item.search_tree){
                        name=item.search_tree;
                    }
                });
                if (savedFiltersAddJson !== null) {
                    _.forEach(this.data, function (i) {
                        i.is_selected=false;
                    });
                    var arr = JSON.parse(savedFiltersAddJson);
                    _.forEach(this.data, function (i) {
                        _.forEach(arr[name], function (item) {
                            if(i.id===item){
                                i.is_selected=true;
                            }
                        });
                    });
                }
                this.first = true;
                if (savedFiltersAddJson !== null) {
                    var arrr = [];
                    _.forEach(arr[name], function (item) {
                        arrr.push(item);
                    });
                    this.local_filters_additional[name] = arrr;
                    if (this.local_filters_additional[name].length > 0)
                        return 1;
                }
                return false;
            },
            selectedTree(event){
                    var name;
                    var field;
                    _.forEach(this.columns, function (item) {
                        if (item.search_tree){
                            name=item.search_tree;
                            field=item.field;
                        }
                    });
                    var arr=[];
                    _.forEach(event,function (item) {
                        arr.push(item[field]);
                    });
                    this.local_filters_additional[name]=arr;
            },
            tree_data : function (item){
                var _this=this;
                this.$http.get(item.src)
                    .then(function (respond) {
                        _this.data = respond.data.groups;
                        var savedFiltersAddJson = localStorage.getItem(_this.storageIndex('local_filters_additional'));
                        let name;
                        _.forEach(_this.columns, function (item) {
                            if (item.search_tree){
                                name=item.search_tree;
                            }
                        });
                        if (savedFiltersAddJson !== null) {
                            _.forEach(_this.data, function (i) {
                                i.is_selected=false;
                            });

                            let arr = JSON.parse(savedFiltersAddJson);
                            _.forEach(_this.data, function (i) {
                                _.forEach(arr[name], function (item) {
                                    if(i.id===item){
                                        i.is_selected=true;
                                    }
                                });
                            });
                        }
                        let cc_obj = _this.columns_as_object;
                        _.map(_this.columns, function (item) {
                            cc_obj[item.name] = item;
                        });
                    });
            },
            getItemKey: function (item) {
                return item[this.key_field_name]
            },
            storageIndex: function (name) {
                return 'musicShopTable_' + this.id + '_' + name;
            },
            gbNumber: function (val) {
                return val;
            },
            _range: function (f, l) {
                return _.range(f, l);
            },
            displayCell: function (col, item) {
                if (col.display_callback !== undefined) {
                    return col.display_callback(col, item);
                }
                if (col.type === 'button') {
                    return;
                }
                if (col.value !== undefined) {
                    return col.value;
                }
                if (col.dict_values !== undefined) {
                    let dict = this.dicts[col.dict_values];
                    return dict[item[col.name]];
                }

                let value = col.display !== undefined ? _.get(item, col.display) : _.get(item, col.name);
                value = (value === null ? '' : value);

                if (col.format === 'date') {
                    value = value ? musicShop.formatDate(value) : '';
                } else if (col.format === 'date_time') {
                    value = musicShop.formatDateTime(value);
                } else if (col.format === 'date_or_time') {
                    value = messageDate(value);
                } else if (col.format === 'datetimefull') {
                    value = gbDateTimeFull(value);
                } else if (col.format === 'bool') {
                    value = (gbNumber(value) == 0) ? 'No' : 'Yes';
                } else if (col.value === false) {
                    value = col.name;
                }
                if (col.format == 'text') {
                    if ('display_trim' in col) {
                        value = value.substring(0, col.display_trim);
                    }
                }
                if(col.format == 'phone') {
                    if (value !== "") {
                        value = value.substr(value.length - 10);
                        value = '+7 '+'('+value.substr(0,3)+') '+value.substr(3,3)+'-'+value.substr(6,2)+'-'+value.substr(8,2);
                    }
                }
                value = col.format === 'number' ? musicShop.moneyFormat(value, true) : value;
                value = col.format === 'raw_number' ? Number(value) : value;
                value = col.format === 'currency' ? musicShop.moneyFormat(value) : value;
                if (col.format === 'permission') {
                    value = "Права";
                }
                if (col.format === 'contacts_ref') {
                    value = "Контакты";
                }
                return value;
            },
            cellDetailsStyle: function (col, item) {
                let styleObj = {
                    'red': (col.rule === '!0' && item[col.name] != 0 && item[col.name] != '.00') || (col.rule === '<0' && item[col.name] < 0) || (col.rule === '>70' && parseInt(item[col.name]) > 70),
                    'text-right': col.format == 'currency' || col.align === 'right',
                    'text-warning': item[col.highlight] === true
                };
                console.log(styleObj, item[col.name]);
                if (col.style_callback !== undefined) {
                    styleObj = _.merge(styleObj, col.style_callback(col, item));
                }
                switch (col.breakpoints) {
                    case 'lg':
                        styleObj['d-lg-none'] = true;
                        break;
                    case 'md':
                        styleObj['d-md-none'] = true;
                        break;
                    case 'sm':
                        styleObj['d-sm-none'] = true;
                        break;
                    default: styleObj['d-none'] = true;
                }
                return styleObj;
            },
            cellStyle: function (col, item) {
                let styleObj = {
                    'red': (col.rule === '!0' && item[col.name] != 0 && item[col.name] != '.00') || (col.rule === '<0' && item[col.name] < 0) || (col.rule === '>70' && parseInt(item[col.name]) > 70),
                    'font-weight-bold': (col.bold === true),
                    'text-right': col.format == 'currency' || col.align === 'right',
                    'text-warning': item[col.highlight] === true
                };
                if (col.style_callback !== undefined) {
                    styleObj = _.merge(styleObj, col.style_callback(col, item));
                }
                switch (col.breakpoints) {
                    case 'lg':
                        styleObj['d-lg-table-cell'] = true;
                        styleObj['d-none'] = true;
                        break;
                    case 'md':
                        styleObj['d-md-table-cell'] = true;
                        styleObj['d-none'] = true;
                        break;
                    case 'sm':
                        styleObj['d-sm-table-cell'] = true;
                        styleObj['d-none'] = true;
                        break;
                }
                return styleObj;
            },
            ukDate: function (d) {
                return gbDate(d);
            },
            refresh: function () {
                document.activeElement.blur();
                let $this = this;
                this.loading = true;
                let items = [];
                this.refreshing = true;
                if(this.with_header)
                    this.$http.get(this.data_src + this.q_fast + this.q_slow + this.q_input, {headers: {'Authorization' : 'Bearer '+document.head.querySelector('meta[name="bearer"]').content}})
                        .then(function(response) {
                            items = response.data[$this.data_prop_name];
                            items = _.map(items, function (item) {
                                return _.merge(item, {vm_show_details: false, is_selected: false});
                            });
                            $this.items = items;
                            $this.total = response.data.total;
                            $this.$emit('loaded', response.data);
                            $this.loading = false;
                            $this.select_all_items = false;
                            $this.settings.refresh_indicator = false;
                            $this.refreshing = false;
                        }, function (response) {
                            $this.loading = false;
                            if(response.status===401)
                                window.location.href = '/login';
                            musicShop.errorDialogFromResponse(response);
                            $this.settings.refresh_indicator = false;
                        });
                else
                this.$http.get(this.data_src + this.q_fast + this.q_slow + this.q_input)
                    .then(function(response) {
                        items = response.data[$this.data_prop_name];
                        items = _.map(items, function (item) {
                            return _.merge(item, {vm_show_details: false, is_selected: false});
                        });
                        $this.items = items;
                        $this.total = response.data.total;
                        $this.$emit('loaded', response.data);
                        $this.loading = false;
                        $this.select_all_items = false;
                        $this.settings.refresh_indicator = false;
                        this.refresh_indicator = this.settings.refresh_indicator;
                        $this.refreshing = false;
                    }, function (response) {
                        $this.loading = false;
                        if(response.status===401)
                            window.location.href = '/login';
                        musicShop.errorDialogFromResponse(response);
                        $this.settings.refresh_indicator = false;
                        this.refresh_indicator = this.settings.refresh_indicator;
                    });

            },
            showDetails: function (item) {
                item.vm_show_details = !item.vm_show_details;
            },
            checkPageFilters: function () {
                let _this =this;
                let columns_names = this.columns.map(column => column.name);
                // Проверка на несуществующие фильтры
                if (columns_names.indexOf(localStorage.getItem(this.storageIndex('sortCol'))) === -1) {
                    localStorage.removeItem(this.storageIndex('sortCol'));
                    localStorage.removeItem(this.storageIndex('sortDir'));
                }
                let filters = [
                    {
                        filter_name: 'local_filters_input',
                        array_to_compare: columns_names
                    }
                ];
                if(this.local_filters_additional){
                    filters.push(
                        {
                            filter_name: 'local_filters_additional',
                            array_to_compare: Object.getOwnPropertyNames(this.local_filters_additional)
                        }
                    )
                }
                filters.forEach( function (filter) {
                    let checked_filter = JSON.parse(localStorage.getItem(_this.storageIndex(filter.filter_name)));
                    if(checked_filter !== null && checked_filter !== undefined){
                        for (let key in checked_filter) {
                            if (filter.array_to_compare.indexOf(key) === -1) {
                                delete checked_filter[key];
                            }
                        }
                        localStorage.setItem(_this.storageIndex(filter.filter_name), JSON.stringify(checked_filter));
                    }
                });
            },
            refreshDefault: function () {
                var clone_filters_change_input = this.filters_change_input;
                var clone_filters_change = this.filters_change;
                var clone_filters_change_add = this.filters_change_add;
                var clone_search = this.searchQuery;
                let $filters_d = this.default_filters;
                let $filters = this.local_filters;
                let $filters_in = this.local_filters_input;
                _.forEach($filters, function (v, k) {
                    if (v !== null && v !== '') {
                        if ($filters_d === undefined || $filters_d[k] === undefined) {
                            $filters[k] = null;
                        }
                        else {
                            $filters[k] = $filters_d[k];
                        }
                    }
                });
                _.forEach($filters_in, function (v, k) {
                    if (v !== null && v !== '') {
                        if ($filters_d === undefined || $filters_d[k] === undefined) {
                            $filters_in[k] = null;
                        }
                        else {
                            $filters_in[k] = $filters_d[k];
                        }
                    }
                });
                let name;
                _.forEach(this.columns, function (item) {
                    if (item.search_tree){
                        name=item.search_tree;
                    }
                });
                this.local_date_filters_show ={};
                this.local_filter_date_from ={};
                this.local_filter_date_to ={};
                this.saveDateFilters();
                let date_from = new Date(Date.now() - 1000*60*60*24*30);
                let date_to = new Date();
                this.local_filters_additional['name']='month';
                this.local_filters_additional['date_from']=date_from.getFullYear() + '-' + String(date_from.getMonth()+1).replace(/^(.)$/, "0$1") +'-'+ String(date_from.getDate()).replace(/^(.)$/, "0$1");
                this.local_filters_additional['date_to']=date_to.getFullYear() + '-' + String(date_to.getMonth()+1).replace(/^(.)$/, "0$1") +'-'+ String(date_to.getDate()).replace(/^(.)$/, "0$1");
                // this.local_filters_additional['jjv']=1;
                // this.local_filters_additional['jjp']=0;
                // this.local_filters_additional['aap']=0;
                // this.local_filters_additional['jjj']=0;
                if (this.local_filters_additional['custom_fields']){
                    this.local_filters_additional['custom_fields'] = [];
                }
                this.settings.refresh_indicator = true;
                if(this.settings.refresh_indicator !== false){
                    this.refresh_indicator = this.settings.refresh_indicator;
                }
                this.page = 0;
                this.sortCol = this.default_sort_col;
                this.sortDir = this.default_sort_dir;
                this.loading = true;
                this.searchQuery = clone_search = null;
                _.forEach(this.data, function (item) {
                    item.is_selected=false;
                });
                if (this.settings.refresh_default_callback !== undefined) {
                    this.settings.refresh_default_callback();
                }

                if((clone_filters_change_input!=='' || clone_filters_change_input!==null)
                    && (clone_filters_change==='' || clone_filters_change===null)
                    && (clone_search==='' || clone_search===null)) {
                        this.refresh();
                }
            },
            switchSort: function (col) {
                if (col.sortable !== false) {
                    if (col.type === 'buttons' || col.type === 'button' || col.format === 'toggle') {
                        return;
                    }
                    if (this.sortCol === col.name) {
                        this.sortDir = this.sortDir === 'desc' ? 'asc' : 'desc';
                    } else {
                        this.sortDir = 'desc';
                    }
                    this.sortCol = col.name;
                    localStorage.setItem(this.storageIndex('sortDir'), this.sortDir);
                    localStorage.setItem(this.storageIndex('sortCol'), this.sortCol);
                }
            },
            createHref: function (col, item) {
                if (item[col.href] !== undefined) {
                    return item[col.href].replace('api/', 'common/');
                }
                if (!this.showByRule(col,item)){
                    return '#';
                }
                var arr = musicShop.explode('/', col.href);
                var q = '';
                var params = '';
                var error = false;
                var _self = this;
                _.forEach(arr, function (v, k) {
                    if (v != '') {
                        params = v.match(/\{(.+)\}/i);
                        if (params !== null) {
                            arr[k] = v.replace(/\{(.+)\}/i, musicShop.getObjectVal(item, params[1]));
                            if (arr[k] == 'null') {
                                error = true;
                            }
                        }
                    }
                });
                var result = arr.length === 0 ? '' : arr.join('/');
                return error ? '#' : result;
            },
            iconToggle: function (col, item) {
                return item[col.name] ? col.icon_true : col.icon_false;
            },
            showByRule: function (col, item) {
                if (col.show === undefined) {
                    return true;
                }
                var result = true;
                var rules = musicShop.explode(' and ', col.show);
                _.forEach(rules, function (rl) {
                    if (result) {
                        var rule = musicShop.explode(' ', rl);
                        if (rule.length === 3) {
                            if (rule[2] === 'null') rule[2] = null;
                            if (rule[2] === 'true') rule[2] = true;
                            if (rule[2] === 'false') rule[2] = false;
                            switch (rule[1]) {
                                case '>':
                                    result = item[rule[0]] > rule[2];
                                    break;
                                case '<':
                                    result = item[rule[0]] < rule[2];
                                    break;
                                case '=':
                                    result = item[rule[0]] == rule[2];
                                    break;
                                case '!=':
                                    result = item[rule[0]] != rule[2];
                                    break;
                            }
                        }
                    }
                });
                return result;
            },
            buttonCallback: function ($event, col, item) {
                $event.preventDefault();
                if (this.settings !== undefined && this.settings[col.id + '_callback'] !== undefined) {
                    this.$emit(col.id, item);
                    return false;
                    // let func = this.settings[col.id + '_callback'];
                    // return func(col, item);
                }
            },
            toggleCallback: function (col, item) {
                if (this.settings !== undefined && this.settings[col.id + '_callback'] !== undefined) {
                    let func = this.settings[col.id + '_callback'];
                    return func(col, item);
                }
            },
            show_modal: function () {
                $('#showModal').modal('show');
            },
            onResize: function () {
                this.window_width = $(document).width();
             },
            getObjectVal: function (item, params) {
                musicShop.getObjectVal(item, params);
            },
            toPageUp: function () {
                window.scrollTo(0,0);
            },
            onclick: function (event) {
                if (typeof(event.date_from) !== 'string') {
                    this.filters_additional.date_from = event.date_from.getFullYear() + '-' + String(event.date_from.getMonth() + 1).replace(/^(.)$/, "0$1") + '-' + String(event.date_from.getDate()).replace(/^(.)$/, "0$1");
                }
                else {
                    this.filters_additional.date_from = event.date_from.replace(/(\d+).(\d+).(\d+)/, '$3-$2-$1');
                }
                if (typeof(event.date_to) !== 'string') {
                    this.filters_additional.date_to = event.date_to.getFullYear() + '-' + String(event.date_to.getMonth() + 1).replace(/^(.)$/, "0$1") + '-' + String(event.date_to.getDate()).replace(/^(.)$/, "0$1");
                }
                else {
                    this.filters_additional.date_to = event.date_to.replace(/(\d+).(\d+).(\d+)/, '$3-$2-$1');
                }
                this.filters_additional.name = event.name;
                let data = {
                    'name': this.filters_additional.name,
                };
            },
            getHeaderClass: function (col) {
                let header_class = {
                    sorting: (this.sortCol !== col.name && col.sortable !== false),
                    'sorting_asc': this.sortCol === col.name && this.sortDir == 'asc',
                    'sorting_desc': this.sortCol === col.name && this.sortDir == 'desc',
                    active: this.sortCol === col.name,
                    'text-right': col.align === 'right' || col.format === 'currency',
                    'font-weight-bold': (col.bold === true)
                };
                if (col.type === 'buttons' || col.type === 'button' || col.format === 'toggle' || col.type === 'checkbox') {
                    header_class = { none: true };
                }
                switch (col.breakpoints) {
                    case 'lg':
                        header_class['d-none'] = true;
                        header_class['d-lg-table-cell'] = true;
                        break;
                    case 'md':
                        header_class['d-none'] = true;
                        header_class['d-md-table-cell'] = true;
                        break;
                    case 'sm':
                        header_class['d-none'] = true;
                        header_class['d-sm-table-cell'] = true;
                        break;
                }

                return header_class;
            }
        }
    }
</script>

<style>
    .vtable .smart-form .checkbox input.part+i:after {
        content: '\f068';
        opacity: 1;
    }
    .vtable thead th {
        vertical-align: middle;
    }
    .vtable thead th.sorting:after {
        content:"\f0dc";
        cursor: pointer;
        font-family: FontAwesome;
        float: right;
        position: relative;
        margin-right: -12px;
    }

    .vtable thead th.sorting_asc:after {
        content:"\f0de";
        cursor: pointer;
        font-family: FontAwesome;
        float: right;
        position: relative;
        margin-right: -12px;
        margin-top: 0;
    }

    .vtable thead th.sorting_desc:after {
        content:"\f0dd";
        cursor: pointer;
        font-family: FontAwesome;
        float: right;
        position: relative;
        margin-right: -12px;
        margin-top: -3px;
    }

    .vtable thead tr {
        font-weight: bold;
    }

    .vtable .pagination {
        float: right;
    }

    .vtable .pagination span {
        border: 0;
        box-shadow: none;
        -webkit-box-shadow: none;
    }

    .vtable table.fixed-height {
        border-left: none;
        border-right: none;
        border-top: none;
        width: 100%;
        margin: 0;
        padding: 0;
    }

    .vtable div.div-fixed-height {
        min-height: 308px;
        border: 1px solid #ccc;
        margin-bottom: 14px;
    }

    td.vtable-plus {
        width: 1px;
    }

    td.vtable-plus i{
        cursor: pointer;
    }


    .musicShop-table-loading span {
        border: 1px solid;
        padding: 23px;
    }

    .musicShop-table-loading i.fa-spin {
        font-size: 18px;
    }

    .musicShop-table .btn-group {
        display: inline-block !important;
        margin: 0 3px !important;
    }

    .musicShop-table tr th {
        padding-right: 17px!important;
    }

    .musicShop-table {
        margin-bottom: 0;
    }

    .musicShop-table .red{
        color: red;
    }

    .musicShop-table .table td a {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0px;
        display: block;
    }

    .musicShop-table .table > tbody > tr > td {
        max-width: 100px;
    }

    .musicShop-table .table td span {
        white-space: nowrap;
        overflow: hidden;
        padding: 0px;
        text-overflow: ellipsis;
        margin: 0px;
        display: block;
    }
    .musicShop-table .badge {
        min-height: 14px;
    }

    td.vtable-plus {
        width: 1%!important;
    }

    .smart-style-1 .musicShop-table .pagination>.active>a,
    .smart-style-1 .musicShop-table .pagination>.active>a:focus,
    .smart-style-1 .musicShop-table .pagination>.active>a:hover,
    .smart-style-1 .musicShop-table .pagination>.active>span,
    .smart-style-1 .musicShop-table .pagination>.active>span:focus,
    .smart-style-1 .musicShop-table .pagination>.active>span:hover {
        background-color: #337ab7;
        border-color: #337ab7;
    }

    .musicShop-table .table>tbody+tbody {
        border-top: none;
    }
    .musicShop-table .table-striped>tbody:nth-of-type(odd)>tr {
        background: #f9f9f9;
    }

    .musicShop-table .table-striped>tbody:nth-of-type(even)>tr {
        background: white;
    }

    .musicShop-table .table-hover>tbody>tr:hover {
        background: #e5e5e5;
    }

    .tree-children > li > span{
        display: inline !important;
    }
    .tree-children > div > button{
        height :28px;
        font-size: 0.95em;
        line-height: 0;
    }
    .tree-children > div > button > i{
        line-height: 0;
    }


/*выпадающие списки*/
    .dropdown-search-table{
        background: #fff;
        min-width: 60px;
    }

    .dropdown-search-table .dropdown-toggle input {

        height: 17px !important;
    }

    .dropdown-search-table .dropdown-toggle i{
        bottom: 0;
        top:6px;
        background: #fff;
    }
    .dropdown-search-table .dropdown-toggle button{
        bottom: 0;
        top:3px;
        background: #fff;
    }
    .dropdown-search-table .dropdown-toggle .selected-tag{
        width:55%;
        text-align: left;
        padding: 0 2px;
    }
    .bootstrap-datetimepicker-widget.dropdown-menu.usetwentyfour.bottom td span {
        display: inline-block;
    }
    .bootstrap-datetimepicker-widget.dropdown-menu.usetwentyfour.bottom tr {
        background-color: white;
    }
    .bootstrap-datetimepicker-widget.dropdown-menu.usetwentyfour.bottom th,
    .bootstrap-datetimepicker-widget.dropdown-menu.usetwentyfour.bottom td
    {
        border: 0;
    }
    .form-control.date-boot-musicShop{
        padding: 0.2rem;
        text-align: right;
    }
    .bootstrap-datetimepicker-widget.dropdown-menu {
        display: table !important;
        width: auto !important;
    }
</style>
