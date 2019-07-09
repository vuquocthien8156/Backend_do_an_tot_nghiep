@extends('layout.base2')
@section('body-content')
<body data-spy="scroll" data-target="#navbar" data-offset="120" style="background: #f5f5f5">
    <div id="detail">
        <div class="container">
            @foreach($list as $key)
            <div class="row">
                <div class="col-sm-12">
                    <h3 class="tag-page-custom" style="color: black;text-align: center;margin-top: 8%;margin-bottom: 5%"><b>
                        {{$key->ten_tin_tuc}}
                </b></h3>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6">
                     <img src="{{$key->path}}{{$key->hinh_tin_tuc}}" width="100%" height="100%">
                </div>
                <div class="col-sm-6">
                     <div style="margin-top:3%;margin-left:8%">
                        <u><h3>Nội dung</h3></u>
                    </div>
                    <div style="margin-top:3%;margin-left:8%">
                       <p style="font-size: 20px">{{$key->noi_dung}}</p>
                    </div>
                </div>
            </div> 
            @endforeach   
        </div>
    </div>
</body>
@endsection