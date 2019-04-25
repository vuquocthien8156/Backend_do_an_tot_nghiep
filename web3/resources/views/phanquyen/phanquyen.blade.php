
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
                                <select v-model="user_per" class="form-control" id="user_per" style="width: 200px;">
                                    @if (count($listUser) > 0)
                                        <option value="">Chọn user</option>
                                         @foreach ($listUser as $item)
                                            @if ($item->ten_vai_tro == null)
                                                <option value="{{ $item->id }}">{{$item->email}}-Chưa có</option>
                                            @else
                                                <option value="{{ $item->id }}">{{$item->email}}-{{$item->ten_vai_tro}}</option>
                                            @endif              
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div>
                                @if (count($listPermission) > 0)
                                    @foreach ($listPermission as $item)
                                        <input type="radio" name="as" v-model="per" value="{{ $item->id }}"> {{$item->ten_vai_tro}}<br>              
                                    @endforeach
                                @endif
                            <div>       
                        </td>
                    </tr>
                </table>
                <div class="modal-footer">
                    <button type="button" @click="permission()" class="button-app" style="margin-right: 75%">Cập nhật</button>
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