<form method="post" action="{!! url('api/verify') !!}" style="text-align: center;margin-top: 20%;">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
    <h1>Xác thực</h1><br>
    <input type="email" style="width: 300px; height: 40px" name="Email" placeholder="Nhập gmail">
    <input type="submit" style="background: #ce252c; color: white; height: 40px" name="" value="Xác thực">
</form>