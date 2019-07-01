
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="order" style="font-weight: bold">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem; margin-top:2%">
                <h4 class="tag-page-custom" style="color: blue">
                    Đơn hàng
                </h4>
            </div>
        </div>
        <div class="row">
            <div class="modal fade" id="ModalShowDetail" tabindex="-1" role="dialog" aria-labelledby="ModalShowDetail" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 1000px">
                        <div class="modal-content">
                            <div class="modal-header">
                                Chi tiết đơn hàng
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>                     
                            <div class="modal-body">
                                <table class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
                                    <thead style="">
                                        <tr class="text-center blue-opacity">
                                            <th class="custom-view">STT</th>
                                            <th class="custom-view">Tên sản phẩm</th>
                                            <th class="custom-view">Số lượng</th>
                                            <th class="custom-view">Đơn giá</th>
                                            <th class="custom-view">Kích cở</th>
                                            <th class="custom-view">Giá khuyến mãi</th>
                                            <th class="custom-view">Thành tiền</th>
                                            <th class="custom-view">Ghi chú</th>
                                        </tr>
                                    </thead>
                                    <tbody v-cloak>
                                        <tr class="text-center" style="font-weight: bold" v-for="(item,index) in detail">
                                            <td class="custom-view td-grey">@{{index + 1}}</td>
                                            <td class="custom-view">@{{item.ten}}</td>
                                            <td class="custom-view">@{{item.so_luong}}</td>
                                            <td class="custom-view">@{{item.don_gia}}</td>
                                            <td class="custom-view">@{{item.kich_co}}</td>
                                            <td class="custom-view">@{{item.gia_khuyen_mai}}</td>
                                            <td class="custom-view">@{{item.thanh_tien}}</td>
                                            <td class="custom-view">@{{item.ghi_chu}}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"> Huỷ bỏ </button>
                            </div>
                        </div>
                    </div>
                </div>
            <div class="modal fade" id="ModalStatus" tabindex="-1" role="dialog" aria-labelledby="ModalStatus" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 300px;margin-top: 10%">  
                        <div class="modal-content">               
                            <div style="border-bottom: 1px solid black">
                                <label id="ma" style="margin-top: 2%; margin-left: 2%"></label>
                            </div> 
                            <div class="modal-body">
                                 @if (count($getStatus) > 0)
                                        @foreach ($getStatus as $item)
                                            <p v-if="{{$item->ma_trang_thai}} == this.trang_thai" style="color: green">{{$item->ma_trang_thai}} - {{$item->ten_trang_thai}}</p>
                                            <p v-else>{{$item->ma_trang_thai}} - {{$item->ten_trang_thai}}</p>
                                        @endforeach
                                     @endif
                            </div>
                        </div>
                    </div>
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
                            
                <table border="0px" class=" table-striped w-100" style="margin-left: 0%">
                    <tr>
                        <td>
                            <label for="other_note1"> Thông tin giao hàng </label>
                            <input type="text" class="form-control" id="TTGH" style="width: 200px;">
                        </td>
                        <td>
                            <label for="other_note1"> Tên khuyến mãi </label>
                            <select id="KM" class="form-control" style="width: 200px;">
                                <option value="">Chọn loại khuyến mãi</option>
                                    @if (count($getAllDisCount) > 0)
                                        @foreach ($getAllDisCount as $item)
                                            <option value="{{ $item->ma_khuyen_mai }}" > {{$item->ten_khuyen_mai}}</option>
                                        @endforeach
                                     @endif
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="other_note1"> Phí ship </label>
                            <input type="text" class="form-control" id="PS" style="width: 200px;">
                        </td>
                        <td>
                            <label for="other_note1"> Tổng tiền </label>
                            <input type="text" class="form-control" id="TT" style="width: 200px;">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="other_note1"> Ghi chú </label>
                            <input type="text" class="form-control" id="GC" style="width: 200px;">
                        </td>
                        <td>
                            <label for="other_note1"> Phương thức thanh toán </label>
                            <input type="text" class="form-control" id="PTTT" style="width: 200px;">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label for="other_note1"> Ngày lập </label>
                            <input type="date" class="form-control" id="NL" style="width: 200px;">
                        </td>
                        <td>
                            <input type="hidden" class="form-control" id="id" style="width: 200px;">
                        </td>
                    </tr>
                </table>
                        </div>
                        <div class="modal-footer">
                            <button type="button" @click="editOrder()" class="button-app">Sửa</button>
                            <button type="button" class="button-app ml-5 float-right" data-dismiss="modal">Đóng</button>
                        </div>
                        </div>
                    </div>
        </div>
            <div class="set-row background-contact w-100" style="min-height: 150px">
                <input id="" type="text" class="input-app mr-4"  placeholder="Mã chữ"  style="width: 200px;margin-bottom: 10px" v-model="code">
                <button class="button-app" @click="search()">Tìm kiếm</button>
                <button class="button-app" style="margin-left: 2%" @click="duyet()">Duyệt trạng thái</button>
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
                        <th class="custom-view">Thông tin trạng thái đơn hàng</th>
                        <th class="custom-view">Chọn duyệt</th>
                        <th class="custom-view">Hành Động</th>
                    </tr>
                    </thead>
                    <tbody v-cloak>
                        <tr class="text-center" style="font-weight: bold" v-for="(item,index) in results.data">
                            <td class="custom-view td-grey">@{{index + 1}}</td>
                            <td class="custom-view"><a style="cursor: pointer; color: blue;" @click="showDetail(item.madh)" class="see_more_less">@{{item.ma_chu}}</a></td>
                            <td class="custom-view ">@{{item.thong_tin_giao_hang}}</td>
                            <td class="custom-view ">@{{item.ten_khuyen_mai}}</td>
                            <td class="custom-view ">@{{item.phi_ship2}} VNĐ</td>
                            <td class="custom-view ">@{{item.tong_tien2}} VNĐ</td>
                            <td class="custom-view ">@{{item.ghi_chu}}</td>
                            <td class="custom-view " v-if="item.phuong_thuc_thanh_toan == 3">Thanh toán bằng thẻ</td>
                            <td class="custom-view " v-if="item.phuong_thuc_thanh_toan == 1">Thanh toán khi nhận hàng</td>
                            <td class="custom-view " v-if="item.phuong_thuc_thanh_toan == 2">Thanh toán bằng điểm</td>
                            <td class="custom-view " v-if="item.phuong_thuc_thanh_toan == 4">Thanh toán bằng thẻ và điểm</td>
                            <td class="custom-view ">@{{item.ngay_lap}}</td>
                            <td class="custom-view " @click="seeMoreStatus(item.trang_thai, item.ma_chu)" style="cursor: pointer;color: green">@{{item.ten_trang_thai}}</td>
                            <td class="custom-view ">
                                 <span href="#" v-if="item.trang_thai == 5" class="btn_edit fa fa-check" style="color: green"></span>
                                <input v-else style="width: 20px;height: 20px" :value="item.madh" name="check[]" v-model="checkApprove" type="checkbox">
                            </td>
                            <td  class="custom-view">
                                <a href="#" class="btn_edit fa fa-edit" @click="seeMoreDetail(item.thong_tin_giao_hang, item.khuyen_mai,item.phi_ship,item.tong_tien,item.ghi_chu,item.phuong_thuc_thanh_toan,item.ngay_lap,item.madh);" title="Sửa"></a>
                                <span v-if="item.da_xoa == 0" class="btn_remove fa fa-trash" style="cursor: pointer;" @click="deleteOrder(item.madh)"  data-toggle="tooltip" data-placement="right" title="Xoá"></span></td>
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