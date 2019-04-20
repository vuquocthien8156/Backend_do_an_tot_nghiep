@extends('layout.base')
@section('stylesheet')
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/fancyapps/fancybox@3.5.6/dist/jquery.fancybox.min.css"/>
@endsection
@section('body-content')
	<div id="manage-vehicle"> 
		<div class="row mt-5 pt-3">
			<div style="padding-left: 2rem">
				<h4 class="tag-page-custom">
					<a class="tag-title-show" style="text-decoration: none;" href="{{route('manage-vehicle', [], false)}}">QUẢN LÝ BÁN XE</a> 
				</h4>
			</div>
		</div>
		<div class="row">
			<div class="set-row background-contact w-100" style="min-height: 150px">
				<div class="pb-2">
					<input id="code" type="text" class="input-app mr-4"  placeholder="Nhập mã bài đăng"  style="width: 200px" v-model="code">
					<input id="poster" type="text" class="input-app mr-4"  placeholder="Người đăng hoặc SĐT"  style="width: 200px" v-model="poster">
					<select name="manufacture" v-model="id_manufacture" id="manufacture"@change="getModelManufacture()" class="input-app mr-4" style="width: 200px; height: 33px">
						<option value="">Hãng xe</option>
						@if(count($listManufacture) > 0)
							@foreach ($listManufacture as $value)
								<option value="{{$value->id}}">{{$value->name}}</option>
							@endforeach
						@endif
					</select>
					<select name="model" id="model" class="input-app mr-4" v-model="model" style="width: 200px; height: 33px">
						<option value=""> Dòng xe </option>
						<option v-for="manufacture_model_result in manufacture_model_results" v-bind:value="manufacture_model_result.id">@{{manufacture_model_result.name}}</option>
					</select>
					<select name="status" id="status" class="input-app mr-4" v-model="status" style="width: 200px; height: 33px">
						<option value="">Chọn trạng thái</option>
						<option value="{{ \App\Enums\EVehicleStatus::SOLD }}">Đã bán</option>
						<option value="{{ \App\Enums\EVehicleStatus::SELLING }}">Chưa bán</option>
					</select> 
				</div>
				<div class="row">
					<div class="col-md-6 mt-3 ml-auto">
						<button class="button-app ml-5 float-right" @click="searchVehicle()">Tìm kiếm</button>
					</div>
					<div class="col-md-6 mt-3 ml-auto">
						<a :href="'manage/exportList?poster='+result_infoExport.poster+'&vehicle_manufacture_id='+result_infoExport.id_manufacture_selling+'&selling_status='+result_infoExport.selling_status+'&model='+result_infoExport.model" class="btn btn-primary button-app mb-4" >Xuất File Excel</a>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 mb-4" style="text-align: right;">
						<button class="button-app mt-3 ml-auto" style="border: 1px solid transparent;margin-right: 8%" @click="checkaccredited()">Kiểm định</button>
						<button class="button-app ml-3" style="border: 1px solid transparent;margin-right: 8%" @click="approveSellingRequest()">Duyệt</button>
					</div>
				</div>
				<div id="table_1" class="position-relative">
					<table id="tb1" class="table table-bordered table-striped w-100" style="min-height: 150px; line-height: 1.4;">
						<thead style="">
							<tr class="text-center blue-opacity">
								<th class="custom-view" width="2%">STT</th>
								<th class="custom-view">Mã bài đăng</th>
								<th class="custom-view">Hãng xe</th>
								<th class="custom-view">Dòng Xe</th>
								<th class="custom-view">Giá</th>
								<th class="custom-view">Người đăng</th>
								<th class="custom-view">Tiêu đề bài đăng</th>
								<th class="custom-view">Mô tả</th>
								<th class="custom-view">Kiểm định</th>
								<th class="custom-view">Trạng thái</th>
								<th class="custom-view">Tình trạng</th>
								<th class="custom-view">Ưu tiên</th>
								<th class="custom-view">Hình ảnh</th>
							</tr>
						</thead>
						<tbody v-cloak>
							<tr class="text-center" style="font-weight:bold" v-for="(item, index) in results_search.data" :key="item.selling_vehicle_id">
								<td class="custom-view td-grey" :class="{'grey-blue' : index % 2 != 0}" style="font-weight: bold">@{{ (results_search.current_page - 1) * results_search.per_page + index + 1 }}</td>
								<td class="custom-view">@{{item.code}}</td>
								<td class="custom-view ">@{{item.vehicle_manufacture_id}}</td>
								<td class="custom-view " style="width: 200px">@{{item.vehicle_model_id}}</td>
								<td class="custom-view ">@{{item.price}} VND</td>
								<td class="custom-view" style="width:250px;">@{{item.poster_name}} - @{{item.number_phone}}</td>
								<td class="custom-view "><div class="title1">@{{item.title}}</div></td>
								<td class="more" v-if="item.description != null">
									<span v-if="item.description.length > 80" class="d-none" :id="'description' + item.selling_vehicle_id">@{{item.description | formatDescription}}</span>	
									<span class="d-block" :id="'descriptionSubstr' + item.selling_vehicle_id">@{{item.description | descriptionSubstr}}
										<span v-if="item.description.length > 80" class="d-block" :id="'eclip' + item.selling_vehicle_id">...</span>
									</span>
									<a v-if="item.description.length > 80" style="cursor: pointer; color: blue;" class="see_more_less" :data-id="item.selling_vehicle_id" :id="'see_more_less' + item.selling_vehicle_id">Xem thêm mô tả</a>
								</td>
								<td class="more" v-else-if="item.description == null || item.description == ''">
									<span style="color: red; text-align: center;">Chưa có mô tả</span>

								</td>
								<td class="custom-view">
									<p v-if=" item.accredited == '{{\App\Enums\EVehicleAccredited::ACCREDITED}}'">Đã kiểm định</p>
									<input v-if="item.accredited == '{{\App\Enums\EVehicleAccredited::NOTACCREDITED}}'" :value="item.selling_vehicle_id" type="checkbox" name="checkconsership" v-model="checkCensorship" class="check_approve">
								</td>
								<td class="custom-view">
									<input type="checkbox" class="check_approve" :id="'check_selling'+item.selling_vehicle_id" v-if="item.selling_status == '{{\App\Enums\EVehicleStatus::SELLING}}'" @click="updateStatus(item.selling_vehicle_id)">
									<p v-if="item.selling_status == '{{\App\Enums\EVehicleStatus::SOLD}}'">Đã bán</p>
								</td>

								<td class="custom-view">
									<p v-if="item.approved == '{{App\Enums\EVehicleApprove::APPROVE}}'">Đã duyệt</p>
									<input class="check_approve" :value="item.selling_vehicle_id" name="check[]" v-model="checkApprove" type="checkbox" v-if="item.approved == '{{App\Enums\EVehicleApprove::NOTAPPROVE}}'">
								</td>
								<td class="custom-view">
									<input type="checkbox" v-if="item.display_order == '{{\App\Enums\EVehicleDisplayOrder::PRIORITIZE}}'" name="check" class="check_approve" :checked="true" @click="checkprioritize(item.selling_vehicle_id,item.display_order)">
									<input type="checkbox" v-if="item.display_order == '{{\App\Enums\EVehicleDisplayOrder::NOPRIORITIZE}}' && item.selling_status == '{{\App\Enums\EVehicleStatus::SELLING}}'" name="check" class="check_approve" :checked="false" @click="checkprioritize(item.selling_vehicle_id,item.display_order)">
								</td>
								<td class="custom-view">
									<a href="#imageVehicle"><p class="btn see_image" @click="loadSellingRequestResource(item.selling_vehicle_id)">Xem ảnh</p></a>
								</td>
							</tr>
						</tbody>   
					</table>    
				</div>
				<div class="col-12">
					<pagination :data="results_search" @pagination-change-page="searchVehicle" :limit="4"></pagination> 
				</div>
				<div class="row" v-if="results_search.last_page > 1">
					<div class="col-md-10 mx-auto" style="text-align: right;">
						<button class="button-app ml-3 mr-4" style="border: 1px solid transparent;margin-right: 8%" @click="approveSellingRequest()">Duyệt</button>
						<button class="button-app ml-3" style="border: 1px solid transparent;margin-right: 8%" @click="checkaccredited()">Kiểm duyệt</button>
					</div>
				</div>
			</div>
		</div>
		
		<div id="imageVehicle" class="demo-gallery" style="display: none">
			<label>Hình ảnh xe</label>
			<ul id="lightgallery1" class="list-unstyled row">
				<li style="margin-bottom:2%; width: 100px; margin-left: 1%; height: 100px;" v-for="(item, index) in results_image.imageVehicle" v-if="item.kind == '{{\App\Enums\EVehicleResourceKind::IMAGE}}'">
					<a data-fancybox="galleryVehicle" :href="results_image.path +'/'+ item.path_to_resource">
						<img class="img-responsive" width="100px" height="100px" :src="results_image.path +'/'+ item.path_to_resource">
					</a>
				</li> 
			</ul>
		</div>
		<div id="cavetVehicle" class="demo-gallery" style="display: none">
				<label>Cavet</label>
				<ul id="lightgallery2" class="list-unstyled row">
					<li style="margin-bottom:2%; width: 100px; margin-left: 1%; height: 100px;" v-for="(item, index) in results_image.imageVehicle" v-if="item.kind == '{{\App\Enums\EVehicleResourceKind::CAVET}}'">
						<a data-fancybox="galleryCavet" :href="results_image.path +'/'+ item.path_to_resource">
							<img class="img-responsive" width="100px" height="100px" :src="results_image.path +'/'+ item.path_to_resource">
						</a>
					</li> 
				</ul>
			</div>	
	</div>
@endsection
@section('scripts')
	<script type="text/javascript">
		@php
			include public_path('/js/vehicle/manage-vehicle/manage-vehicle.js');
			include public_path('/js/vehicle/manage-vehicle/jquery.fancybox.min.js');
			include public_path('/js/vehicle/manage-vehicle/see-more-description.js');
		@endphp
	// $(document).ready(function() {
	// 	$('body').on('click', '.check_approve', function() {
	// 				$(this).prop('checked',true);
	// 		})
	// });
	</script>
@endsection