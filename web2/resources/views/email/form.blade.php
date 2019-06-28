<form method="post" action="{!! url('api/verify') !!}" style="text-align: center;margin-top: 20%;">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
    <h1>Xác thực</h1><br>
<<<<<<< HEAD
    <input type="email" style="width: 300px; height: 40px" name="email" placeholder="Nhập gmail">
=======
    <input type="email" style="width: 300px; height: 40px" name="Email" placeholder="Nhập gmail">
>>>>>>> 0fa6a49c4b0ca03dac546318aee0dbc660907676
    <input type="submit" style="background: #ce252c; color: white; height: 40px" name="" value="Xác thực">
</form>