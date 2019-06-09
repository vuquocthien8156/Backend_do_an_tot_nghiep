@extends('layout.base2')
@section('body-content')
<body data-spy="scroll" data-target="#navbar" data-offset="120" style="background: #f5f5f5">
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div id="detail">
        <div id="menu" class="navbar navbar-inverse navbar-fixed-top" role="navigation" style="background: black; height: 65px;">
            <div class="container">
                <div id="navbar" class="navbar-collapse collapse" style="margin-top: 0.5%">
                    <ul class="nav navbar-nav navbar-right">
                        <li><img src="http://localhost:8888/images/logo1.png" width="50px" height="50px"></li>
                        <li style="margin-right: 15%"><a href="#food-menu">T&T</a></li>
                        <li><a href="#food-menu">MENU</a></li>
                        <li><a href="#special-offser">SPECIAL OFFERS</a></li>
                        <li style="display:none;"><a href="#header"></a></li>
                        <li><a href="#story">STORY</a></li>
                        <li><a href="#reservation">RESERVATION</a></li>
                        <li><a href="#reservation">NEWS</a></li>
                    </ul>
                </div><!--/.navbar-collapse -->
            </div><!-- container -->
        </div><!-- menu -->
        <div class="row mt-5 pt-3">
            @foreach($list as $key)
            <div class="row" style="padding-left: 2rem;margin-top:8%;margin-left:8%">
                <h4 class="tag-page-custom"><b style="color: #33333373">
                        MENU/ {{$key->ten_loai_sp}}/ {{$key->ten}}
                </b></h4>
            </div>
            <div style="width: 100%">
                <div style="padding-left: 2rem;margin-top:0%;margin-left:6%;float: left;width: 40%">
                    <h5 class="tag-page-custom">
                        <img src="{{$path}}{{$key->hinh_san_pham}}" width="500px" height="500px">
                    </h5>
                </div>
                <div style="padding-left: 2rem;margin-top:3%;margin-left:6%;float: left;width: 40%">
                    <div style="margin-top:3%;margin-left:8%;">
                        <h1>{{$key->ten}}</h1>
                    </div>
                    <div style="margin-top:3%;margin-left:8%">
                       <p style="font-size: 20px">{{$key->mo_ta}}</p>
                    </div>
                    <div style="margin-top:3%;margin-left:8%">
                       <p style="font-size: 20px">Ngày ra mắt: {{$key->ngay_ra_mat}}</p>
                    </div>
                    <div style="margin-top:3%;margin-left:8%">
                       <p style="font-size: 20px;color: orange">Giá sản phẩm: {{$key->gia_san_pham}} VNĐ</p>
                    </div>
                    <div style="margin-top:3%;margin-left:8%">
                       <p style="font-size: 20px;color: orange">Size Vừa: {{$key->gia_vua}} VNĐ</p>
                    </div>
                    <div style="margin-top:3%;margin-left:8%">
                       <p style="font-size: 20px;color: orange">Size Lớn: {{$key->gia_lon}} VNĐ</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</body>
@endsection