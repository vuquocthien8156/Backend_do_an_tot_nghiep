
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="permission" style="margin-top: 5%">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="row mt-5 pt-3 pb-5">
            <div style="padding-left: 2rem">
                <h4 class="tag-page-custom">
                    <a class="tag-title-show" style="text-decoration: none;" href="#"> PHÂN QUYỀN </a>
                </h4>
            </div>
        </div>
    <div class="container pl-0 pr-0 pb-5">
        <div class="w-100" style="min-height: 150px">
            <div class=" form-box col-12 m-auto">
                <div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 37rem;border: 1px solid black">
                    <form class="form-inline"> 
                        <input type="hidden" name="_token" :value="csrf">
                        <div class="text-center mx-auto mb-3"> <h2>Tạo tài khoản admin</h2> </div>
                        <div class="form-group w-100 mb-3">
                            <label for="name_user" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Tên</label>
                            <div class="col-md-8 p-0 input-group">
                                <input type="text" id="name_user" v-model="name_user" class="form-control" style="margin-right: 10px; background-color: #fff;" placeholder="Nhập tên" required>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3" >
                            <label for="email_user" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Email</label>
                            <div class="col-md-8 p-0 input-group">
                                <input type="text" id="email_user" v-model="email_user" class="form-control" style="margin-right: 10px; background-color: #fff;" placeholder="Nhập email" required>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="phone_user" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Số điện thoại</label>
                            <div class="col-md-8 p-0 input-group">
                                <input type="text" id="phone_user" v-model="phone_user" class="form-control" style="margin-right: 10px; background-color: #fff;" placeholder="Nhập số điện thoại" required>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3">
                            <label for="password_user" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Mật khẩu</label>
                            <div class="col-md-8 p-0 input-group">
                                <input type="password" id="password_user" v-model="password_user" class="form-control" style="margin-right: 10px; background-color: #fff;" placeholder="Nhập password" required>
                            </div>
                        </div>
                        <div class="form-group w-100 mb-3" >
                            <label for="cmnd" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Cho phép truy cập</label>
                            <div class="col-md-8 p-0 input-group">
                                @foreach ($listPermission as $value)
                                    <div class="col-md-8 p-0 mb-2">
                                        <div class="form-group">
                                            <input style="width: 20px; height: 20px" type="checkbox" name="chk_permission_group[]" class="input_type_check" id="permission_{{$value->id}}" value="{{$value->id}}"> 
                                            <label for="customer_type"> {{$value->ten_vai_tro}} </label><br>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="row mx-auto">
                            <button type="button" class="button-app" @click="saveUserWeb">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="set-row background-contact w-100" style="min-height: 150px">    
            <div id="table_1" class="position-relative">
                <table class="table table-bordered table-striped w-100" style="min-height: 150px">
                    <thead>
                        <tr class="text-center blue-opacity">
                            <th class="custom-view"> STT </th>
                            <th class="custom-view"> Họ Tên/ Số điện thoại </th>
                            <th class="custom-view"> Email </th>
                            <th class="custom-view"> Cho phép truy cập </th>
                            <th class="custom-view"> Hành động </th>
                        </tr>
                    </thead>
                    <tbody v-cloak>
                        <tr class="text-center" style="font-weight:bold" v-for="(item, index) in results.data">
                            <td class="custom-view td-grey" :class="{'grey-blue' : index%2 != 0}" style="font-weight: bold">@{{ (results.current_page - 1) * results.per_page + index + 1 }}</td>
                            <td class="custom-view text-left"> @{{ item.ten }} / <br> @{{ item.sdt }}</td>
                            <td class="custom-view text-left">@{{ item.email }}</td>
                            <td class="custom-view">
                                 <span v-for="(access, key) in item.listRoll"> <span v-if="key != 0">-</span>  @{{access.ten_vai_tro}} </span>
                            </td>
                            <td class="custom-view">
                                <span class="btn_edit fa fa-edit"  @click="getInfo(item.tai_khoan,item.ten,item.sdt,item.email,item.ten_vai_tro)" data-toggle="tooltip" data-placement="left" title="Sửa"></span>
                                <span class="btn_remove fa fa-trash" @click="deleteUserWeb(item.tai_khoan)" data-toggle="tooltip" data-placement="right" title="Xoá"></span>
                            </td>
                        <tr>
                    </tbody> 
                </table>
            </div>
        </div>
    </div>
     {{-- Modal update User authorization --}}
     <div class="modal fade" id="ModalUpdateUserAuthorization" tabindex="-1" role="dialog" aria-labelledby="ModalUpdateUserAuthorization" aria-hidden="true">
            <div class="modal-dialog" role="document" style="width: 500px">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"> Chỉnh sửa thông tin </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>                     
                    <div class="modal-body">
                        <div class="form-group row">
                            <label for="name_user_update" class="col-sm-4"><b> Tên </b></label>
                            <div class="col-sm-8">
                                <input type="text" readonly="true" id="name_user_update" class="form-control" v-model="name_user_update">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label for="email_user_update" class="col-sm-4"> <b>Email</b> </label>
                            <div class="col-sm-8">
                                <input type="text" readonly="true" id="email_user_update" class="form-control" v-model="email_user_update">
                            </div>
                        </div> 
                        <div class="form-group row">
                            <label for="phone_user_update" class="col-sm-4"> <b>Số điện thoại </b></label>
                            <div class="col-sm-8">
                                <input type="text" readonly="true" id="phone_user_update" class="form-control" v-model="phone_user_update">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="info_user_update" class="col-sm-4"> <b>Cho phép truy cập </b></label>
                            <div class="col-sm-8">
                                @foreach ($listPermission as $value)
                                    <div class="col-md-8">
                                        <div>
                                            <input style="width: 20px; height: 20px" type="checkbox" name="chk_permission_group_update[]" class="input_type_check" id="permission_update_{{$value->id}}" value="{{$value->id}}"> 
                                            <label for="permission_update_{{$value->id}}"> {{$value->ten_vai_tro}} </label><br>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal"> Đóng </button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" @click="updateUserWeb()"> Cập nhật </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
			
@endsection
@section('scripts')
           <script type="text/javascript">
				@php
					include public_path('/js/permission/permission/permission.js');
				@endphp
			</script>
@endsection