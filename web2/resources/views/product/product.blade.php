
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-product">
        <div id="edit" style="width: 35%;display: none;margin-bottom: 10%;margin-top: 10px;margin-left: 33%">
            <div style="border: 1px solid red">
                <div class="form-group" style="text-align: center;">
                    <h4 for="child_name1" style="color: blue;">Chỉnh sửa</h4>
                </div>
                <form id="form_edit_info" method="POST" action="edit" enctype="multipart/form-data">
                <table border="0px" class=" table-striped w-100" style="margin-left: 2%">
                <tr>
                    <td style="width: 300px;">
                        <div class="form-group">
                            <label for="other_note1"> Tên sản phẩm </label>
                            <input type="text" class="form-control" id="ten" style="width: 200px;">
                        </div>
                    </td>
                    <td style="width: 300px;">
                        <div class="form-group">
                            <label for="other_note1"> Mã sản phẩm </label>
                            <input type="text" class="form-control" id="ma" style="width: 200px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Giá gốc </label>
                            <input type="text" class="form-control" id="gia_goc" style="width: 200px;">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Giá size vừa </label>
                            <input type="text" class="form-control" id="gia_size_vua" style="width: 200px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Giá size lớn </label>
                            <input type="text" class="form-control" id="gia_size_lon" style="width: 200px;">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Loại sản phẩm </label>
                            <select id="loaisp" class="form-control" style="width: 200px;">
                                <option value="">Chọn loại sản phẩm</option>
                                    @if (count($list) > 0)
                                        @foreach ($list as $item)
                                            <option value="{{ $item->ma_loai_sp }}" > {{$item->ten_loai_sp}}</option>
                                        @endforeach
                                     @endif
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Ngày ra mắt </label>
                            <input type="date" class="form-control" id="ngay_ra_mat"  style="width: 200px;">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="other_note1">Mô tả </label>
                            <textarea class="form-control" id="mo_ta"  style="width: 200px;"></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Số lần order </label>
                            <input type="text" class="form-control" id="so_lan_order"  style="width: 200px;">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Hình ảnh </label>
                            <input type="file"@change="onSelectImageHandler" class="form-control" id=""  style="width: 200px;">
                            <input type="input" hidden="true" id="id_product"  style="width: 200px;">
                        </div>
                    </td>
                </tr>
            </table>
                </form>
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
                    <th>Tên sản phẩm</th>
                    <th>Mã sản phẩm</th>
                    <th>Loại sản phẩm</th>
                    <th>Giá gốc</th>
                    <th>Giá size vừa</th>
                    <th>Giá size lớn</th>
                    <th>Số lượng Order</th>
                    <th>Mô tả</th>
                    <th>Ngày ra mắt</th>
                    <th>Hình ảnh</th>
                    <th>Trạng thái</th>
                    <th>Hành Động</th>
                </tr>
                </thead>
                <tbody v-cloak>
                    <tr  v-for="(item,index) in results.data">
                        <td><p>@{{index + 1}}<p></td>
                        <td><p>@{{item.ten}}<p></td>
                        <td><p>@{{item.ma_chu}}<p></td>
                        <td><p>@{{item.ten_loai_sp}}<p></td>
                        <td><p>@{{item.gia_san_pham}}<p></td>
                        <td><p>@{{item.gia_vua}}<p></td>
                        <td><p>@{{item.gia_lon}}<p></td>
                        <td><p>@{{item.so_lan_dat}}<p></td>
                        <td><p>@{{item.mo_ta}}<p></td>
                        <td><p>@{{item.ngay_ra_mat}}<p></td>
                        <td><img :src="item.pathToResource+'/'+item.hinh_san_pham" width="50px" height="50px"></td>
                        <td v-if="item.daxoa == {{ \App\Enums\EStatus::DELETED }}">Đã xóa</td>
                        <td v-if="item.daxoa == {{ \App\Enums\EStatus::ACTIVE }}">Đã kích hoạt</td>
                        <td><p>
                            <a href="#" class="btn_edit fa fa-edit" @click="seeMoreDetail(item.ma_so, item.ten, item.ma_chu, item.ten_loai_sp, item.gia_san_pham, item.gia_vua, item. gia_lon, item.so_lan_dat, item.ngay_ra_mat, item.mo_ta, item.ma_loai_sp);"></a>
                            <span class="btn_remove fa fa-trash" style="cursor: pointer;" @click="deleted(item.ma_so)"  data-toggle="tooltip" data-placement="right" title="Xoá"></span><p></td>
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
					include public_path('/js/product/product/product.js');
				@endphp
			</script>
@endsection