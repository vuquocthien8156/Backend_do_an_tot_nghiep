
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-thongke">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem;margin-top:3%;">
                <h4 class="tag-page-custom" style="color: blue">
                    Thống kê sản phẩm
                </h4>
            </div>
        </div>
        <div class="row">
        <div id="body" class="set-row background-contact w-100">
            Thống kê theo <select style="width: 200px;margin-bottom: 10px;" class="input-app mr-4" v-model="thongke" @change='search()'>
                <option value="">chọn mục</option>
                <option value="week">Tuần</option>
                <option value="month">Tháng</option>
            </select>
            <table id="tb1" class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
                <thead>
                    <tr class="text-center blue-opacity">
                        <th class="custom-view"><b>STT</b></th>
                        <th class="custom-view"><b>Tên sản phẩm</b></th>
                        <th class="custom-view"><b>Mã sản phẩm</b></th>
                        <th class="custom-view"><b>Loại sản phẩm</b></th>
                        <th class="custom-view"><b>Giá sản phẩm</b></th>
                        <th class="custom-view"><b>Mô tả</b></th>
                        <th class="custom-view"><b>Ngày ra mắt</b></th>
                        <th class="custom-view"><b>Tổng số lần đặt</b></th>
                        <th class="custom-view"><b>Hình ảnh</b></th>
                        <th class="custom-view"><b>Trạng thái</b></th>
                    </tr>
                </thead>
                <tbody v-cloak>
                    <tr class="text-center" v-for="(item,index) in results">
                        <td class="custom-view td-grey"><b>@{{index + 1}}</b></td>
                        <td  class="custom-view"><b>@{{item.ten}}</b></td>
                        <td  class="custom-view"><b>@{{item.ma_chu}}</b></td>
                        <td  class="custom-view"><b>@{{item.ten_loai_sp}}</b></td>
                        <td  class="custom-view" width="150px"><div style="text-align: left; width: 100px; height: 100px; margin-left: 15%"><b>S(@{{item.gia_san_pham}}) VNĐ</b><b> M(@{{item.gia_vua}}) VNĐ</b><b>L(@{{item.gia_lon}}) VNĐ</b></td>
                         <td class="custom-view text-left" style="width: 150px;">
                            <b>@{{ item.mo_ta}}</b> 
                        </td>
                        <td class="custom-view"><b>@{{item.ngay_ra_mat}}</b></td>
                        <td class="custom-view"><b>@{{item.total}}</b></td>
                        <td class="custom-view">
                                    <a data-fancybox="gallery" :href="item.pathToResource+'/'+item.hinh_san_pham">
                                        <img class="img-responsive" width="50px" height="50px" :src="item.pathToResource+'/'+item.hinh_san_pham">
                                    </a>
                        </td>
                        <td class="custom-view" v-if="item.daxoa == 1"><b>Đã xóa</b></td>
                        <td class="custom-view" v-if="item.daxoa == 0"><b>Đã kích hoạt</b></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    </div>
			
@endsection
@section('scripts')
           <script type="text/javascript">
				@php
					include public_path('/js/product/product/thongke.js');
                    include public_path('/js/product/product/jquery.fancybox.min.js');
				@endphp
			</script>
@endsection