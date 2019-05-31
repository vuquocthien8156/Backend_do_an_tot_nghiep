
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-branch">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem;margin-top:5%">
                <h4 class="tag-page-custom">
                    Quản lý chi nhánh
                </h4>
            </div>
        </div>
        <div class="row">
        <div class="set-row background-contact w-100" style="min-height: 150px">
            <div class="col-md-10 mb-2">
                <input name="name" id="name" type="text" placeholder="Tên chi nhánh" v-model="name" class="input-app mr-4" style="width: 200px; height: 33px; cursor: pointer;">
                <select name="place" id="place" v-model="place" class="input-app mr-4" style="width: 200px; height: 33px; cursor: pointer;">
                    <option value="">Chi nhánh</option>
                     @if(count($list) > 0)
                        @foreach ($list as $value)
                             <option value="{{$value->ma_khu_vuc}}">{{$value->ten_khu_vuc}}</option>
                        @endforeach
                    @endif        
                </select>
            </div>
            <button class="button-app ml-5 float-right" @click="search()">Tìm kiếm</button>
            <table id="tb1" class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
                <thead>
                <tr class="text-center blue-opacity">
                    <th class="custom-view">STT</th>
                    <th class="custom-view">Tên chi nhánh</th>
                    <th class="custom-view">Địa chỉ</th>
                    <th class="custom-view">latitude</th>
                    <th class="custom-view">Longitude</th>
                    <th class="custom-view">Ngày khai trương</th>
                    <th class="custom-view">Giờ mở cửa</th>
                    <th class="custom-view">Giờ đóng cửa</th>
                    <th class="custom-view">Khu vực</th>
                    <th class="custom-view">Số điện thoại</th>
                    <th class="custom-view">Trạng thái</th>
                    <th class="custom-view">Hành Động</th>
                </tr>
                </thead>
                <tbody v-cloak>
                    <tr class="text-center" v-for="(item,index) in results.data">
                        <td class="custom-view td-grey">@{{index + 1}}</td>
                        <td class="custom-view">@{{item.ten}}</td>
                        <td class="custom-view">@{{item.dia_chi}}</td>
                        <td class="custom-view">@{{item.latitude}}</td>
                        <td class="custom-view" width="250px">@{{item.longitude}}</td>
                        <td class="custom-view">@{{item.ngay_khai_truong}}</td>
                        <td class="custom-view">@{{item.gio_mo_cua}}</td>
                        <td class="custom-view">@{{item.gio_dong_cua}}</td>
                        <td class="custom-view">@{{item.ten_khu_vuc}}</td>
                        <td class="custom-view">@{{item.sdt}}</td>
                        <td class="custom-view" v-if="item.da_xoa == 1">Đã xóa</td>
                        <td class="custom-view" v-if="item.da_xoa == 0">Đã kích hoạt</td>
                        <td class="custom-view">
                            <a href="#" class="btn_edit fa fa-edit" @click="seeMoreDetail(item.ma_so, item.ten, item.ma_chu, item.ten_loai_sp, item.gia_san_pham, item.gia_vua, item. gia_lon, item.so_lan_dat, item.ngay_ra_mat, item.mo_ta, item.ma_loai_sp, item.hinh_san_pham);"></a>
                            <span class="btn_remove fa fa-trash" style="cursor: pointer;" @click="deleted(item.ma_so)"  data-toggle="tooltip" data-placement="right" title="Xoá"></span></td>
                    </tr>
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
					include public_path('/js/Branch/Branch.js');
				@endphp
			</script>
@endsection