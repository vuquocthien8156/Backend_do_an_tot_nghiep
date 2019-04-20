
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="permission">
        <div id="body" class="form-box col-12 m-auto" style="margin-bottom: 50%;margin-top: 3%;margin-left: 2%;margin-right: 2%">
            <div id="edit" style="width: 35%;display: block;margin-bottom: 10%;margin-top: 10px;margin-left: 33%">
            <div style="border: 1px solid red">
                <div class="form-group" style="text-align: center;">
                    <h4 for="child_name1" style="color: blue;">Phân quyền</h4>
                </div>
                <table border="0px" style="margin-left: 2%">
                    <tr class="form-group">
                        <td>
                            <div class="form-group">
                                <label for="child_name1">Tài khoản</label>
                                <input type="text" class="form-control" id="email" style="width: 200px;">
                            </div>
                            <div>
                                <input type="radio" name="">
                                <input type="radio" name="">
                            <div>       
                        </td>
                    </tr>
                </table>
                <div class="modal-footer">
                    <button type="button" @click="edit()" class="button-app" style="margin-right: 75%">Sửa</button>
                    <button type="button" @click="exit()" class="button-app ml-5 float-right">Thoát</button>
                </div>
            </div>
        </div>
        </div>
    </div>
			
@endsection
@section('scripts')
           <script type="text/javascript">
				@php
					include public_path('/js/permission/permission/permission.js');
				@endphp
			</script>
@endsection