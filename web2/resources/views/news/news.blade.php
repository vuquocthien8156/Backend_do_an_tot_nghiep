
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-news">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem;margin-top:3%;">
                <h4 class="tag-page-custom" style="color: blue">
                    Quản lý tin tức
                </h4>
            </div>
        </div>
        <div class="row">
        <div class="modal fade" id="update" tabindex="-1" role="dialog" aria-labelledby="update" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="max-width: 800px">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Chỉnh sửa</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>       
                        <div class="modal-body">
                            <form id="form_edit_info" method="POST" action="edit" enctype="multipart/form-data">
                <table border="0px" class=" table-striped w-100" style="margin-left: 0%; font-weight: bold">
                    <tr style="background: #f2f2f2">
                        <td>
                            <label for="other_note1"> Hình ảnh </label>
                            <img id="avatarcollector_edit" style="width: 150px; height: 150px;" class="d-block" :src="imageUrl" />
                        </td>
                        <td>
                            <div class="form-group">
                                <input type="file"@change="onSelectImageHandler" class="form-control" id=""  style="width: 200px;">
                                <input type="input" hidden="true" id="id"  style="width: 200px;">
                            </div>
                        </td>
                    </tr>
                    <tr>
                    <td style="width: 300px;">
                        <div class="form-group">
                            <label for="other_note1"> Tên tin tức </label>
                            <input type="text" class="form-control" id="ten" style="width: 200px;">
                        </div>
                    </td>
                    <td style="width: 300px;">
                        <div class="form-group">
                            <label for="other_note1"> Ngày đăng </label>
                            <input type="date" class="form-control" id="date" style="width: 200px;">
                        </div>
                    </td>
                    
                </tr>
                <tr>
                    <td colspan="2">
                        <div class="form-group">
                            <label for="other_note1"> Nội dung </label>
                            <textarea class="form-control" id="ND" style="width: 700px; height: 200px;"></textarea>
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
        <form method="POST" action="update-img" enctype="multipart/form-data">
                    @csrf
                <div class="modal fade" id="showMore" tabindex="-1" role="dialog" aria-labelledby="showMore" aria-hidden="true">
                    <div class="modal-dialog" role="document" style="width: 470px">
                        <div class="modal-content">
                            <div class="modal-header">
                                Hình phụ
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>                     
                            <div class="modal-body">
                                <div id="showMoreImg" style="width: 100%">
                                    <div v-for="(item,index) in listImg" style="margin-left: 2%; float: left;">
                                        <input type="hidden" name="type" value="1">
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
            <input id="" type="text" class="input-app mr-4"  placeholder="Tên tin tức"  style="width: 200px;margin-bottom: 10px" v-model="name">
            <button class="button-app ml-5" @click="search()">Tìm kiếm</button>
            <table id="tb1" class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
                <thead>
                <tr class="text-center blue-opacity">
                    <th  class="custom-view">STT</th>
                    <th  class="custom-view">Tên tin tức</th>
                    <th  class="custom-view" style="width:500px;">Nội dung</th>
                    <th  class="custom-view">Ngày đăng</th>
                    <th  class="custom-view">Hình ảnh</th>
                    <th  class="custom-view">Hành Động</th>
                </tr>
                </thead>
                <tbody v-cloak>
                    <tr class="text-center" v-for="(item,index) in results.data">
                        <td class="custom-view td-grey"><b>@{{index + 1}}</b></td>
                        <td  class="custom-view"><b>@{{item.ten_tin_tuc}}</b></td>
                        <td  class="custom-view" style="text-align: left;"><b>@{{item.noi_dung}}</b></td>
                        <td  class="custom-view"><b>@{{item.ngay_dang}}</b></td>
                        <td class="custom-view">
                                    <a data-fancybox="gallery" :href="item.pathToResource+'/'+item.hinh_tin_tuc">
                                        <img class="img-responsive" width="50px" height="50px" :src="item.pathToResource+'/'+item.hinh_tin_tuc">
                                    </a>
                                    <button style="cursor: pointer;border: 1px solid transparent; background: transparent;font-weight: bold;" @click="showMore(item.ma_tin_tuc)">+</button>
                        </td>
                        <td class="custom-view"><p>
                            <a href="#" class="btn_edit fa fa-edit" @click="seeMoreDetail(item.ten_tin_tuc, item.ma_tin_tuc, item.noi_dung, item.ngay_dang, item.hinh_tin_tuc);"></a>
                            <span class="btn_remove fa fa-trash" style="cursor: pointer;" @click="deleted(item.ma_tin_tuc)"  data-toggle="tooltip" data-placement="right" title="Xoá"></span><p></td>
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
					include public_path('/js/news/news/news.js');
                    include public_path('/js/product/product/jquery.fancybox.min.js');
				@endphp
			</script>
@endsection