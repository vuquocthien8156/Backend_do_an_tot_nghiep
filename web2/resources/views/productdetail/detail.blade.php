
<body data-spy="scroll" data-target="#navbar" data-offset="120" style="background: #f5f5f5">
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div id="detail">
        <div class="container-fluid">
         @foreach($list as $key)
            <div class="row">
                <div class="col-sm-12">
                    <h5 class="tag-page-custom"><b style="color: #33333373; margin-top: 8%">
                        MENU/ {{$key->ten_loai_sp}}/ {{$key->ten}}
                </b></h5>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                     <img src="{{$path}}{{$key->hinh_san_pham}}" width="100%" height="100%">
                </div>
                <div class="col-sm-6">
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