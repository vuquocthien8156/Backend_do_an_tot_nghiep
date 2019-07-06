
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="verify">
        <div class="row mt-5 pt-3">
            <div style="padding-left: 2rem;margin-top:3%;">
                <h4 class="tag-page-custom" style="color: blue">
                    Xác thực
                </h4>
            </div>
        </div>
        <div class="row">
            <form method="post" action="{!! url('verify') !!}">
                <input type="hidden" name="_token" value="{!! csrf_token() !!}">
                <div class="row" style="font-size: 25px; width: 100%;font-weight: bold; line-height: 5; text-align: center; color: #ce252c;margin-left: 63%">Xác thực</div>
                <div class="form-group" style="margin-left: 38%;width: 1000px">
                    <input id="username" style="height: 40px;width: 300px;margin-left: 15%" name="Email" type="email" class="form-control" readonly="true" value="{{session()->get('email')}}" placeholder="Nhập email" required autofocus>
                </div>
                <input style="background: #ce252c; color: white;width: 300px; height: 40px; margin-left: 53%" type="submit" name="" value="Xác thực">
            </form>
                       
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
@endsection