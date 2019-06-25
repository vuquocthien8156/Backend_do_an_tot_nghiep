
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-add-product">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem;margin-top:3%">
                <h4 class="tag-page-custom" style="color: blue">
                    Thêm Khuyến mãi
                </h4>
            </div>
        </div>
        <div class="container pl-0 pr-0 pb-5">
            <div class="w-100" style="min-height: 150px">
                <div class="form-box col-12 m-auto">
                    <div class="mx-auto px-sm-5 py-sm-3 form-box-shadow" style="max-width: 600px;border: 1px solid black;">
                        <form id="add" method="POST" action="/discount/add-new" enctype="multipart/form-data" style="font-weight: bold;">
                            @csrf
                            Chọn loại khuyến mãi <select class="input-app mr-4" style="width: 200px;margin-bottom: 10px" v-model="type" name="type">
                <option value="1">Khuyến mãi theo % (ĐH)</option>
                <option value="2">Khuyến mãi theo % và số tiền qui định(ĐH)</option>
                <option value="3">Khuyến mãi theo số sản phẩm qui định(ĐH)</option>
                <option value="4">Khuyến mãi theo số tiền và số sản phẩm qui định(ĐH)</option>
                <option value="5">Khuyến mãi theo số sản phẩm qui định(SP)</option>
            </select>
                            <label for="model" class="col-md-4 p-0 justify-content-start align-items-start font-weight-bold">Hình Ảnh</label><br>
                                        <i style="color: red">*Hình đầu tiên là hình chính</i>
                                        <input id="_imagesInput" name="files[]" type="file" multiple style="width: 75px;" title="Chọn ảnh">
                                        <div id="_displayImages" style="margin-bottom: 15%">
                                            <div>
                                                <ul id="frames" class="frames">
                                                    
                                                </ul>
                                            </div>
                                        </div>
                
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Mã code </label>
                            <input type="text" class="form-control" name="ma" required="" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" style="float: left; margin-left: 5%">
                            <label for="other_note1"> Tên khuyến mãi </label>
                            <input type="text" class="form-control" name="ten" required="" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Mô tả </label>
                            <input type="text" class="form-control" name="MT" required="" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" v-if="type == 1 || type == 2" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Số phần trăm </label>
                            <input type="number" class="form-control" name="SPT" required="" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" v-if="type == 2 || type == 4" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Số tiền </label>
                            <input type="number" class="form-control" name="ST" required="" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Số sản phẩm qui định </label>
                            <input type="number" class="form-control" required="" name="SSPQD" style="width: 200px;">
                        </div>
                    
                        <div class="form-group" v-if="type == 2 || type == 4" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Số tiền qui định tối thiểu </label>
                            <input type="number" class="form-control" required="" name="STQDTT"  style="width: 200px;">
                        </div>
                    
                    
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Ngày bắt đầu </label>
                            <input type="date" class="form-control" name="NBD" required="" style="width: 200px;">
                        </div>
                   
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Ngày kết thúc </label>
                            <input type="date" class="form-control" name="NKT" required="" style="width: 200px;">
                        </div>
                   
                        <div class="form-group" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Giới hạn số code </label>
                            <input type="number" class="form-control" name="GHSC" required="" style="width: 200px;">
                        </div>

                        <div class="form-group" v-if="type == 3 || type == 5" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Số sản phẩm tặng kèm </label>
                            <input type="number" class="form-control" name="SSPTK" required="" style="width: 200px;">
                        </div>

                        <div class="form-group" v-if="type == 3 || type == 5" style="float: left;margin-left: 5%">
                            <label for="other_note1"> Sản phẩm </label>
                            <select name="SP" class="form-control" style="width: 200px;">
                                <option value="">Chọn loại sản phẩm</option>
                                    @if (count($list) > 0)
                                        @foreach ($list as $item)
                                            <option value="{{ $item->ma_so }}" > {{$item->ten}}</option>
                                        @endforeach
                                     @endif
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="submit" class="button-app" style="margin-left: 5%; margin-top: 5%" value="Lưu"></button>
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
					include public_path('/js/discount/discount/addDiscount.js');
				@endphp
			</script>
@endsection