
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-discount">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem;margin-top:3%;">
                <h4 class="tag-page-custom" style="color: blue">
                    Quản lý tài khoản
                </h4>
            </div>
        </div>
        <div class="row">
        <div class="modal fade" id="update" tabindex="-1" role="dialog" aria-labelledby="update" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 600px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Chỉnh sửa</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>       
                        <div class="modal-body">
                            <form id="form_edit_info" name="formEdit" method="POST" action="edit" enctype="multipart/form-data" style="font-weight: bold">
                        <label for="other_note1"> Hình ảnh </label>
                        <img id="avatarcollector_edit" style="width: 100px; height: 100px;" class="d-block" :src="imageUrl" />
            
                        <div class="form-group">
                            <input type="file"@change="onSelectImageHandler" class="form-control" id=""  style="width: 200px;">
                            <input type="input" hidden="true" id="id" required=""  style="width: 200px;">
                        </div>
                
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Mã code </label>
                            <input type="text" class="form-control" id="ma" required="" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" style="float: left; margin-left: 5%">
                            <label for="other_note1"> Tên khuyến mãi </label>
                            <input type="text" class="form-control" id="ten" required="" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Mô tả </label>
                            <input type="text" class="form-control" id="MT" required="" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" v-if="type == 1 || type == 2" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Số phần trăm </label>
                            <input type="number" class="form-control" id="SPT" required="" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" v-if="type == 2 || type == 4" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Số tiền </label>
                            <input type="number" class="form-control" id="ST" required="" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Số sản phẩm qui định </label>
                            <input type="number" class="form-control" required="" id="SSPQD" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" v-if="type == 2 || type == 4" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Số tiền qui định tối thiểu </label>
                            <input type="number" class="form-control" required="" id="STQDTT"  style="width: 200px;">
                        </div>
                    
                    
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Ngày bắt đầu </label>
                            <input type="date" class="form-control" id="NBD" required="" style="width: 200px;">
                        </div>
                   
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Ngày kết thúc </label>
                            <input type="date" class="form-control" id="NKT" required="" style="width: 200px;">
                        </div>
                   
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Giới hạn số code </label>
                            <input type="number" class="form-control" id="GHSC" required="" style="width: 200px;">
                        </div>

                        <div class="form-group" v-if="type == 3 || type == 5" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Số sản phẩm tặng kèm </label>
                            <input type="number" class="form-control" id="SSPTK" required="" style="width: 200px;">
                        </div>

                        <div class="form-group" v-if="type == 3 || type == 5" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Sản phẩm </label>
                            <select id="SP" class="form-control" style="width: 200px;">
                                <option value="">Chọn loại sản phẩm</option>
                                    @if (count($list) > 0)
                                        @foreach ($list as $item)
                                            <option value="{{ $item->ma_so }}" > {{$item->ten}}</option>
                                        @endforeach
                                     @endif
                            </select>
                        </div>
                    
            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="button-app" @click="edit">Sửa</button>
                            <button type="button" class="button-app ml-5 float-right" data-dismiss="modal">Đóng</button>
                        </div>
                        </div>
                    </div>
        </div>
        <div class="modal fade" id="showMore" tabindex="-1" role="dialog" aria-labelledby="showMore" aria-hidden="true">
            <form method="POST" action="update-img" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-dialog" role="document" style="width: 470px">
                        <div class="modal-content">
                            <div class="modal-header">
                                Hình phụ
                                <input  type="hidden" name="type" value="4">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>                     
                            <div class="modal-body">
                                <div id="showMoreImg" style="width: 100%">
                                    <div v-for="(item,index) in listImg" style="margin-left: 2%; float: left;">
                                       <img v-if="item.url != null && item.url != ''" class="img-responsive" width="100px" height="100px" :src="item.pathToResource+'/'+item.url">
                                    </div>
                                </div>
                                    <div>
                                        <label for="model" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold" style="margin-left:2% ">Hình Ảnh</label><br>
                                            <input id="_imagesInput" name="files[]" type="file" multiple style="width: 75px;margin-left:2% " title="Chọn ảnh">
                                            <input id="id_update" name="id_update" type="hidden">
                                            <div id="_displayImages">
                                                <div>
                                                    <ul id="frames" class="frames">
                                                        
                                                    </ul>
                                                </div>
                                            </div>
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <input type="submit" class="button-app" value="Lưu">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal"> Huỷ bỏ </button>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
        <div id="body" class="set-row background-contact w-100">
            <select class="input-app mr-4" style="width: 200px;margin-bottom: 10px" @change="search()" v-model="type">
                <option value="1">Khuyến mãi theo % (ĐH)</option>
                <option value="2">Khuyến mãi theo % và số tiền qui định(ĐH)</option>
                <option value="3">Khuyến mãi theo số sản phẩm qui định(ĐH)</option>
                <option value="4">Khuyến mãi theo số tiền và số sản phẩm qui định(ĐH)</option>
                <option value="5">Khuyến mãi theo số sản phẩm qui định(SP)</option>
            </select>
            <!-- <button class="button-app ml-5 float-right" @click="search()">Tìm kiếm</button> -->
            <table id="tb1" class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
                <thead>
                <tr class="text-center blue-opacity">
                    <th  class="custom-view">STT</th>
                    <th  class="custom-view">Mã code</th>
                    <th  class="custom-view">Tên khuyến mãi</th>
                    <th  class="custom-view">Mô tả</th>
                    <th  class="custom-view" v-if="type == 1 || type == 2">Số phần trăm</th>
                    <th  class="custom-view" v-if="type == 2 || type == 4">Số tiền</th>
                    <th  class="custom-view">Số sản phẩm quy định</th>
                    <th  class="custom-view" v-if="type == 3 || type == 5">Mã sản phẩm</th>
                    <th  class="custom-view" v-if="type == 2 || type == 4">Số tiền qui định</th>
                    <th  class="custom-view">Giới hạn số code</th>
                    <th  class="custom-view" v-if="type == 3 || type == 5">Số sản phẩm tặng kèm</th>
                    <th  class="custom-view">Ngày bắt đầu</th>
                    <th  class="custom-view">Ngày kết thúc</th>
                    <th  class="custom-view">Hình ảnh</th>
                    <th  class="custom-view">Trạng thái</th>
                    <th  class="custom-view">Hành Động</th>
                </tr>
                </thead>
                <tbody v-cloak>
                    <tr class="text-center" v-for="(item,index) in results.data">
                        <td class="custom-view td-grey"><b>@{{index + 1}}</b></td>
                        <td  class="custom-view"><b>@{{item.ma_code}}</b></td>
                        <td  class="custom-view"><b>@{{item.ten_khuyen_mai}}<b></td>
                        <td  class="custom-view"><b>@{{item.mo_ta}}</b></td>
                        <td  class="custom-view" v-if="type == 1 || type == 2" width="150bx"><b>@{{item.so_phan_tram}}</b></td>
                        <td class="custom-view" v-if="type == 2 || type == 4"><b>@{{item.so_tien}}</b></td>
                        <td  class="custom-view" width="150px"><b>@{{item.so_sp_qui_dinh}}</b></td>
                        <td  class="custom-view" v-if="type == 3 || type == 5" width="150px"><b>@{{item.ma_san_pham}}</b></td>
                        <td class="custom-view" v-if="type == 2 || type == 4"><b>@{{item.so_tien_qui_dinh_toi_thieu}}</b></td>
                        <td class="custom-view"><b>@{{item.gioi_han_so_code}}</b></td>
                        <td  class="custom-view" v-if="type == 3 || type == 5" width="150px"><b>@{{item.so_sp_tang_kem}}</b></td>
                        <td  class="custom-view" width="150px"><b>@{{item.ngay_BD}}</b></td>
                        <td  class="custom-view" width="150px"><b>@{{item.ngay_KT}}</b></td>
                        <td class="custom-view">
                                     <a data-fancybox="gallery" :href="item.pathToResource+'/'+item.hinh_anh">
                                        <img class="img-responsive" width="50px" height="50px" :src="item.pathToResource+'/'+item.hinh_anh">
                                    </a>
                                    <button style="cursor: pointer;border: 1px solid transparent; background: transparent;font-weight: bold;" @click="showMore(item.ma_khuyen_mai)">+</button>
                        </td>
                        <td  class="custom-view">
                                <span href="#" v-if="item.da_xoa == 0" class="btn_edit fa fa-check" style="color: green"></span>
                                <span href="#" v-if="item.da_xoa == 1" class="btn_edit fa fa-times" style="color: red"></span>
                            </td>
                        <td class="custom-view"><p>
                            <a href="#" class="btn_edit fa fa-edit" @click="seeMoreDetail(item.ma_code,item.ten_khuyen_mai,item.mo_ta,item.so_phan_tram,item.so_tien,item.so_sp_qui_dinh,item.so_tien_qui_dinh_toi_thieu,item.gioi_han_so_code,item.ngay_bat_dau,item.ngay_ket_thuc,item.hinh_anh, item.ma_khuyen_mai,item.so_sp_tang_kem, item.ma_san_pham);"></a>
                            <span class="btn_remove fa fa-trash" style="cursor: pointer;" @click="deleted(item.ma_khuyen_mai)"  data-toggle="tooltip" data-placement="right" title="Xoá"></span><p></td>
                    </tr>
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
        $(document).ready(function() {
            $('#_uploadImages').click(function() {
                $('#_imagesInput').click();
            });

            $('#_imagesInput').on('change', function() {
                $("#frames").text('');
                $("#showMoreImg").text('');
                handleFileSelect();
            });

            function handleFileSelect() {
                if (window.File && window.FileList && window.FileReader) {
                    var files = event.target.files;
                    if (files.length > 3) {
                        bootbox.alert("Chỉ được chọn 3 hình");
                        files = [];
                        return false;
                    }
                    var output = document.getElementById("frames");
                    var arrFilesCount = [];
                    for (var i = 0; i < files.length; i++) {
                        arrFilesCount.push(i);
                        var file = files[i];
                        var picReader = new FileReader();
                        picReader.addEventListener("load", function (event) {
                            var picFile = event.target;
                                output.innerHTML = output.innerHTML +"<div style=\"float:left;width:100px;margin-left:2%;\" class=\"carousel-item carousel-item-avatar active\">"+"<img width='100px;' height='100px;' style='margin-left:%;margin-top:2%' src='" + picFile.result + "'" + "title=''/>"
                                                +  "</div>";
                                                $(".btn_remove_image").click(function() {
                                $(this).parent(".carousel-item").remove();
                                $("#frames").val('');
                            });         
                        });

                        picReader.readAsDataURL(file);
                    }
                 } else {
                    console.log("Your browser does not support File API");
                 }
        }
        });     
    </script>
           <script type="text/javascript">
				@php
					include public_path('/js/discount/discount/discount.js');
                    include public_path('/js/product/product/jquery.fancybox.min.js');
				@endphp
			</script>
@endsection