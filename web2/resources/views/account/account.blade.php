
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-account">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem; margin-top:2%">
                <h4 class="tag-page-custom" style="color: blue">
                    Quản lý tài khoản
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="set-row background-contact w-100" style="min-height: 150px">
                <input id="" type="text" class="input-app mr-4"  placeholder="Tên"  style="width: 200px;margin-bottom: 10px" v-model="name">
                <input id="" type="text" class="input-app mr-4"  placeholder="sdt"  style="width: 200px;margin-bottom: 10px" v-model="phone">
                <select class="input-app mr-4" v-model="gender" style="width: 200px; height: 33px; cursor: pointer;">
                        <option value=""> Chọn giới tính </option>
                        <option value="1"> Nam </option>
                        <option value="2"> Nữ </option>
                    </select>
                <button class="button-app ml-5 float-right" @click="search()">Tìm kiếm</button>
                <a :href="'excel-account?name='+exportaccount.name+'&phone='+exportaccount.phone+'&gender='+exportaccount.gender" class="btn btn-primary button-app mb-4 float-right" style="color: white">Xuất File Excel</a>
               <table class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
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
                        <tr class="text-center" style="font-weight: bold" v-for="(item,index) in results.data">
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
                            <td  class="custom-view"> <a data-fancybox="gallery" :href="item.pathToResource+'images/'+item.avatar">
                                        <img class="img-responsive" width="50px" height="50px" :src="item.pathToResource+'images/'+item.avatar">
                                    </a></td>
                            <td  class="custom-view" v-if="item.da_xoa == 1">Đã xóa</td>
                            <td  class="custom-view" v-if="item.da_xoa == 0">Đã kích hoạt</td>
                            <td  class="custom-view">
                                <a href="#" class="btn_edit fa fa-edit" @click="seeMoreDetail(item.ten, item.sdt, item.ngay_sinh, item.gioi_tinh, item.diem_tich, item.dia_chi, item.email, item.avatar, item.id);"></a>
                                <span class="btn_remove fa fa-trash" style="cursor: pointer;" @click="deleteHealthRecord(item.id)"  data-toggle="tooltip" data-placement="right" title="Xoá thẻ thành viên"></span></td>
                        <tr>
                    </tbody>
                </table>
                <div class="col-12" style="margin-left: 80%">
                        <pagination :data="results" @pagination-change-page="search"></pagination> 
                </div>
                <div class="modal fade" id="update" tabindex="-1" role="dialog" aria-labelledby="update" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 500px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Chỉnh sửa</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>       
                        <div class="modal-body">
                            <form id="form_edit_info" method="POST" action="edit" enctype="multipart/form-data">
                                
                <table border="0px" class=" table-striped w-100" style="margin-left: 2%">
                <tr>
                    <td>
                        <label for=""><b>Hình ảnh</b></label>
                        <img id="avatarcollector_edit" style="width: 100px; height: 100px;" class="d-block" :src="imageUrl" />
                    </td>
                    <td>
                        <div class="form-group">
                            <input name="files_edit" type="file" class="mt-3" id="files_edit" accept="image/*" @change="onSelectImageHandler" ref="fileInputEl"/>
                            <input type="input" hidden="true" id="id_user"  style="width: 200px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td style="width: 300px;">
                        <div class="form-group">
                            <label for=""> <b>Tên</b></label>
                            <input type="text" class="form-control" id="ten" style="width: 200px;">
                        </div>
                    </td>
                    <td style="width: 300px;">
                        <div class="form-group" style="margin-left: 15%">
                            <label for=""><b> SDT</b> </label>
                            <input type="text" class="form-control" id="SDT" style="width: 200px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <label for=""> <b>Ngày sinh</b> </label>
                            <input type="text" class="form-control" id="NS" style="width: 200px;">
                        </div>
                    </td>
                    <td>
                        <div class="form-group" style="margin-left: 15%">
                            <label for=""> <b>giới tính </b></label>
                            <select id="gender" class="form-control" style="width: 200px;">
                                <option value="">Chọn giới tính</option>
                                <option value="1">Nam</option>
                                <option value="2">Nữ</option>
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <label for=""> <b>Điểm tích</b> </label>
                            <input type="text" class="form-control" id="diemtich" style="width: 200px;">
                        </div>
                    </td>
                    <td>
                        <div class="form-group" style="margin-left: 15%">
                            <label for=""> <b>Địa chỉ</b> </label>
                            <textarea type="text" class="form-control" id="diachi" style="width: 200px;"></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <label for=""><b>Tên tài khoản</b> </label>
                            <input type="text" class="form-control" id="email"  style="width: 200px;">
                        </div>
                    </td>
                </tr>
            </table>
                </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="edit()" class="button-app">Sửa</button>
                            <button type="button" class="button-app ml-5 float-right" data-dismiss="modal">Đóng</button>
                        </div>
                        </div>
                    </div>
        </div>
            </div>
        </div>
    </div>
			
@endsection
@section('scripts')
           <script type="text/javascript">
				@php
					include public_path('/js/account/account/account.js');
                    include public_path('/js/account/account/jquery.fancybox.min.js');
				@endphp
			</script>
@endsection