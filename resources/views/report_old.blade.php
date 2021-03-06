@extends('layouts.app')

@section('content')
    <div class="layout">
        <div class="container">
            <div id="orders_list" class="no-print">
                <section class="filter_status">
                    <div class="orders_list_wrapper">
                        {{--<div class="filter_text">Filter by status:</div>--}}
                        <div>
                            <form method="get" action="{{url('report')}}">
                                {{--{{csrf_field()}}--}}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="table_label">Provider</label>
                                            <div class="select_wrapper">
                                                <select name="provider" class="block_btn_30 modal_input">
                                                    <option value=""></option>
                                                    <option value="1" selected>Vodafone</option>
                                                </select>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="table_label">Phone Number</label>
                                            <input type="text" class="block_btn_30 modal_input_without_icon" name="number" value="{{ (isset($_GET['number'])) ? $_GET['number'] : '' }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="table_label">Level</label>
                                            <div class="select_wrapper">
                                                <select name="level" class="block_btn_30 modal_input" onchange="filterLevel(this.value)">
                                                    <option value="All">All</option>
                                                    @php if(isset ($_GET['level']))
                                                    $levelCur = $_GET['level'];
                                                else
                                                    $levelCur =  Auth::user()->level;
                                                    @endphp
                                                    @foreach($level as $item)
                                                    <option value="{{$item}}" {{ ($levelCur == $item) ? 'selected' : '' }}>{{$item}}</option>
                                                    @endforeach
                                                </select>
                                                <i class="input_icon_small icon-level"></i>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <!--<div class="departure"><i class="icon-landing"></i> From</div>-->
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="table_label">From</label>
                                                    <div class="wrap_date">
                                                        <div class="input-group date flight_dates" data-provide="datepicker">
                                                            <div class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </div>
                                                            <input type="text" name="from" class="inline_block_btn departure_date vd_date_required" data-date-format="DD/MM/YYYY" value="{{ (isset($_GET['from'])) ? $_GET['from'] : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <!--<div class="departure" ><i class="icon-landing"></i> To</div>-->
                                                    <label class="table_label">To</label>
                                                    <div class="wrap_date">
                                                        <div class="input-group date flight_dates" data-provide="datepicker">
                                                            <div class="input-group-addon">
                                                                <span class="glyphicon glyphicon-calendar"></span>
                                                            </div>
                                                            <input type="text" name="to" class="inline_block_btn departure_date vd_date_required" data-date-format="DD/MM/YYYY" value="{{ (isset($_GET['to'])) ? $_GET['to'] : '' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                        </div>


                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="table_label">Username</label>
                                            <div class="select_wrapper" id="user-select">
                                                <select name="username" class="block_btn_30 modal_input username">
                                                    <option value="">All</option>
                                                    @php if(isset ($_GET['username']))
                                                    $id = $_GET['username'];
                                                else
                                                    $id =  Auth::user()->id;
                                                    @endphp
                                                    @foreach($users as $user)
                                                    <option value="{{$user->id}}" {{ ($id == $user->id) ? 'selected' : '' }}>{{$user->login}}</option>
                                                    @endforeach
                                                </select>
                                                <i class="input_icon_small icon-username"></i>
                                            </div>
                                        </div>




                                    </div>
                                </div>

                            <!--<div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="table_label">Phone Number</label>
                                            <input type="text" class="block_btn_30 modal_input_without_icon" name="number" value="{{ (isset($_GET['number'])) ? $_GET['number'] : '' }}">
                                        </div>
                                    </div>
                                </div>-->

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <div class="report_buttons">
                                                {{--<input type="reset" class="btn btn-warning" value="Clear">--}}
                                                <input type="submit" name="report" class="btn btn-info" value="Report">
                                                <input type="submit" name="export" class="btn btn-info" value="Export">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </div>
                        <div class="filter_buttons">
{{--                            <a class="filter_option  {{ (Request::is('home')) ? 'blue' : 'light_blue' }}" href="{{url('/home')}}">
                                <i class="icon-company_status"></i>All ({{$counts['All']}})
                            </a>
                            <a class="filter_option  {{ (Request::is('filter-orderlist/active')) ? 'blue' : 'light_blue' }}" href="{{url('filter-orderlist/active')}}">
                                <span class="status active blue" ></span>active ({{$counts['active']}})
                            </a>
                            <a class="filter_option  {{ (Request::is('filter-orderlist/pending')) ? 'blue' : 'light_blue' }}" href="{{url('filter-orderlist/pending')}}">
                                <span class="status inactive"></span> pending ({{$counts['pending']}})
                            </a>
                            <a class="filter_option  {{ (Request::is('filter-orderlist/waiting')) ? 'blue' : 'light_blue' }} last" href="{{url('filter-orderlist/waiting')}}">
                                        <span class="status waiting"></span> waiting ({{$counts['waiting']}}) </a>
                                    </a>--}}

                            <div class="search_management_option">
                                <form action="{{url('/search/report')}}" method="get" class="search_form_option">
                                    <input type="text" class="block_btn_30 search_input" name="query" value="{{ (isset($_GET['query'])) ? $_GET['query'] : '' }}" placeholder="Search">
                                    {{csrf_field()}}
                                    <button type="submit" class="search_button low"><i class="icon-search"></i></button>
                                </form>
                                {{--<a href="{{url('/exportorders')}}" class="export_user"><i class="icon-export"></i>Export</a>--}}
                                {{--<a href="#" class="add_new_btn" data-toggle="modal" data-target="#modal_new_order"><i class="icon-new_order"></i>New Order</a>--}}
                            </div>
                        </div>

                        <div class="clear"></div>
                    </div>
                </section>
                <section class="section_table">
                    <!--rwd-table responsive_table table-->
                    <div class="row">
                        <div class="col-md-12">

                            <div id="wrap_report_table">
                                <table class="rwd-table responsive_table table" data-toggle="table" data-page="order">
                                    <thead>
                                        <tr>
                                            {{--<th data-field="id">ID</th>--}}
                                            <th data-field="Phone" data-sortable="true">Phone</th>
                                            <th data-field="SIM Number">SIM Number</th>
                                            <th data-field="provider" data-sortable="true" data-th="Provider">Provider</th>
                                            <th data-field="from" data-sortable="true" data-th="From">From</th>
                                            <th data-field="to" data-sortable="true" data-th="To">To</th>
                                            <th data-field="dealer" data-sortable="true" data-th="Dealer">Dealer </th>

                                            <th data-th="Reference N">Reference #</th>
                                            {{--<th data-th="action">Action</th>
                                            <th data-field="status" data-sortable="true" data-th="Status">Status</th>--}}
                                            <th>Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($ordersArray as $order)
                                    <tr data-uniqueid="{{$order['id']}}">
                                        {{--<td>{{$order['id']}}</td>--}}
                                        <td class="rwd-td0 table_id_cell editable_cell" data-th="Phone" data-cell-id="{{$order['id']}}">
                                            @if($order['phone_id']==0)
                                                <a id= "{{$order['id']}}" href="#" >No Number</a>
                                            @else
                                            {{$order['phone_id']}}
                                            @endif
                                        </td>
                                        <td class="rwd-td1 editable_cell" data-th="SIM Number" data-form="#modal_view_order">
                                            <a href="#modal_view_order" role="button" class="link">
                                                {{$order['sim_id']}}
                                            </a>
                                        </td>
                                        <td class="rwd-td2 editable_cell align_order" data-th="Provider" data-target="#modal_view_order" data-form="#modal_view_order">
                                            <a href="#modal_view_order" role="button" class="link">
                                                {{$order['provider']}}
                                            </a>
                                        </td>
                                        <td class="rwd-td3 table_time_cell_large from align_order" data-field="From" data-th="From"  data-target="#modal_view_order" data-form="#modal_view_order">
                                            <a href="#modal_view_order" role="button" class="link">
                                                {{$order['landing']}}
                                            </a>
                                        </td>
                                        <td class="rwd-td4 table_time_cell_large to align_order" data-field="To" data-th="To" data-toggle="modal"  data-target="#modal_edit_order" data-form="#modal_edit_order">
                                            {{$order['departure']}}
                                        </td>
                                        <td class="rwd-td5 editable_cell align_order" data-field="Created by" data-th="Created by" data-target="#modal_view_order" data-form="#modal_view_order">
                                            <a href="#modal_view_order" role="button" class="link">
                                                {{$order['created_by']}}
                                            </a>
                                        </td>
                                        <td class="rwd-td6 {{ ($order['reference_number'] != '') ? 'ref_number' : '' }} align_order"  data-field="Reference Number" data-th="Reference Number">
                                            @if ($order['reference_number'] != '')
                                            <span class="hint_text" data-toggle="tooltip"  data-trigger="click" data-original-title="{{$order['reference_number']}}">
                                                {{substr($order['reference_number'], 0, 9)}}
                                            <span class="hint">i</span>
                                            </span>
                                            @endif
                                        </td>
                                       {{-- <td class="rwd-td7 table_action_cell_large" data-field="Action" data-th="Action">
                                            <span class="table_icon call_edit {{ ($order['status'] == 'active') ? 'disable' : '' }}" data-toggle="modal" data-row-id="{{$order['id']}}" data-target="#modal_edit_order" data-form="#modal_edit_order">
                                                <i class="icon-edit"></i>
                                            </span>
                                            <span class="table_icon print" data-toggle="modal" data-target="#modal_print_order" data-form="modal_print_order">
                                                <i class="icon-print"></i>
                                            </span>
                                            <span class="table_icon call_mail"  data-toggle="modal" data-target="#modal_order_email" data-row-id="{{$order['id']}}">
                                                <i class="icon-email"></i>
                                            </span>
                                        </td>
                                        <td class="rwd-td8" data-field="Status" data-th="Status">
                                            <span class="table_status_text not_used">{{$order['status']}}</span>
                                        </td>--}}
                                        <td class="rwd-td9 table_status_cell" data-th="Remove">
                                            {{$order['duration']}} day(s)
                                        </td>
                                    </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                                {{--<p>{{$total}}</p>--}}
                    {{$ordersArray->links()}}
                            </div><!--#wrap_orders_table-->
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>


    @include('ordermodal.view')


    <!--Email Modal-->
    <div class="modal fade" id="modal_order_email" tabindex="-1" role="dialog" aria-labelledby="modal_order_email">
        <div class="modal-dialog vdf_modal" role="document">
            <div class="modal-content vdf_modal_content">
                <div class="modal-header vdf_modal_header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="vdf_modal_sub_header">
                        <div class="col-md-7">
                            <h3>Mail message: Order <span class="mail_order">#124875</span></h3>
                        </div>
                        <div class="col-md-5">
                            <div class="point_to">
                                <span class="point_to_text">To</span>
                                <div class="relative point_to_input">
                                    <form action="/" class="vd_form">
                                    <input type="text" name="email" id="email" class="block_btn_30 modal_input vd_email vd_required" value="" />
                                        </form>
                                    <i class="input_icon icon-special"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <form action="/" class="form-horizontal vd_form">
                    <div class="modal-body vdf_modal_body">
                        <div class="form-group">
                            <div class="col-md-7">
                                <div class="form-group">
                                    <div class="col-md-8">
                                        <label class="table_label">Phone Number </label>
                                        <div class="relative">
                                            <input type="text" name="number" class="block_btn_30 modal_input vd_number phone" value="" disabled/>
                                            <i class="input_icon icon-phone_number"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="single_package">
                                            <label class="table_label">Selected Package </label>

                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="table_label">Active Period</label>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="departure">From</div>
                                        <div class="email_date_time from"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="departure"> To</div>
                                        <div class="email_date_time to"></div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <label class="table_label">Description</label>
                                        <textarea name="print_xtx_nnm" id="send_text" class="modal_textarea"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <img src="/img/print_image.jpg" class="print_image" alt="print image">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer vdf_modal_footer">
                        <span class="c_support">Customer service: +44 2031501573 Ext. 1</span>
                        <a href="#" class="inline_block_btn light_gray_btn close vd_form_reset" data-dismiss="modal" aria-label="Close">Cancel</a>
                        <a href="#" class="inline_block_btn light_green_btn vd_form_submit" id="send-order">Send</a>

                        <span class="success_response"></span>
                        <span class="error_response"></span>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!--end of Email Modal-->




@endsection
