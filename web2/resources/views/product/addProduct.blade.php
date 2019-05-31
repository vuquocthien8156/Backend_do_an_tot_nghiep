
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-add-product">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem;margin-top:3%">
                <h4 class="tag-page-custom">
                    Thêm sản phẩm
                </h4>
            </div>
        </div>
        <div class="container pl-0 pr-0 pb-5">
            <div class="w-100" style="min-height: 150px">
                <div class="form-box col-12 m-auto">
                    <div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 33rem;">
                        <form id="add" method="POST" action="add-new" enctype="multipart/form-data">
                            <table>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="other_note1"> Tên sản phẩm </label>
                                            <input type="text" class="form-control" id="" v-model="ten" style="width: 200px;">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group" style="margin-left: 15%">
                                            <label for="other_note1"> Tên sản phẩm </label>
                                            <input type="text" class="form-control" id="" v-model="ten" style="width: 200px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="other_note1"> Mã sản phẩm </label>
                                            <input type="text" class="form-control" id="" v-model="ma"  style="width: 200px;">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group" style="margin-left: 15%">
                                            <label for="other_note1"> Giá gốc </label>
                                            <input type="text" class="form-control" id="" v-model="gia_goc" style="width: 200px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="other_note1"> Giá size vừa </label>
                                            <input type="text" class="form-control" id="" v-model ="gia_size_vua" style="width: 200px;">
                                        </div>    
                                    </td>
                                    <td>
                                        <div class="form-group" style="margin-left: 15%">
                                            <label for="other_note1"> Giá size lớn </label>
                                            <input type="text" class="form-control" id="" v-model ="gia_size_lon" style="width: 200px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
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
                                    <td>
                                        <div class="form-group" style="margin-left: 15%">
                                            <label for="other_note1"> Ngày ra mắt </label>
                                            <input type="date" class="form-control" v-model="ngay_ra_mat" id=""  style="width: 200px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="other_note1">Mô tả </label>
                                            <textarea type="text" v-model="mo_ta" class="form-control" id=""  style="width: 200px;"></textarea>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group" style="margin-left: 15%">
                                            <label for="other_note1"> Hình ảnh </label>
                                            <input type="file"@change="onSelectImageHandler" class="form-control" id=""  style="width: 200px;">
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        <div class="form-group">
                            <button type="button" @click="luu();" class="button-app" style="margin-right: 75%">lưu</button>
                        </div>
            </form>    
                    </div>
                </div>
            </div>
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