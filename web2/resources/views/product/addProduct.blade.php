
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-add-product">
        <div id="edit" style="width: 35%;display: none;margin-bottom: 10%;margin-top: 10px;margin-left: 33%">
            <div style="border: 1px solid red">
                <div class="form-group" style="text-align: center;">
                    <h4 for="child_name1" style="color: blue;">Chỉnh sửa</h4>
                </div>
                <form id="add" method="POST" action="add-new" enctype="multipart/form-data">
                <table border="0px" style="margin-left: 2%">
                    <tr class="form-group">
                        <td>
                            <div class="form-group">
                                <label for="child_name1">Hình ảnh</label>
                                <input type="file" class="form-control" @change="onSelectImageHandler" id="imag1" style="width: 200px;">
                            </div>
                            
                                <input type="text" id="id_user" hidden="true" style="width: 200px;">
                            
                        </td>
                    </tr>
                </table>
                </form>
                <div class="modal-footer">
                    <button type="button" @click="edit1()" class="button-app" style="margin-right: 75%">Sửa</button>
                    <button type="button" @click="exit()" class="button-app ml-5 float-right">Thoát</button>
                </div>
            </div>
        </div>
        <div id="body" class="form-box col-12 m-auto" style="margin-bottom: 50%;margin-top: 3%;margin-left: 2%;margin-right: 2%">
            <h2>Thêm sản phẩm</h2>
            <form id="add" method="POST" action="product/edit" enctype="multipart/form-data">
            <table border="0px" class=" table-striped w-100" style="min-height: 30px; line-height: 1.4;margin-left: 30%">
                <tr>
                    <td style="width: 300px;">
                        <div class="form-group">
                            <label for="other_note1"> Tên sản phẩm </label>
                            <input type="text" class="form-control" id="" v-model="ten" style="width: 200px;">
                        </div>
                    </td>
                    <td style="width: 300px;">
                        <div class="form-group">
                            <label for="other_note1"> Mã sản phẩm </label>
                            <input type="text" class="form-control" id="" v-model="ma"  style="width: 200px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Giá gốc </label>
                            <input type="text" class="form-control" id="" v-model="gia_goc" style="width: 200px;">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Giá size vừa </label>
                            <input type="text" class="form-control" id="" v-model ="gia_size_vua" style="width: 200px;">
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Giá size lớn </label>
                            <input type="text" class="form-control" id="" v-model ="gia_size_lon" style="width: 200px;">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Loại sản phẩm </label>
                            <select id="model" class="form-control" v-model="loaisp" style="width: 200px;">
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
                            <input type="date" class="form-control" v-model="ngay_ra_mat" id=""  style="width: 200px;">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <label for="other_note1">Mô tả </label>
                            <textarea type="text" v-model="mo_ta" class="form-control" id=""  style="width: 200px;"></textarea>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="form-group">
                            <label for="other_note1"> Hình ảnh </label>
                            <input type="file"@change="onSelectImageHandler" class="form-control" id=""  style="width: 200px;">
                        </div>
                    </td>
                    <td>
                        <div class="form-group">
                            <button type="button" @click="luu();" class="button-app" style="margin-right: 75%">lưu</button>
                        </div>
                    </td>
                </tr>
            </table>
            </form>
        </div>
    </div>
			
@endsection
@section('scripts')
           <script type="text/javascript">
				@php
					include public_path('/js/product/product/addProduct.js');
				@endphp
			</script>
@endsection