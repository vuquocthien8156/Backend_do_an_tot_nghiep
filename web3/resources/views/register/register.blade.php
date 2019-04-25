
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="register">
            <div class="form-box col-12 m-auto">
                <div class="mx-auto px-sm-5 py-sm-5 my-sm-5">
                    <div class="formlogin">
                        <p style="font-size: 25px;margin-top: 10%;font-weight: bold; line-height: 40px; text-align: center; color: #ce252c">Register</p>
                        <div style="width: 100%;margin-bottom: 50%">
                            <div style="float: left;width: 50%;">
                                <p style="margin-left: 49%;color: red">*Profile account</p>
                                <table style="margin-left: 50%;margin-top: 1%;width: 300px;" border="0px">
                            <tr>
                                <td>
                                    Username
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input style="margin-top: 15px;" id="username" name="username" v-model="username" type="text" class="form-control" placeholder="your account" required autofocus>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Password
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input style="margin-top: 15px;" id="password" v-model="password" type="password" class="form-control" placeholder="your password" name="password" required>
                                     </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Re-Password
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input style="margin-top: 15px;" id="re-password" v-model="re_password" type="password" class="form-control" placeholder="re-password" name="re_password" required>
                                     </div>
                                </td>
                                <tr>
                                    <td colspan="2">
                                        <div>
                                        <strong id="error2" style="display: none;color: red">* Account had already</strong>
                                        <strong id="error6" style="display: none;color: red">* Re-password is wrong</strong>
                                        <strong id="error1" style="display: none;color: red">* Not null</strong>
                                        <strong id="error7" style="display: none;color: red">* Email is wrong</strong>
                                    </div>
                                    </td>
                                </tr>
                            </tr>
                        </table>
                            </div>
                            <div style="float: left;width: 50%;">
                                <p style="margin-left: 5%;color: red">*Profile user</p>
                        <table style="margin-left: 6%;width: 300px;">
                            <tr>
                                <td>
                                   Full name
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input style="margin-top: 15px;" id="name" name="name" v-model="name" type="text" class="form-control" placeholder="your name" required autofocus>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Phone
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input style="margin-top: 15px;" id="phone" v-model="phone" type="text" class="form-control" placeholder="your phone" name="phone" required>
                                     </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    Gender
                                </td>
                                <td>
                                    <div class="form-group">
                                        <select class="form-control" v-model="gender">
                                            <option value="">Gender</option>
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                            <option value="Others">Others</option>
                                        </select>
                                     </div>
                                </td>
                            </tr>
                             <tr>
                                <td>
                                    Day of birth
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input style="margin-top: 15px;" id="birthday" v-model="birthday" type="date" class="form-control" name="birthday" required>
                                     </div>
                                </td>
                            </tr>
                             <tr>
                                <td>
                                    Address
                                </td>
                                <td>
                                    <div class="form-group">
                                        <textarea style="margin-top: 15px;" id="address" v-model="address" type="text" class="form-control" placeholder="your address" name="address" required>
                                            
                                        </textarea>
                                        
                                     </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button @click="register();" class="btn btn-primary-app btn-block" style="background: #ce252c; color: white;width: 150px;margin-left: 50%">Register</button>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <div>
                                        <strong id="error3" style="display: none;color: red">* Account had already</strong>
                                        <strong id="error4" style="display: none;color: red">* Not null</strong>
                                        <strong id="error5" style="display: none;color: red">* Phone is wrong</strong>
                                    </div>
                                </td>
                            </tr>
                        </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
			
@endsection
@section('scripts')
           <script type="text/javascript">
				@php
					include public_path('/js/register/register/register.js');
				@endphp
			</script>
@endsection