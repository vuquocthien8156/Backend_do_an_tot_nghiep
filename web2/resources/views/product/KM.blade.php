@extends('layout.base2')
@section('body-content')
<body data-spy="scroll" data-target="#navbar" data-offset="120" style="background: #f5f5f5">
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div id="detail">
        <div>
            @foreach($list as $key)
            <div class="row" style="padding-left: 2rem;margin-top:8%;margin-left:40%;margin-bottom: 2%">
                <h3 class="tag-page-custom" style="color: black"><b>
                        {{$key->ten_khuyen_mai}}
                </b></h3>
            </div>
            <div style="width: 100%" class="row">
                <div class="col-sm-6" style="padding-left: 2rem;margin-top:0%;margin-left:0%;float: left;width: 40%">
                    <h5 class="tag-page-custom">
                        <img src="{{$path}}{{$key->hinh_anh}}" width="100%" height="100%">
                    </h5>
                </div>
                <div class="col-sm-6" style="padding-left: 2rem;margin-top:3%;margin-left:0%;float: left;width: 40%">
                    <div style="margin-top:3%;margin-left:8%">
                        <u><h3>Mô tả</h3></u>
                    </div>
                    <div style="margin-top:3%;margin-left:8%">
                       <p style="font-size: 20px">{{$key->mo_ta}}</p>
                    </div>
                </div>
            </div>@endforeach
        </div>
    </div>
</body>
@endsection