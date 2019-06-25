
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
    <div id="manage-add-news">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem;margin-top:3%">
                <h4 class="tag-page-custom" style="color: blue">
                    Thêm tin tức
                </h4>
            </div>
        </div>
        <div class="container pl-0 pr-0 pb-5">
            <div class="w-100" style="min-height: 150px">
                <div class="form-box col-12 m-auto">
                    <div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 33rem;border: 1px solid black">
                        <form id="add" method="POST" action="/news/add-new" enctype="multipart/form-data">
                            @csrf
                            <table>
                                <tr>
                                    <td>
                                        <label for="model" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Hình Ảnh</label><br>
                                        <i style="color: red">*Hình đầu tiên là hình chính</i>
                                        <input id="_imagesInput" name="files[]" type="file" multiple style="width: 75px;" title="Chọn ảnh">
                                        <div id="_displayImages">
                                            <div>
                                                <ul id="frames" class="frames">
                                                    
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="other_note1"><b>Tên tin tức</b> </label>
                                             <input type="text" required="" class="form-control" id="" name ="ten" style="width: 200px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="other_note1"> <b>Ngày đăng</b> </label>
                                            <input type="date" required="" class="form-control" id="" name ="NĐ" style="width: 200px;">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="form-group">
                                            <label for="other_note1"> <b>Nội dung</b> </label>
                                            <textarea type="text" required="" name="ND" class="form-control" id=""  style="width: 400px;height: 200px"></textarea>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        <div class="form-group">
                            <input type="submit" class="button-app" style="margin-right: 75%" value="Lưu">
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
        $(document).ready(function() {
            $('#_uploadImages').click(function() {
                $('#_imagesInput').click();
            });

            $('#_imagesInput').on('change', function() {
                $("#frames").text('');
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
                                output.innerHTML = output.innerHTML +"<div style=\"float:left;width:50px;margin-right:5px;\" class=\"carousel-item carousel-item-avatar active\">"+"<img width='50px;' height='50px;' style='margin-left:2%;margin-top:2%' src='" + picFile.result + "'" + "title=''/>"
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
                    include public_path('/js/news/news/addNews.js');
                @endphp
            </script>
@endsection