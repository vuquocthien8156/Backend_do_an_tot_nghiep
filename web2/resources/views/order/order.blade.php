
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="order">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem; margin-top:2%">
                <h4 class="tag-page-custom" style="color: blue">
                    Đơn hàng
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="set-row background-contact w-100" style="min-height: 150px">
                <input id="" type="text" class="input-app mr-4"  placeholder="Mã chữ"  style="width: 200px;margin-bottom: 10px" v-model="code">
                <button class="button-app" @click="search()">Tìm kiếm</button>
               <table class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
                    <thead>
                    <tr class="text-center blue-opacity">
                        <th class="custom-view">STT</th>
                        <th class="custom-view">Mã đơn hàng</th>
                        <th class="custom-view">Thông tin giao hàng</th>
                        <th class="custom-view">khuyến mãi</th>
                        <th class="custom-view">Phí ship</th>
                        <th class="custom-view">Tổng tiền</th>
                        <th class="custom-view">Ghi chú</th>
                        <th class="custom-view">Phương thức thanh toán</th>
                        <th class="custom-view">Ngày lập</th>
                        <th class="custom-view">Thông tin trạng thái</th>
                        <th class="custom-view">Trạng thái</th>
                        <th class="custom-view">Hành Động</th>
                    </tr>
                    </thead>
                    <tbody v-cloak>
                        <tr class="text-center" style="font-weight: bold" v-for="(item,index) in results.data">
                            <td class="custom-view td-grey">@{{index + 1}}</td>
                            <td class="custom-view">@{{item.ma_chu}}</td>
                            <td class="custom-view ">@{{item.thong_tin_giao_hang}}</td>
                            <td class="custom-view ">@{{item.ten_khuyen_mai}}</td>
                            <td class="custom-view ">@{{item.phi_ship}} VNĐ</td>
                            <td class="custom-view ">@{{item.tong_tien}} VNĐ</td>
                            <td class="custom-view ">@{{item.ghi_chu}}</td>
                            <td class="custom-view ">@{{item.phuong_thuc_thanh_toan}}</td>
                            <td class="custom-view ">@{{item.ngay_lap}}</td>
                            <td v-if="item.trang_thai != 5 && item.trang_thai != null" class="custom-view ">@{{item.ten_trang_thai}}<a href="#" @click="duyet(item.madh, item.trang_thai, item.tong_tien, item.ma_khach_hang, item.phuong_thuc_thanh_toan)">...Duyệt</a></td>
                            <td v-if="item.trang_thai == 5" class="custom-view ">@{{item.ten_trang_thai}}</td>
                            <td class="custom-view ">
                                <span href="#" v-if="item.da_xoa == 0" class="btn_edit fa fa-check" style="color: green"></span>
                                <span href="#" v-if="item.da_xoa == 1" class="btn_edit fa fa-times" style="color: red"></span>
                            </td>
                            <td  class="custom-view">
                                <a href="#" class="btn_edit fa fa-edit" @click="seeMoreDetail();"></a>
                                <span class="btn_remove fa fa-trash" style="cursor: pointer;" @click="deleteOrder(item.madh)"  data-toggle="tooltip" data-placement="right" title="Xoá thẻ thành viên"></span></td>
                        <tr>
                    </tbody>
                </table>
                 <div class="col-12" style="margin-left: 80%">
                        <pagination :data="results" @pagination-change-page="search"></pagination> 
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
           <script type="text/javascript">
				@php
					include public_path('/js/order/order/order.js');
				@endphp
			</script>
@endsection