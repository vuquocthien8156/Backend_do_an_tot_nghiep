
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="ChangPasswordAdmin">
        <form method="POST" action="submitChange" style="text-align: left;padding-top: 10%">
    @csrf
    <h2 style="margin-left: 40%"><b>ĐỔi MẬT KHẨU</b></h2><br>
    <table style="margin-left: 35%">
        <tr>
            <td>
                <b>Gmail</b>
            </td>
            <td>
                <input type="type" id="Email" name="Email" placeholder="Email" value="{{session()->get('email')}}" readonly="true" style="margin-bottom: 2%;width: 200px;height: 40px; border-radius: 3px"><br>
            </td>
        </tr>
        <tr>
            <td>
                <b>Mật khẩu mới</b>
            </td>
            <td>
                <input type="password" id="newPass" name="newPass" placeholder="Mật khẩu mới" style="margin-bottom: 2%;width: 200px;height: 40px; border-radius: 3px"><br>
            </td>
        </tr>
        <tr>
            <td>
                <b>Nhập lại khẩu mới</b>
            </td>
            <td>
                <input type="password" id="reNewPass" name="reNewPass" placeholder="Nhập lại khẩu mới" style="margin-bottom: 2%;width: 200px;height: 40px; border-radius: 3px"><br>
            </td>
        </tr>
        <tr>
            <td colspan="2"><input type="submit" style="margin-left:50%;width: 70px;height: 40px; border-radius: 3px" name=""></td>
        </tr>
    </table>
</form>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script type="text/javascript">
        $(document).ready(function() {
            $('#reNewPass').focusout(function() {
                if ($('#reNewPass').val() != $('#newPass').val()) {
                    alert('Không khớp mật khẩu');
                }
            });
        });     
    </script>
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