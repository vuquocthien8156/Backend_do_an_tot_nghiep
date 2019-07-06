
@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-branch" style="background: white; margin-top: 5%">
        <div class="row mt-5 pt-3 pb-4">
            <div class="pl-4">
                <h4 class="tag-page-custom">
                    <a class="tag-title-show" style="text-decoration: none;" href="#"> CHI NHÁNH </a>                
                </h4>
            </div>
        </div>
        <div class="container pl-0 pr-0 pb-1">
            <div class="w-100" style="min-height: 150px">
                <div class="row pl-0">
                    <div class="col-4 pl-0" style="border-right: 1px solid;">
                        <ul class="nav nav-tabs mb-4">
                            <li class="nav-item">
                                <a class="nav-link active" id="list_branch" href="#">Danh sách</a>
                            </li> 
                            <li class="nav-item">
                                <a class="nav-link" id="add_branch" href="#">Thêm mới</a>
                            </li>
                        </ul>

                        <div class="list-branch-form d-block">
                            <a class="btn btn-primary mb-3" data-toggle="collapse" href="#collapseTableBranch" role="button" aria-expanded="false" aria-controls="collapseTableBranch">
                                <i class="fas fa-align-justify"> </i> Danh sách
                            </a>
                            {{-- Chỉnh sửa --}}
                            <div class="mb-3 update-branch-form d-none">
                                <div class="text-center"> <h4>CHỈNH SỬA CHI NHÁNH</h4> </div>
                                <div class="form-group row">
                                    <label for="name_branch_update" class="col-sm-4 col-form-label">Tên Chi Nhánh</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" placeholder="Tên chi nhánh" v-model="name_branch_update" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="lat_update" class="col-sm-4 col-form-label">Tọa độ X</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="lat_update" class="form-control" placeholder="Latitude" readonly required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="long_update" class="col-sm-4 col-form-label">Tọa độ Y</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="long_update" class="form-control" placeholder="Longitude" readonly required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                <label for="address" class="col-sm-4 col-form-label">Khu vực</label>
                                <div class="col-sm-8">
                                    <select name="education_level" id="place_update" class="input-app mr-4"style="width: 239px; height: 33px; cursor: pointer;">
                                        <option value="">Khu vực</option>
                                        @if(count($list) > 0)
                                            @foreach ($list as $value)
                                                <option value="{{$value->ma_khu_vuc}}">{{$value->ten_khu_vuc}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                                <div class="form-group row">
                                    <label for="address_update" class="col-sm-4 col-form-label">Địa chỉ</label>
                                    <div class="col-sm-8">
                                        <input type="text" id="address1" class="form-control" placeholder="Địa chỉ" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label for="phone_branch_update" class="col-sm-4 col-form-label">Số điện thoại</label>
                                    <div class="col-sm-8">
                                        <input type="text"v-model="phone_branch_update" class="form-control" placeholder="Số điện thoại" required>
                                    </div>
                                </div>
                                <button class="button-app" @click="updateBranch()"> Lưu </button>
                            </div>
                            
                            <div class="collapse show position-relative" id="collapseTableBranch">
                                <table class="table table-bordered table-striped w-100 d-block" style="line-height: 1.4; overflow: auto;">
                                    <thead style="">
                                        <tr class="text-center blue-opacity">
                                            <th class="custom-view"> Tên Chi Nhánh </th>
                                            <th class="custom-view"> Địa chỉ </th>
                                            <th class="custom-view"> Khu vực </th>
                                            <th class="custom-view w-25" > Sửa/Xoá </th>
                                        </tr>
                                    </thead>
                                    <tbody id="">
                                        <tr class="text-center" v-for="(item,index) in results.data" style="font-weight:bold">
                                                <td class="custom-view td-grey"> @{{item.ten }} </td>
                                                <td class="custom-view text-left"> @{{item.dia_chi }} </td>
                                                <td class="custom-view text-right"> @{{item.ten_khu_vuc }} </td>
                                                <td class="custom-view">
                                                    <span v-if="item.da_xoa == 0" class="btn_edit fas fa-edit" @click="getInfoBranch(item.ten,  item.sdt,  item.dia_chi ,item.latitude, item.longitude,item.ma_chi_nhanh, item.ma_khu_vuc)" >        
                                                    </span>
                                                    <span v-if="item.da_xoa == 0" class="btn_remove fas fa-times" @click="deleteBranch(item.ma_chi_nhanh,item.da_xoa)"></span>
                                                    <span v-if="item.da_xoa == 1" class="btn_edit fas fa-undo" style="cursor: pointer;" @click="deleteBranch(item.ma_chi_nhanh,item.da_xoa)"  data-toggle="tooltip" data-placement="right" title="Phục hồi"></span></td>
                                                </td>
                                            <tr>
                                    </tbody>   
                                </table>
                                <div class="col-12" style="margin-left: 0%">
                                    <pagination :data="results" @pagination-change-page="search"></pagination> 
                                </div>
                            </div>
                        </div>
                        {{-- Thêm mới --}}
                        <div class="mb-3 add-branch-form d-none">
                            <div class="text-center"> <h4>THÊM CHI NHÁNH</h4> </div>
                            <div class="form-group row">
                                <label for="inputPassword" class="col-sm-4 col-form-label">Tên Chi Nhánh</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" placeholder="Tên chi nhánh" v-model="name_branch" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="lat" class="col-sm-4 col-form-label">Tọa độ X</label>
                                <div class="col-sm-8">
                                    <input type="text" id="lat" class="form-control" placeholder="Latitude" readonly required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="long" class="col-sm-4 col-form-label">Tọa độ Y</label>
                                <div class="col-sm-8">
                                    <input type="text" id="long" class="form-control" placeholder="Longitude" readonly required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="address" class="col-sm-4 col-form-label">Khu vực</label>
                                <div class="col-sm-8">
                                    <select name="education_level" id="place" class="input-app mr-4"style="width: 239px; height: 33px; cursor: pointer;">
                                        <option value="">Khu vực</option>
                                        @if(count($list) > 0)
                                            @foreach ($list as $value)
                                                <option value="{{$value->ma_khu_vuc}}">{{$value->ten_khu_vuc}}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="address" class="col-sm-4 col-form-label">Địa chỉ</label>
                                <div class="col-sm-8">
                                    <input type="text" id="address" class="form-control" placeholder="Địa chỉ" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="phone_branch" class="col-sm-4 col-form-label">Số điện thoại</label>
                                <div class="col-sm-8">
                                    <input type="text" id="phone_branch" v-model="phone_branch" class="form-control" placeholder="Số điện thoại" required>
                                </div>
                            </div>
                            <button class="button-app" @click="saveBranch()"> Lưu </button>
                        </div>
                    </div>
                    <div class="col-8 pr-0" style="">
                        <div class="pac-card" id="pac-card">
                            <div>
                                <div id="title" class="d-none mb-2">
                                    Autocomplete search
                                </div>
                                <div id="type-selector" class="pac-controls d-none">
                                    <input type="radio" name="type" id="changetype-all" checked="checked">
                                    <label for="changetype-all">All</label>

                                    <input type="radio" name="type" id="changetype-establishment">
                                    <label for="changetype-establishment">Establishments</label>

                                    <input type="radio" name="type" id="changetype-address">
                                    <label for="changetype-address">Addresses</label>

                                    <input type="radio" name="type" id="changetype-geocode">
                                    <label for="changetype-geocode">Geocodes</label>
                                </div>
                                <div id="strict-bounds-selector" class="pac-controls d-none">
                                    <input type="checkbox" id="use-strict-bounds" value="">
                                    <label for="use-strict-bounds">Strict Bounds</label>
                                </div>
                            </div>
                            <div id="pac-container">
                                <input id="pac-input" class="mt-3" type="text"
                                    placeholder="Enter a location" style="width: 450px; height: 30px;">
                            </div>
                        </div>
                        <div id="map" class="w-100" style="height: 566px"></div>
                        <div id="infowindow-content">
                            <img src="" width="20" height="16" id="place-icon">
                            <span id="place-name"  class="title"></span><br>
                            <span id="place-address"></span>
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
					include public_path('/js/Branch/Branch.js');
				@endphp
			</script>
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUYbTR-3PDWPhgxjENs4yf35g2eHc641s&libraries=places&callback=initMap"
    async defer></script>
    {{-- <script src="https://maps.googleapis.com/maps/api/js?key={{config('app.maps_api_key')}}&libraries=places&callback=initMap"
        async defer></script> --}}
    <script type="text/javascript">
        function initMap() {
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: 10.797, lng: 106.718},
                zoom: 13
            });
            setMarkers(map);
            var card = document.getElementById('pac-card');
            var input = document.getElementById('pac-input');
            var types = document.getElementById('type-selector');
            var strictBounds = document.getElementById('strict-bounds-selector');

            map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);

            var autocomplete = new google.maps.places.Autocomplete(input);

            // Bind the map's bounds (viewport) property to the autocomplete object,
            // so that the autocomplete requests use the current map bounds for the
            // bounds option in the request.
            autocomplete.bindTo('bounds', map);

            // Set the data fields to return when the user selects a place.
            autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);

            var infowindow = new google.maps.InfoWindow();
            var infowindowContent = document.getElementById('infowindow-content');
            infowindow.setContent(infowindowContent);
            var marker = new google.maps.Marker({
                map: map,
                draggable: true,
                anchorPoint: new google.maps.Point(0, -29)
            });

            google.maps.event.addListener(marker, 'dragend', function (evt) {
                document.getElementById("lat").value = evt.latLng.lat().toFixed(5);
                document.getElementById("long").value = evt.latLng.lng().toFixed(5);
                document.getElementById("lat_update").value = evt.latLng.lat().toFixed(5);
                document.getElementById("long_update").value = evt.latLng.lng().toFixed(5);
            });

            autocomplete.addListener('place_changed', function() {
            infowindow.close();
            marker.setVisible(false);
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                window.alert("No details available for input: '" + place.name + "'");
                return;
            }

            // If the place has a geometry, then present it on a map.
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }

            document.getElementById("lat").value = place.geometry.location.lat().toFixed(5);
            document.getElementById("long").value = place.geometry.location.lng().toFixed(5);
            document.getElementById("lat_update").value = place.geometry.location.lat().toFixed(5);
            document.getElementById("long_update").value = place.geometry.location.lng().toFixed(5);
            document.getElementById("address").value = document.getElementById("pac-input").value;
            document.getElementById("address1").value = document.getElementById("pac-input").value;

            marker.setPosition(place.geometry.location);
            marker.setVisible(true);

            var address = '';
            if (place.address_components) {
                address = [
                    (place.address_components[0] && place.address_components[0].short_name || ''),
                    (place.address_components[1] && place.address_components[1].short_name || ''),
                    (place.address_components[2] && place.address_components[2].short_name || '')
                ].join(' ');
            }

            // infowindowContent.children['place-icon'].src = place.icon;
            // infowindowContent.children['place-name'].textContent = place.name;
            infowindowContent.children['place-address'].textContent = address;
            infowindow.open(map, marker);
            });

            // Sets a listener on a radio button to change the filter type on Places
            // Autocomplete.
            function setupClickListener(id, types) {
                var radioButton = document.getElementById(id);
                radioButton.addEventListener('click', function() {
                    autocomplete.setTypes(types);
                });
            }

            setupClickListener('changetype-all', []);
            setupClickListener('changetype-address', ['address']);
            setupClickListener('changetype-establishment', ['establishment']);
            setupClickListener('changetype-geocode', ['geocode']);

            document.getElementById('use-strict-bounds').addEventListener('click', function() {
                autocomplete.setOptions({strictBounds: this.checked});
            });
      }

        var arr_lat_long = [];
            arr_lat_long.push(<?php 
                for ($i = 0; $i < count($listBranch); $i++) {
                    if ($listBranch[$i]->latitude != null && $listBranch[$i]->latitude != "" && $listBranch[$i]->longitude != null && $listBranch[$i]->longitude != "") {
                        echo ("['" . $listBranch[$i]->ten . "',"  . (float)$listBranch[$i]->latitude . "," . (float)$listBranch[$i]->longitude . "],");   
                    }
                }
            ?>);
        function setMarkers(map) {
            var image = {
                url: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png',
                size: new google.maps.Size(20, 32),
                origin: new google.maps.Point(0, 0),
                anchor: new google.maps.Point(0, 32)
            };

            var shape = {
                coords: [1, 1, 1, 20, 18, 20, 18, 1],
                type: 'poly'
            };

            for (var i = 0; i < arr_lat_long.length; i++) {
                var name_lat_long = arr_lat_long[i];
                var marker = new google.maps.Marker({
                    position: {lat: name_lat_long[1], lng: name_lat_long[2]},
                    map: map,
                    icon: image,
                    shape: shape,
                    title: name_lat_long[0],
                });

                google.maps.event.addListener(marker, 'click', function (evt) {
                    map.setZoom(18);
                    map.panTo(this.getPosition());
                });
            }
        }
        $("#list_branch").click(function() { 
            $("#list_branch").addClass('active');
            $("#add_branch").removeClass('active');

            $(".list-branch-form").removeClass('d-none');
            $(".list-branch-form").addClass('d-block');

            $(".add-branch-form").removeClass('d-block');
            $(".add-branch-form").addClass('d-none');
        });

        $("#add_branch").click(function() {
            $("#add_branch").addClass('active');
            $("#list_branch").removeClass('active');

            $(".add-branch-form").removeClass('d-none');
            $(".add-branch-form").addClass('d-block');
            
            $(".list-branch-form").removeClass('d-block');
            $(".list-branch-form").addClass('d-none');
        });

        $('#collapseTableBranch').on('show.bs.collapse', function () {
            $(".update-branch-form").removeClass('d-block');
            $(".update-branch-form").addClass('d-none');
        })

    </script>
@endsection