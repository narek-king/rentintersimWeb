@extends('layouts.admin')

@section('dashboard')
    <div id="number_management">
        <section class="filter_status">
            <div class="number_management_wrapper">
                <div class="filter_text">Filter by status:</div>
                <div class="filter_buttons">
                    <a class="filter_option {{ (Request::is('number')) ? 'blue' : 'light_blue' }} " href="{{url('/number')}}">
                        <i class="icon-company_status"></i> All ({{$counts['All']}})
                    </a>
                    <a class="filter_option {{ (Request::is('filter-numberlist/active')) ? 'blue' : 'light_blue' }}" href="{{url('/filter-numberlist/active')}}">
                        <span class="status active"></span> active ({{$counts['active']}})
                    </a>
                        <a class="filter_option {{ (Request::is('filter-numberlist/pending')) ? 'blue' : 'light_blue' }}" href="{{url('/filter-numberlist/pending')}}">
                        <span class="status inactive"></span> inactive ({{$counts['pending']}})
                    </a>
                    <a class="filter_option {{ (Request::is('filter-numberlist/not in use')) ? 'blue' : 'light_blue' }} last" href="{{url('/filter-numberlist/not in use')}}">
                        <span class="status disabled"></span> not in use ({{$counts['not in use']}})
                    </a>
                    <div class="search_management_option">
                        <form action="/" class="search_form_option">
                            <input type="text" class="block_btn_30 search_input" value="search">
                            <button type="submit" class="search_button"><i class="icon-search"></i></button>
                        </form>
                        <a href="#" class="export_user"><i class="icon-export"></i>Export</a>
                        <a href="#" class="add_new_btn" data-toggle="modal" data-target="#modal_add_number"><i class="icon-add_number"></i>Add number</a>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </section>
        <section class="section_table">
            <div class="row">
                <div class="col-md-12">
                    <table class="rwd-table responsive_table table" data-toggle="table">
                        <thead>
                        <tr>
                            <th class="table_id_cell" data-field="id" data-sortable="true" data-th="Id">Id</th>
                            <th data-field="phone number" data-sortable="true" data-th="Phone Number">Phone Number</th>
                            <th>SIM Number</th>
                            <th data-field="provider" data-sortable="true" data-th="Provider">Provider </th>
                            <th data-field="type" data-sortable="true" data-th="Type">Type</th>
                            <th>Action </th>
                            <th class="w_160_status" data-field="status" data-sortable="true" data-th="Status">Status </th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($phonesArray as $number)
                            <tr>
                                <td class="rwd-td0 table_id_cell" data-toggle="modal" data-th="Id">{{$number['id']}}</td>
                                <td class="rwd-td1" data-toggle="modal" data-th="Phone Number">{{$number['phone']}}</td>
                                <td class="rwd-td2" data-toggle="modal" data-th="SIM Number">{{$number['current_sim_id']}}</td>
                                <td class="rwd-td3" data-toggle="modal" data-th="Provider">{{$number['provider_id']}}</td>
                                <td class="rwd-td4" data-toggle="modal" data-th="Type">{{$number['package_id']}}</td>
                                <td class="rwd-td5 table_action_cell" data-th="Action">
                                    <span class="table_icon"  data-toggle="modal" data-target="#modal_edit_number"><i class="icon-edit"></i></span>
                                    <span class="table_icon {{ (($number['is_special']) ? 'active' : '') }}"><i class="icon-special"></i></span>
                                </td>
                                <td class="rwd-td6 w_160_status" data-th="Status">
                                    <span class="table_status_text not_used">{{$number['state']}}</span>
                                    <div class="vdf_radio">
                                        <div class="toggle_container {{ ((!$number['is_active']) ? 'disabled' : '') }}">
                                            <label>
                                                <input type="radio" name="toggle" value="1"><span ></span>
                                            </label>
                                            <label>
                                                <input type="radio" name="toggle" value="0"><span class="input-checked"></span>
                                            </label>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                        @endforeach


                        </tbody>
                    </table>
                    {{$phonesArray->links()}}
                </div>
            </div>
        </section>
    </div><!--#number_management-->



    <!-- Add User Modal -->
    <div class="modal fade" id="modal_add_number" tabindex="-1" role="dialog" aria-labelledby="modal_add_number">
        <div class="modal-dialog vdf_modal" role="document">
            <div class="modal-content vdf_modal_content">
                <div class="modal-header vdf_modal_header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="vdf_modal_sub_header">
                        <h3>Add one by one</h3>
                    </div>
                </div>
                <form method="post" action="{{url('/number')}}" class="form-horizontal vd_form">
                    <div class="modal-body vdf_modal_body">

                        <div class="form-group">
                            <div class="col-md-6 vdf_modal_left">
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Phone Number</label>
                                        <div class="relative">
                                            <input type="text" name="num_phone_num" class="block_btn_30 modal_input vd_number" value=""/>
                                            <i class="input_icon icon-phone_number"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="table_label">Parking SIM number</label>
                                        <div class="select_wrapper">
                                            <select name="prk_sim_num" class="block_btn_30 modal_input vd_select">
                                                <option value=""></option>
                                                @foreach($sims as $sim)
                                                <option value="{{$sim['id']}}">{{$sim['number']}}</option>
                                                @endforeach
                                            </select>
                                            <i class="input_icon icon-sim"></i>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Provider </label>
                                        <div class="select_wrapper">
                                            <select name="some_prov_num" class="block_btn_30 modal_input vd_select">
                                                <option value=""></option>
                                                <option value="1">Vodafone</option>
                                            </select>
                                            <i class="input_icon icon-provider"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="table_label">Type</label>
                                        <div class="select_wrapper">
                                            <select name="prk_sim_num2" class="block_btn_30 modal_input vd_select">
                                                <option value=""></option>
                                                @foreach($packages as $package)
                                                    <option value="{{$package['id']}}">{{$package['name']}}</option>
                                                @endforeach
                                            </select>
                                            <i class="input_icon icon-username"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="special_number">
                                            <label class="vdf_checkbox">
                                                <input type="checkbox" value="b" />
                                                <i class="icon-special"></i>
                                            </label>
                                            <span class="vdf_checkbox_text">Special number</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="or">OR</span>
                            </div>
                            <div class="col-md-6 vdf_modal_right">
                                <div class="form_row form-group">
                                    <div class="col-md-12">
                                        <label class="table_label moved_label">Add from file</label>
                                        <span class="uploaded_files">
                                            <span class="keep_file_name"></span>
                                            <span class="file_container"> Browse
                                                <input class="modal_image_name" type="file" />
                                            </span>
                                        </span>
                                        <span class="uploaded_file_links"> File example
                                            <a href="#" class="download_file disable" download=""><i class="icon-download"></i></a>
                                        </span>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div class="modal-footer vdf_modal_footer">
                        <a href="#" class="inline_block_btn light_gray_btn close vd_form_reset" data-dismiss="modal" aria-label="Close">Cancel</a>
                        <a href="#" class="inline_block_btn light_green_btn vd_form_submit">Add Number</a>
                    </div>
                </form>
            </div>
        </div>
    </div><!-- end Add User Modal -->


    <!-- Edit Number Modal -->
    <div class="modal fade" id="modal_edit_number" tabindex="-1" role="dialog" aria-labelledby="modal_edit_number">

        <div class="modal-dialog vdf_modal" role="document">
            <div class="modal-content vdf_modal_content">
                <div class="modal-header vdf_modal_header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="vdf_modal_sub_header">
                        <h3>Edit number</h3>
                    </div>
                </div>
                <form action="/" class="form-horizontal vd_form">
                    <div class="modal-body vdf_modal_body">
                        <div class="form-group">
                            <div class="col-md-6 vdf_modal_left">
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Phone Number</label>
                                        <div class="relative">
                                            <input type="text" name="some_phone_edit" class="block_btn_30 modal_input vd_number" value=""/>
                                            <i class="input_icon icon-phone_number"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="table_label">Parking SIM number</label>
                                        <div class="select_wrapper">
                                            <select name="select_s_type2" class="block_btn_30 modal_input vd_select">
                                                <option value=""></option>
                                                <option value="Admin">Parking SIM number</option>
                                                <option value="Dealer">Parking SIM number</option>
                                                <option value="Sub-Dealer">Parking SIM number</option>
                                            </select>
                                            <i class="input_icon icon-sim"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Provider </label>
                                        <div class="select_wrapper">
                                            <select name="select_s_type" class="block_btn_30 modal_input vd_select">
                                                <option value=""></option>
                                                <option value="Admin">type 1</option>
                                                <option value="Dealer">type 2</option>
                                                <option value="Sub-Dealer">type 3</option>
                                            </select>
                                            <i class="input_icon icon-provider"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="table_label">Type</label>
                                        <div class="select_wrapper">
                                            <select name="select_s_type3" class="block_btn_30 modal_input vd_select">
                                                <option value=""></option>
                                                <option value="Admin">type 1</option>
                                                <option value="Dealer">type 2</option>
                                                <option value="Sub-Dealer">type 3</option>
                                            </select>
                                            <i class="input_icon icon-provider"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <div class="special_number">
                                            <label class="vdf_checkbox">
                                                <input type="checkbox" value="b" />
                                                <i class="icon-special"></i>
                                            </label>
                                            <span class="vdf_checkbox_text">Special number</span>
                                        </div>
                                    </div>
                                </div>
                                <span class="or">OR</span>
                            </div>
                            <div class="col-md-6 vdf_modal_right">
                                <div class="form_row form-group">
                                    <div class="col-md-12">
                                        <label class="table_label">Add from file</label>
                                        <div class="form_row">
                                            <span class="uploaded_files">
                                                <span class="keep_file_name"></span>
                                                <span class="file_container"> Browse
                                                    <input class="modal_image_name" type="file" />
                                                </span>
                                                <span class="uploaded_file_links"> File example
                                                    <a href="#" class="download_file disable" download=""><i class="icon-download"></i></a>
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <div class="modal-footer vdf_modal_footer">
                        <a href="#" class="inline_block_btn light_gray_btn close vd_form_reset" data-dismiss="modal" aria-label="Close">Cancel</a>
                        <a href="#" class="inline_block_btn light_green_btn vd_form_submit">Edit Number</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- end of Edit Number Modal -->

@endsection