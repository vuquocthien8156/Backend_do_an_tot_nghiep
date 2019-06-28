<form method="POST" action="submitChange" style="text-align: center;padding-top: 5%">
	@csrf
	<h2>ĐỔi MẬT KHẨU</h2><br>
	<table style="margin-left: 35%">
		<tr>
			<td>
				<b>Gmail</b>
			</td>
			<td>
				<input type="type" id="Email" name="Email" placeholder="Email" value="{{session()->get('emailMobile')}}" readonly="true" style="margin-bottom: 2%;width: 200px;height: 40px; border-radius: 3px; margin-left: 5%"><br>

			</td>
		</tr>
		<tr>
			<td>
				<b>Mật khẩu mới</b>
			</td>
			<td>

				<input type="password" id="newPass" name="newPass" placeholder="Mật khẩu mới" style="margin-bottom: 2%;width: 200px;height: 40px; border-radius: 3px; margin-left: 5%"><br>
			</td>
		</tr>
		<tr>
			<td>
				<b>Nhập lại khẩu mới</b>
			</td>
			<td>
				<input type="password" id="reNewPass" name="reNewPass" placeholder="Nhập lại khẩu mới" style="margin-bottom: 2%;width: 200px;height: 40px; border-radius: 3px; margin-left: 5%"><br>
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