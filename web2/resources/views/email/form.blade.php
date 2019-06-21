<form method="post" action="{!! url('lien-he') !!}">
    <input type="hidden" name="_token" value="{!! csrf_token() !!}">
    <input type="text" name="email">
    <input type="submit" name="">
</form>