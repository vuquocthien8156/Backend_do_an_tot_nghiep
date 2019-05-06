
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-account">
        <div id="edit" style="width: 35%;display: none;margin-bottom: 10%;margin-top: 10px;margin-left: 33%">
            <div style="border: 1px solid red">
                <div class="form-group" style="text-align: center;">
                    <h4 for="child_name1" style="color: blue;">Chỉnh sửa</h4>
                </div>
                <table border="0px" style="margin-left: 2%">
                    <tr class="form-group">
                        <td>
                            <div class="form-group">
                                <label for="child_name1">Hình ảnh</label>
                                <input type="file" class="form-control" id="imag1" style="width: 200px;">
                            </div>
                            
                                <input type="text" id="id_user" hidden="true" style="width: 200px;">
                            
                        </td>
                    </tr>
                </table>
                <div class="modal-footer">
                    <button type="button" @click="edit()" class="button-app" style="margin-right: 75%">Sửa</button>
                    <button type="button" @click="exit()" class="button-app ml-5 float-right">Thoát</button>
                </div>
            </div>
        </div>
        <div id="body" class="form-box col-12 m-auto" style="margin-bottom: 50%;margin-top: 3%;margin-left: 2%;margin-right: 2%">
            <input id="code" type="text" class="input-app mr-4"  placeholder="Tên"  style="width: 200px;margin-bottom: 10px" v-model="name">
            <button class="button-app ml-5 float-right" @click="search()">Tìm kiếm</button>
            <table border="1px" class="table table-bordered table-striped w-100" style="min-height: 30px; line-height: 1.4;">
                <thead>
                <tr>
                    <th>STT</th>
                    <th>Họ và tên</th>
                    <th>Số Điện Thoại</th>
                    <th>Ngày sinh</th>
                    <th>Giới tính</th>
                    <th>Điểm tích</th>
                    <th>Địa chỉ</th>
                    <th>Tài Khoản</th>
                    <th>Hình ảnh</th>
                    <th>Trạng thái</th>
                    <th>Hành Động</th>
                </tr>
                </thead>
                <tbody v-cloak>
                    <tr  v-for="(item,index) in results.data">
                        <td><p>@{{index + 1}}<p></td>
                        <td><p>@{{item.ten}}<p></td>
                        <td><p>@{{item.sdt}}<p></td>
                        <td><p>@{{item.ngay_sinh}}<p></td>
                        <td v-if="item.gioi_tinh == 1"><p>Nam<p></td>
                        <td v-if="item.gioi_tinh == 2"><p>Nữ<p></td>
                        <td v-if="item.gioi_tinh == 3"><p>Khác<p></td>
                        <td v-if="item.gioi_tinh == null"><p>Chưa có<p></td>
                        <td><p>@{{item.diem_tich}}<p></td>
                        <td><p>@{{item.dia_chi}}<p></td>
                        <td><p>@{{item.email}}<p></td>
                        <td><img :src="item.pathToResource+'/'+item.avatar" width="50px" height="50px"></td>
                        <td v-if="item.da_xoa == {{ \App\Enums\EStatus::DELETED }}">Đã xóa</td>
                        <td v-if="item.da_xoa == {{ \App\Enums\EStatus::ACTIVE }}">Đã kích hoạt</td>
                        <td><p>
                            <a href="#" class="btn_edit fa fa-edit" @click="seeMoreDetail(item.id);"></a>
                            <span class="btn_remove fa fa-trash" style="cursor: pointer;" @click="deleteHealthRecord(item.id)"  data-toggle="tooltip" data-placement="right" title="Xoá thẻ thành viên"></span><p></td>
                    <tr>
                </tbody>
            </table>
            <div class="col-12">
                    <pagination :data="results" @pagination-change-page="search"></pagination> 
            </div>
        </div>
    </div>
			
@endsection
@section('scripts')
           <script type="text/javascript">
				@php
					include public_path('/js/account/account/account.js');
				@endphp
			</script>
@endsection