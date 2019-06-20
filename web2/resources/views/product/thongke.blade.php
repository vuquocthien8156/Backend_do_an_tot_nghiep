
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
                    <th  class="custom-view"><p>STT</p></th>
                    <th  class="custom-view"><p>Tên sản phẩm</p></th>
                    <th  class="custom-view"><p>Mã sản phẩm</p></th>
                    <th  class="custom-view"><p>Loại sản phẩm</p></th>
                    <th  class="custom-view"><p>Giá sản phẩm</p></th>
                    <th  class="custom-view"><p>Mô tả</p></th>
                    <th  class="custom-view"><p>Ngày ra mắt</p></th>
                    <th  class="custom-view"><p>Hình ảnh</p></th>
                    <th  class="custom-view"><p>Trạng thái</p></th>
                </tr>
                </thead>
                <tbody v-cloak>
                    <tr class="text-center" v-for="(item,index) in results">
                        <td class="custom-view td-grey"><b>@{{index + 1}}</b></td>
                        <td  class="custom-view"><b>@{{item.ten}}</b></td>
                        <td  class="custom-view"><b>@{{item.ma_chu}}</b></td>
                        <td  class="custom-view"><b>@{{item.ten_loai_sp}}</b></td>
                        <td  class="custom-view" width="150px"><div style="text-align: left; width: 100px; height: 100px; margin-left: 15%"><b>S(@{{item.gia_san_pham}}) VNĐ</b><b> M(@{{item.gia_vua}}) VNĐ</b><b>L(@{{item.gia_lon}}) VNĐ</b></td>
                         <td class="custom-view text-left" style="width: 150px;" v-if="item.mo_ta != null">
                            <span v-if="item.mo_ta.length < 30"><b>@{{ item.mo_ta}}</b></span>
                            <span v-if="item.mo_ta.length > 30"><b>@{{ item.mo_ta | contentSubstr}}</b><a v-if="item.mo_ta.length > 30" style="cursor: pointer; color: #55bde7;" @click="showDescription(item.mo_ta)" class="see_more_less"><b>...Xem thêm mô tả</b></a></span>  
                        </td>
                        <td class="custom-view"><b>@{{item.ngay_ra_mat}}</b></td>
                        <td class="custom-view">
                                    <a data-fancybox="gallery" :href="item.pathToResource+'/'+item.hinh_san_pham">
                                        <img class="img-responsive" width="50px" height="50px" :src="item.pathToResource+'/'+item.hinh_san_pham">
                                    </a>
                        </td>
                        <td class="custom-view" v-if="item.daxoa == 1">Đã xóa</td>
                        <td class="custom-view" v-if="item.daxoa == 0">Đã kích hoạt</td>
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