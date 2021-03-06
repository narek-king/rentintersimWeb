@extends('layouts.admin')

@section('dashboard')

    <div id="user_management">
        <section class="filter_status">
            <div class="user_management_wrapper">
                <div class="filter_buttons">
                    <div class="search_management_option">
                        {{--<form action="/" class="search_form_option">--}}
                            {{--<input type="text" class="block_btn_30 search_input" value="search">--}}
                            {{--<button type="submit" class="search_button"><i class="icon-search"></i></button>--}}
                        {{--</form>--}}
                        {{--<a href="#" class="export_user"><i class="icon-export"></i>Export</a>--}}
                        <a href="#" class="add_new_btn" data-toggle="modal" data-target="#modal_add_user"><i class="icon-new_order"></i>New User</a>
                    </div>
                </div>
                <div class="clear"></div>
            </div>
        </section>
        <section class="section_table">
            <div class="row">
                <div class="col-md-12">
                    <div id="wrap_tree_table"></div>
                </div>
            </div>
        </section>
    </div><!---#user_management-->


    <!--Add Use Modal -->
    <div class="modal fade" id="modal_add_user" tabindex="-1" role="dialog" aria-labelledby="modal_add_user">
        <div class="modal-dialog vdf_modal" role="document">
            <div class="modal-content vdf_modal_content">
                <div class="modal-header vdf_modal_header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="vdf_modal_sub_header">
                        <h3>New User</h3>
                    </div>
                </div>
                <form action="/user" id="add-user" class="form-horizontal vd_form">
                    <div class="modal-body vdf_modal_body">
                        <div class="form-group">
                            <div class="col-md-6 vdf_modal_left">
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Name <span class="required_mark">*</span></label>
                                        <div class="form_row">
                                            <input type="text" name="name" id="name" class="block_btn_30 modal_input vd_required" value=""/>
                                            {{csrf_field()}}
                                            <i class="input_icon icon-username"></i>
                                        </div>
                                    </div>

                                    <div class="col-md-6 hidden">
                                        {{--<label class="table_label">Type <span class="required_mark">*</span></label>--}}
                                        {{--<div class="form_row">--}}
                                            {{--<div class="select_wrapper">--}}
                                                {{--<select class="block_btn_30 modal_input" name="type" id="type">--}}
                                                    {{--<option value=""></option>--}}
                                                    {{--<option value="admin">Admin</option>--}}
                                                    {{--<option value="manager">Manager</option>--}}
                                                    {{--<option value="employee">Employee</option>--}}
                                                {{--</select>--}}
                                                {{--<i class="input_icon icon-username"></i>--}}
                                            {{--</div>--}}
                                        {{--</div>--}}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Parent Username <span class="required_mark">*</span></label>
                                        <div class="form_row">
                                            <div class="select_wrapper">
                                                <select class="block_btn_30 modal_input vd_select" name="supervisor_id" id="supervisor_id">
                                                    <option value="">SELECT PARENT</option>
                                                    <option class="" value="{{$defUser->id}}">{{$defUser->login}}</option>
                                                    @foreach($users as $supervisor)
                                                        <option value="{{$supervisor['id']}}">{{$supervisor['login']}} ({{$supervisor['level']}})</option>
                                                    @endforeach
                                                </select>
                                                <i class="input_icon icon-username"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="table_label">Type</label>
                                        <div class="form_row">
                                             <input type="radio" name="type" value="manager"> Manager<br>
                                             <input type="radio" name="type" value="employee"> Employee<br>
                                            {{--<i class="input_icon icon-username"></i>--}}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Primary Email <span class="required_mark">*</span></label>
                                        <div class="form_row">
                                            <input type="text" class="block_btn_30 modal_input vd_email vd_required" name="email" id="email" value="" data-th="Primary Email"/>
                                            <i class="input_icon icon-email"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="table_label">Secondary Email</label>
                                        <div class="form_row">
                                            <input type="text" class="block_btn_30 modal_input vd_email" name="email2" id="email2" value="" data-th="Secondary Email"/>
                                            <i class="input_icon icon-email"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6 vdf_modal_right">
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Username <span class="required_mark">*</span></label>
                                        <div class="form_row">
                                            <input type="text" class="block_btn_30 modal_input vd_required" id="username" name="username" value=""/>
                                            <i class="input_icon icon-username"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="table_label">Password <span class="required_mark">*</span></label>
                                        <div class="form_row">
                                            <input type="password" class="block_btn_30 modal_input vd_required" id="password" name="password" value=""/>
                                            <i class="input_icon icon-password"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer vdf_modal_footer">
                    <a href="#" class="inline_block_btn light_gray_btn close vd_form_reset" data-dismiss="modal" aria-label="Close">Cancel</a>
                    <a href="#" class="inline_block_btn light_green_btn vd_form_submit" id="create-user">Create User</a>

                    <span class="required_mark_description">* Required field</span>
                    <span class="success_response"></span>
                    <span class="error_response"></span>
                </div>
                </form>
            </div>
        </div>
    </div><!-- end Add User Modal -->

    <!--Edit User Modal -->
    <div class="modal fade" id="modal_edit_user" tabindex="-1" role="dialog" aria-labelledby="modal_edit_user">
        <div class="modal-dialog vdf_modal" role="document">
            <div class="modal-content vdf_modal_content">
                <div class="modal-header vdf_modal_header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <div class="vdf_modal_sub_header">
                        <h3>Edit User</h3>
                    </div>
                </div>
                <form action="/" id="edit-user" class="form-horizontal vd_form">
                <div class="modal-body vdf_modal_body">
                        <div class="form-group">
                            <div class="col-md-6 vdf_modal_left">
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Name <span class="required_mark">*</span></label>
                                        <div class="relative">
                                            <input type="text" class="block_btn_30 modal_input name" name="name" id="name_edit" data-th="Name" value=""/>
                                            <input type="hidden" value="" class="user_edit_id" />
                                            <i class="input_icon icon-username"></i>
                                            {{csrf_field()}}
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <label class="table_label">Type <span class="required_mark">*</span></label>
                                        <div class="select_wrapper">
                                            <select class="block_btn_30 modal_input type {{ (Auth::user()->level != 'Super admin') ? 'disable' : '' }}" name="type" data-th="Type" {{ (Auth::user()->level != 'Super admin') ? 'disabled' : '' }}>
                                                {{--<option value=""></option>--}}
                                                {{--<option value="admin">Admin</option>--}}
                                                {{--<option value="manager">Manager</option>--}}
                                                {{--<option value="employee">Employee</option>--}}
                                            </select>
                                            <i class="input_icon icon-username"></i>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">

                                    <div class="col-md-6">
                                        <label class="table_label {{ (Auth::user()->level != 'Super admin') ? 'hidden' : 'hidden' }}">Parent Username</label>
                                        <div class="form_row {{ (Auth::user()->level != 'Super admin') ? 'hidden' : 'hidden' }}">
                                            <div class="select_wrapper">
                                                <select class="block_btn_30 modal_input supervisor_id" name="supervisor_id" id="supervisor_id">
                                                    <option value="{{Auth::user()->id}}">{{Auth::user()->login}}</option>
                                                    @foreach($users as $supervisor)
                                                        <option value="{{$supervisor['id']}}">{{$supervisor['login']}}</option>
                                                    @endforeach
                                                </select>
                                                <i class="input_icon icon-username"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Primary Email <span class="required_mark">*</span></label>
                                        <div class="form_row">
                                            <input type="text" class="block_btn_30 modal_input vd_email vd_required email" name="email" id="email" value="" data-th="Primary Email"/>
                                            <i class="input_icon icon-email"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="table_label">Secondary Email</label>
                                        <div class="form_row">
                                            <input type="text" class="block_btn_30 modal_input vd_email email2" name="email2" id="email2" value="" data-th="Secondary Email"/>
                                            <i class="input_icon icon-email"></i>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6 vdf_modal_right">
                                <div class="form-group">
                                    <div class="col-md-6">
                                        <label class="table_label">Username <span class="required_mark">*</span></label>
                                        <div class="relative">
                                            <input type="text" class="block_btn_30 modal_input login {{ (Auth::user()->level != 'Super admin') ? 'disable' : '' }}" data-th="Username" value="" />
                                            <i class="input_icon icon-username"></i>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="table_label">Password</label>
                                        <div class="relative">
                                            <input type="password" class="block_btn_30 modal_input password"  value=""/>
                                            <i class="input_icon icon-password"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                </div>
                <div class="modal-footer vdf_modal_footer">
                    <a href="#" class="inline_block_btn light_gray_btn close vd_form_reset" data-dismiss="modal" aria-label="Close">Cancel</a>
                    <a href="#" class="inline_block_btn light_green_btn vd_form_submit" id="edit_user_submit">Update User</a>

                    <span class="required_mark_description">* Required field</span>
                    <span class="success_response"></span>
                    <span class="error_response"></span>
                </div>
                </form>
            </div>
        </div>
    </div><!-- end Add User Modal -->

@endsection