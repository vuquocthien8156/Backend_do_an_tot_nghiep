
@extends('layout.base')
@section('body-content')
	<div id="login">
            <div class="form-box col-12 m-auto">
                <div class="mx-auto px-sm-5 py-sm-5 my-sm-5" v-cloak>
                    <div >
                    				<p style="font-size: 25px; font-weight: bold; line-height: 40px; text-align: center; color: #ce252c">Đăng nhập</p>
                    		
                    				<div class="form-group" style="margin-left: 38%">
                            			<input id="username" style="height: 40px;width: 300px" name="username" v-model="username" type="text" class="form-control" placeholder="Nhập email" required autofocus>
                        			</div>
                       
                    				<div class="form-group" style="margin-left: 38%">
                            			<input  @keyup.13="login();" id="password" style="height: 40px;width: 300px" v-model="password" type="password" class="form-control" placeholder="Nhập password" name="password" required>
                       				 </div>
                    		
                    				<button id="dn1" @click="login();" class="btn btn-primary-app btn-block" style="background: #ce252c; color: white;width: 300px; margin-left: 38%">Đăng nhập</button>
                    </div>
                </div>
            </div>
    </div>
@endsection
@section('scripts')
           <script type="text/javascript">
				@php
					include public_path('/js/login/login/login.js');
				@endphp
			</script>
@endsection