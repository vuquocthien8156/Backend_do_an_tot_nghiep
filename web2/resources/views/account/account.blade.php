
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-account">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem; margin-top:5%">
                <h4 class="tag-page-custom">
                    Quản lý tài khoản
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="set-row background-contact w-100" style="min-height: 150px">
                <input id="code" type="text" class="input-app mr-4"  placeholder="Tên"  style="width: 200px;margin-bottom: 10px" v-model="name">
            <button class="button-app ml-5 float-right" @click="search()">Tìm kiếm</button>
           <table id="tb1" class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
                <thead>
                <tr class="text-center blue-opacity">
                    <th class="custom-view">STT</th>
                    <th class="custom-view">Họ và tên</th>
                    <th class="custom-view">Số Điện Thoại</th>
                    <th class="custom-view">Ngày sinh</th>
                    <th class="custom-view">Giới tính</th>
                    <th class="custom-view">Điểm tích</th>
                    <th class="custom-view">Địa chỉ</th>
                    <th class="custom-view">Tài Khoản</th>
                    <th class="custom-view">Hình ảnh</th>
                    <th class="custom-view">Trạng thái</th>
                    <th class="custom-view">Hành Động</th>
                </tr>
                </thead>
                <tbody v-cloak>
                    <tr class="text-center" v-for="(item,index) in results.data">
                        <td class="custom-view td-grey">@{{index + 1}}</td>
                        <td  class="custom-view">@{{item.ten}}</td>
                        <td  class="custom-view">@{{item.sdt}}</td>
                        <td  class="custom-view">@{{item.ngay_sinh}}</td>
                        <td  class="custom-view" v-if="item.gioi_tinh == 1">Nam</td>
                        <td  class="custom-view" v-if="item.gioi_tinh == 2">Nữ</td>
                        <td  class="custom-view" v-if="item.gioi_tinh == 3">Khác</td>
                        <td  class="custom-view" v-if="item.gioi_tinh == null">Chưa có</td>
                        <td  class="custom-view">@{{item.diem_tich}}</td>
                        <td  class="custom-view">@{{item.dia_chi}}</td>
                        <td  class="custom-view" v-if="item.email != null">@{{item.email}}</td>
                        <td  class="custom-view" v-if="item.email == null">Chưa có</td>
                        <td  class="custom-view"><img :src="item.pathToResource+'/'+item.avatar" width="50px" height="50px"></td>
                        <td  class="custom-view" v-if="item.da_xoa == 1">Đã xóa</td>
                        <td  class="custom-view" v-if="item.da_xoa == 0">Đã kích hoạt</td>
                        <td  class="custom-view">
                            <a href="#" class="btn_edit fa fa-edit" @click="seeMoreDetail(item.id);"></a>
                            <span class="btn_remove fa fa-trash" style="cursor: pointer;" @click="deleteHealthRecord(item.id)"  data-toggle="tooltip" data-placement="right" title="Xoá thẻ thành viên"></span></td>
                    <tr>
                </tbody>
            </table>
            <div class="col-12">
                    <pagination :data="results" @pagination-change-page="search"></pagination> 
            </div>
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