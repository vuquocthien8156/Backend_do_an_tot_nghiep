
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="login">
            <div class="form-box col-12 m-auto">
                <div class="mx-auto px-sm-5 py-sm-5 my-sm-5" v-cloak>
                    <div class="formlogin">
                    	<table id="table1" style="margin-left: 34%;margin-bottom: 10%;margin-top: 10%;width: 450px;">
                    		<tr>
                    			<td>
                    				<p style="font-size: 25px; font-weight: bold; line-height: 40px; text-align: center; color: #ce252c">Đăng nhập</p>
                    			</td>
                    		</tr>
                    		<tr>
                    			<td>
                    				<div class="form-group">
                            			<input id="username" style="height: 50px;width: 450px" name="username" v-model="username" type="text" class="form-control" placeholder="Nhập email" required autofocus>
                        			</div>
                        		</td>
                    		</tr>
                    		<tr id="password1">
                    			<td>
                    				<div class="form-group">
                            			<input id="password" style="height: 50px;width: 450px" v-model="password" type="password" class="form-control" placeholder="Nhập password" name="password" required>
                       				 </div>
                    			</td>
                    		</tr>
                    		<tr>
                    			<td>
                    				<button id="dn1" @click="login();" class="btn btn-primary-app btn-block" style="background: #ce252c; color: white;width: 450px">Đăng nhập</button>
                            <button id="dn2" @click="login_sdt();" class="btn btn-primary-app btn-block" style="background: #ce252c; color: white;width: 450px">Đăng nhập</button>
                            <button id="btn-tk" @click="hide1();" class="btn btn-primary-app btn-block" style="background: black; color: white;width: 450px">Tài khoản nội bộ</button>
                            <button id="btn-sdt" @click="hide();" class="btn btn-primary-app btn-block" style="background: black; color: white;width: 450px">Đăng nhập bằng SĐT</button>
                                    <a href="{{ route('facebook.login') }}" class="btn btn-primary-app btn-block" style="background: blue; color: white;width: 450px">Đăng nhập bằng Facebook</a>
                              
                                <!--     <a class="btn btn-link" href="{{ URL::to('auth/google') }}">
                                    <i class="fa fa-google-plus-square" aria-hidden="true"></i> Đăng nhập bằng Google
                                </a> -->
                    			</td>
                    		</tr>
                    	</table>
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
<script>
  // This is called with the results from from FB.getLoginStatus().
  function statusChangeCallback(response) {
    console.log('statusChangeCallback');
    console.log(response);
    // The response object is returned with a status field that lets the
    // app know the current login status of the person.
    // Full docs on the response object can be found in the documentation
    // for FB.getLoginStatus().
    if (response.status === 'connected') {
      // Logged into your app and Facebook.
      testAPI();
    } else {
      // The person is not logged into your app or we are unable to tell.
      document.getElementById('status').innerHTML = 'Please log ' +
        'into this app.';
    }
  }

  // This function is called when someone finishes with the Login
  // Button.  See the onlogin handler attached to it in the sample
  // code below.
  function checkLoginState() {
    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });
  }

  window.fbAsyncInit = function() {
    FB.init({
      appId      : '338868070168031',
      xfbml      : true,
      cookie      : true,
      version    : 'v3.2' // The Graph API version to use for the call
    });

    // Now that we've initialized the JavaScript SDK, we call 
    // FB.getLoginStatus().  This function gets the state of the
    // person visiting this page and can return one of three states to
    // the callback you provide.  They can be:
    //
    // 1. Logged into your app ('connected')
    // 2. Logged into Facebook, but not your app ('not_authorized')
    // 3. Not logged into Facebook and can't tell if they are logged into
    //    your app or not.
    //
    // These three cases are handled in the callback function.

    FB.getLoginStatus(function(response) {
      statusChangeCallback(response);
    });

  };

  // Load the SDK asynchronously
  (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "https://connect.facebook.net/en_US/sdk.js";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));

  // Here we run a very simple test of the Graph API after login is
  // successful.  See statusChangeCallback() for when this call is made.
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      console.log('Successful login for: ' + response.name);
      document.getElementById('status').innerHTML =
        'Thanks for logging in, ' + response.name + '!';
    });
  }
</script>
@endsection