
<body data-spy="scroll" data-target="#navbar" data-offset="120" style="background: #f5f5f5">
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <div id="detail">
        <div class="container-fluid">
            @foreach($list as $key)
            <div class="row">
                <div class="col-sm-12">
                     <h3 class="tag-page-custom" style="color: black; text-align: center;margin-top: 8%;margin-bottom: 5%"><b>
                        {{$key->ten_khuyen_mai}}
                </b></h3>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                     <img src="{{$path}}{{$key->hinh_anh}}" width="100%" height="100%">
                </div>
                <div class="col-sm-6">
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
