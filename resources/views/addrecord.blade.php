@extends('layouts.app')

@section('content')
<form action="{{ route('records.store')}}" method="post">
	<div class="container">
		<div class="card">
			<div class="card-header font-weight-bold text-info">Add New Record</div>
			<div class="card-body">
				<div class="alert alert-info" role="alert">
					All fields marked with an asterisk (<span class="text-danger font-weight-bold">*</span>) are required.
				</div>

				@if(session('msg'))
					<div class="alert alert-danger" role="alert">
						{{session('msg')}}
					</div>
				@endif

				@csrf
				<hr>
				<h5 class="font-weight-bold">Patient Information</h5>
				<hr>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="lname"><span class="text-danger font-weight-bold">*</span>Last Name</label>
							<input type="text" class="form-control @error('lname') border-danger @enderror" id="lname" name="lname" value="{{old('lname')}}" max="50" autofocus required>
							@error('lname')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="fname"><span class="text-danger font-weight-bold">*</span>First Name (and Suffix)</label>
							<input type="text" class="form-control @error('fname') border-danger @enderror" id="fname" name="fname" value="{{old('fname')}}" max="50" required>
							@error('fname')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label for="mname">Middle Name <small><i>(Leave blank if N/A)</i></small></label>
							<input type="text" class="form-control" id="mname" name="mname" value="{{old('mname')}}" max="50">
							@error('mname')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="bdate"><span class="text-danger font-weight-bold">*</span>Birthdate</label>
							<input type="date" class="form-control" id="bdate" name="bdate" value="{{old('bdate')}}" min="1900-01-01" max="{{date('Y-m-d', strtotime('yesterday'))}}" required>
							@error('bdate')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="gender"><span class="text-danger font-weight-bold">*</span>Gender</label>
							<select class="form-control" id="gender" name="gender" required>
								<option value="" disabled selected>Choose</option>
								<option value="MALE" @if(old('gender') == 'MALE') {{'selected'}} @endif>Male</option>
								<option value="FEMALE" @if(old('gender') == 'FEMALE') {{'selected'}} @endif>Female</option>
							</select>
							@error('gender')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="cs"><span class="text-danger font-weight-bold">*</span>Civil Status</label>
							<select class="form-control" id="cs" name="cs" required>
								<option value="" disabled selected>Choose</option>
								<option value="SINGLE" @if(old('cs') == 'SINGLE') {{'selected'}} @endif>Single</option>
								<option value="MARRIED" @if(old('cs') == 'MARRIED') {{'selected'}} @endif>Married</option>
								<option value="WIDOWED" @if(old('cs') == 'WIDOWED') {{'selected'}} @endif>Widowed</option>
								<option value="N/A" @if(old('cs') == 'N/A') {{'selected'}} @endif>N/A</option>
							</select>
							@error('cs')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="nationality"><span class="text-danger font-weight-bold">*</span>Nationality</label>
							<select class="form-control" id="nationality" name="nationality" required>
								<option value="Filipino" @if(old('nationality') == 'Filipino' || empty(old('nationality'))) {{'selected'}} @endif>Filipino</option>
								<option value="Foreign" @if(old('nationality') == 'Foreign') {{'selected'}} @endif>Foreign</option>
							</select>
							@error('nationality')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
				</div>
				<div id="pdiv" class="mb-3">
					<div class="row">
						<div class="col-md-3"></div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="pregnant"><span class="text-danger font-weight-bold">*</span>Is the Patient Pregnant?</label>
								<select class="form-control" name="pregnant" id="pregnant" required>
								  <option value="0" @if(old('pregnant') == 0) {{'selected'}} @endif>NO</option>
								  <option value="1" @if(old('pregnant') == 1) {{'selected'}} @endif>YES</option>
								</select>
							</div>
						</div>
						<div class="col-md-6"></div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label for="mobile"><span class="text-danger font-weight-bold">*</span>Cellphone No.</label>
							<input type="text" class="form-control" id="mobile" name="mobile" value="{{old('mobile')}}" pattern="[0-9]{11}" placeholder="0917xxxxxxx" required>
							@error('mobile')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="phoneno">Home Phone No. (& Area Code)</label>
							<input type="text" class="form-control" id="phoneno" name="phoneno" value="{{old('phoneno')}}">
							@error('phoneno')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="email">Email Address</label>
							<input type="email" class="form-control" name="email" id="email">
							@error('email')
								  <small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label for="philhealth">Philhealth No. <small><i>(Leave blank if N/A)</i></small></label>
							<input type="text" class="form-control" id="philhealth" name="philhealth" value="{{old('philhealth')}}" minlength="12" maxlength="14">
							<small class="form-text text-muted">Note: If your input has no dashes, the system will automatically do that for you.</small>
							@error('philhealth')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
				</div>
				<hr>
				<h5 class="font-weight-bold">Current Address</h5>
				<hr>
				<div id="addresstext">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
							  <input type="text" class="form-control" name="address_province" id="address_province">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" class="form-control" name="address_city" id="address_city">
							</div>
						</div>
						<div class="col-md-4">
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
							  <input type="text" class="form-control" name="address_provincejson" id="address_provincejson">
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<input type="text" class="form-control" name="address_cityjson" id="address_cityjson">
							</div>
						</div>
						<div class="col-md-4">
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label for="saddress_province"><span class="text-danger font-weight-bold">*</span>Province</label>
							<select class="form-control" name="saddress_province" id="saddress_province" required>
							  <option value="" selected disabled>Choose...</option>
							</select>
								@error('saddress_province')
								  <small class="text-danger">{{$message}}</small>
							  @enderror
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						  <label for="saddress_city"><span class="text-danger font-weight-bold">*</span>City</label>
						  <select class="form-control" name="saddress_city" id="saddress_city" required>
							<option value="" selected disabled>Choose...</option>
						  </select>
							@error('saddress_city')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
						  <label for="address_brgy"><span class="text-danger font-weight-bold">*</span>Barangay</label>
						  <select class="form-control" name="address_brgy" id="address_brgy" required>
							<option value="" selected disabled>Choose...</option>
						  </select>
							  @error('address_brgy')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="address_houseno"><span class="text-danger font-weight-bold">*</span>House No./Lot/Building</label>
							<input type="text" class="form-control" id="address_houseno" name="address_houseno" value="{{old('address_houseno')}}" required>
							@error('address_houseno')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="address_street"><span class="text-danger font-weight-bold">*</span>Street/Purok/Sitio</label>
							<input type="text" class="form-control" id="address_street" name="address_street" value="{{old('address_street')}}" required>
							@error('address_street')
								<small class="text-danger">{{$message}}</small>
							@enderror
						</div>
					</div>
				</div>
				<div id="addresscheck">
					<div class="form-check form-check-inline">
						<label for="" class="mr-3 mt-1">Current Address is Different from Permanent Address?</label>
						<label class="form-check-label">
							<input class="form-check-input" type="radio" name="paddressdifferent" id="paddressdifferent1" value="1" @if(old('paddressdifferent') == 1) {{'checked'}} @endif> Yes
						</label>
						<label class="form-check-label">
							<input class="form-check-input ml-3" type="radio" name="paddressdifferent" id="paddressdifferent0" value="0" @if(old('paddressdifferent') == 0) {{'checked'}} @endif> No
						</label>
					</div>
				</div>
				<div id="permaaddress_div">
					<hr>
					<h5 class="font-weight-bold">Permanent Address and Contact Information</h5>
					<hr>
					<div id="permaaddresstext">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
								  <input type="text" class="form-control" name="permaaddress_province" id="permaaddress_province">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class="form-control" name="permaaddress_city" id="permaaddress_city">
								</div>
							</div>
							<div class="col-md-4">
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
								  <input type="text" class="form-control" name="permaaddress_provincejson" id="permaaddress_provincejson">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class="form-control" name="permaaddress_cityjson" id="permaaddress_cityjson">
								</div>
							</div>
							<div class="col-md-4">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="spermaaddress_province"><span class="text-danger font-weight-bold">*</span>Province</label>
								<select class="form-control" name="spermaaddress_province" id="spermaaddress_province">
								  <option value="" selected disabled>Choose...</option>
								</select>
									@error('spermaaddress_province')
									  <small class="text-danger">{{$message}}</small>
								  @enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="spermaaddress_city"><span class="text-danger font-weight-bold">*</span>City</label>
								<select class="form-control" name="spermaaddress_city" id="spermaaddress_city">
								  <option value="" selected disabled>Choose...</option>
								</select>
								  @error('spermaaddress_city')
									  <small class="text-danger">{{$message}}</small>
								  @enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="permaaddress_brgy"><span class="text-danger font-weight-bold">*</span>Barangay</label>
								<select class="form-control" name="permaaddress_brgy" id="permaaddress_brgy">
								  <option value="" selected disabled>Choose...</option>
								</select>
									@error('permaaddress_brgy')
									  <small class="text-danger">{{$message}}</small>
								  @enderror
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="permaaddress_houseno"><span class="text-danger font-weight-bold">*</span>House No./Lot/Building</label>
								<input type="text" class="form-control" id="permaaddress_houseno" name="permaaddress_houseno" value="{{old('permaaddress_houseno')}}">
								@error('permaaddress_houseno')
									<small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="permaaddress_street"><span class="text-danger font-weight-bold">*</span>Street/Purok/Sitio</label>
								<input type="text" class="form-control" id="permaaddress_street" name="permaaddress_street" value="{{old('permaaddress_street')}}">
								@error('permaaddress_street')
									<small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="permamobile"><span class="text-danger font-weight-bold">*</span>Cellphone No.</label>
								<input type="text" class="form-control" id="permamobile" name="permamobile" value="{{old('permamobile')}}" pattern="[0-9]{11}" placeholder="0917xxxxxxx">
								@error('permamobile')
									<small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="permaphoneno">Home Phone No. (& Area Code)</label>
								<input type="number" class="form-control" id="permaphoneno" name="permaphoneno" value="{{old('permaphoneno')}}">
								@error('permaphoneno')
									<small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="email">Email Address</label>
								<input type="permaemail" class="form-control" name="permaemail" id="permaemail">
								@error('permaemail')
									  <small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
					</div>
				</div>
				<hr>
				<div class="form-check form-check-inline">
					<label for="" class="mr-3 mt-1">Patient has Occupation?</label>
					<label class="form-check-label">
						<input class="form-check-input" type="radio" name="hasoccupation" id="hasoccupation_yes" value="1" @if(old('hasoccupation') == 1) {{'checked'}} @endif> Yes
					</label>
					<label class="form-check-label">
						<input class="form-check-input ml-3" type="radio" name="hasoccupation" id="hasoccupation_no" value="0" @if(old('hasoccupation') == 0) {{'checked'}} @endif> No
					</label>
				</div>
				<div id="occupation_div">
					<hr>
					<h5 class="font-weight-bold">Current Workplace Information and Address</h5>
					<hr>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="occupation"><span class="text-danger font-weight-bold">*</span>Occupation</label>
								<input type="text" class="form-control" name="occupation" id="occupation" value="{{old('occupation')}}">
								@error('occupation')
									<small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="occupation_name">Name of Workplace <small>(Optional)</small></label>
								<input type="text" class="form-control" name="occupation_name" id="occupation_name" value="{{old('occupation_name')}}">
								@error('occupation_name')
									<small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
							  <label for="worksInClosedSetting"><span class="text-danger font-weight-bold">*</span>Works in a closed setting?</label>
							  <select class="form-control" name="worksInClosedSetting" id="worksInClosedSetting">
								<option value="NO" {{(old('worksInClosedSetting') == "NO") ? 'selected' : ''}}>No</option>
								<option value="YES" {{(old('worksInClosedSetting') == "YES") ? 'selected' : ''}}>Yes</option>
								<option value="UNKNOWN" {{(old('worksInClosedSetting') == "UNKNOWN") ? 'selected' : ''}}>Unknown</option>
							  </select>
							</div>
						</div>
					</div>
					<div id="occupationaddresstext">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
								  <input type="text" class="form-control" name="occupation_province" id="occupation_province">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class="form-control" name="occupation_city" id="occupation_city">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
								  <input type="text" class="form-control" name="occupation_provincejson" id="occupation_provincejson">
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<input type="text" class="form-control" name="occupation_cityjson" id="occupation_cityjson">
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label for="soccupation_province">Province <small>(Optional)</small></label>
								<select class="form-control" name="soccupation_province" id="soccupation_province">
								  <option value="" selected disabled>Choose...</option>
								</select>
									@error('soccupation_province')
									  <small class="text-danger">{{$message}}</small>
								  @enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="soccupation_city">City <small>(Optional)</small></label>
								<select class="form-control" name="soccupation_city" id="soccupation_city">
								  <option value="" selected disabled>Choose...</option>
								</select>
								  @error('soccupation_city')
									  <small class="text-danger">{{$message}}</small>
								  @enderror
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label for="occupation_brgy">Barangay <small>(Optional)</small></label>
								<select class="form-control" name="occupation_brgy" id="occupation_brgy">
								  <option value="" selected disabled>Choose...</option>
								</select>
									@error('occupation_brgy')
									  <small class="text-danger">{{$message}}</small>
								  @enderror
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label for="occupation_lotbldg">Lot/Building <small>(Optional)</small></label>
								<input type="text" class="form-control" id="occupation_lotbldg" name="occupation_lotbldg" value="{{old('occupation_lotbldg')}}">
								@error('occupation_lotbldg')
									<small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="occupation_street">Street</label>
								<input type="text" class="form-control" id="occupation_street" name="occupation_street" value="{{old('occupation_street')}}">
								@error('occupation_street')
									<small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="occupation_mobile">Phone/Mobile No. <small>(Optional)</small></label>
								<input type="text" class="form-control" id="occupation_mobile" name="occupation_mobile" pattern="[0-9]{11}" placeholder="0917xxxxxxx" value="{{old('occupation_mobile')}}">
								@error('occupation_mobile')
									<small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label for="occupation_email">Email <small>(Optional)</small></label>
								<input type="email" class="form-control" name="occupation_email" id="occupation_email">
								@error('occupation_email')
									  <small class="text-danger">{{$message}}</small>
								@enderror
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer text-right">
				<button type="submit" class="btn btn-primary"><i class="far fa-save mr-2"></i>Save</button>
			</div>
		</div>
	</div>
</form>

<script>
	$(document).ready(function () {
		$('#addresstext').hide();
		$('#permaaddresstext').hide();
		$('#occupationaddresstext').hide();
		$('#saddress_city').prop('disabled', true);
		$('#address_brgy').prop('disabled', true);
		$('#spermaaddress_city').prop('disabled', true);
		$('#permaaddress_brgy').prop('disabled', true);
		$('#soccupation_city').prop('disabled', true);
		$('#occupation_brgy').prop('disabled', true);

		$.getJSON("{{asset('json/refprovince.json')}}", function(data) {
			var sorted = data.sort(function(a, b) {
				if (a.provDesc > b.provDesc) {
				return 1;
				}
				if (a.provDesc < b.provDesc) {
				return -1;
				}
				return 0;
			});

			$.each(sorted, function(key, val) {
				$("#saddress_province").append('<option value="'+val.provCode+'">'+val.provDesc+'</option>');
				$("#spermaaddress_province").append('<option value="'+val.provCode+'">'+val.provDesc+'</option>');
				$("#soccupation_province").append('<option value="'+val.provCode+'">'+val.provDesc+'</option>');
			});
        });

		$('#saddress_province').change(function (e) {
			e.preventDefault();
			$('#saddress_city').prop('disabled', false);
			$('#address_brgy').prop('disabled', true);
			$('#saddress_city').empty();
			$("#saddress_city").append('<option value="" selected disabled>Choose...</option>');
			$('#address_brgy').empty();
			$("#address_brgy").append('<option value="" selected disabled>Choose...</option>');
			$("#address_province").val($('#saddress_province option:selected').text());
			$("#address_provincejson").val($('#saddress_province').val());
			
			$.getJSON("{{asset('json/refcitymun.json')}}", function(data) {
				var sorted = data.sort(function(a, b) {
					if (a.citymunDesc > b.citymunDesc) {
					return 1;
					}
					if (a.citymunDesc < b.citymunDesc) {
					return -1;
					}
					return 0;
				});
				$.each(sorted, function(key, val) {
					if($('#saddress_province').val() == val.provCode) {
						$("#saddress_city").append('<option value="'+val.citymunCode+'">'+val.citymunDesc+'</option>');
					}
				});
			});
		});

		$('#spermaaddress_province').change(function (e) {
			e.preventDefault();
			$('#spermaaddress_city').prop('disabled', false);
			$('#permaaddress_brgy').prop('disabled', true);
			$('#spermaaddress_city').empty();
			$("#spermaaddress_city").append('<option value="" selected disabled>Choose...</option>');
			$('#permaaddress_brgy').empty();
			$("#permaaddress_brgy").append('<option value="" selected disabled>Choose...</option>');
			$("#permaaddress_province").val($('#spermaaddress_province option:selected').text());
			$("#permaaddress_provincejson").val($('#spermaaddress_province').val());
			
			$.getJSON("{{asset('json/refcitymun.json')}}", function(data) {
				var sorted = data.sort(function(a, b) {
					if (a.citymunDesc > b.citymunDesc) {
					return 1;
					}
					if (a.citymunDesc < b.citymunDesc) {
					return -1;
					}
					return 0;
				});
				$.each(sorted, function(key, val) {
					if($('#spermaaddress_province').val() == val.provCode) {
						$("#spermaaddress_city").append('<option value="'+val.citymunCode+'">'+val.citymunDesc+'</option>');
					}
				});
			});
		});

		$('#soccupation_province').change(function (e) {
			e.preventDefault();
			$('#soccupation_city').prop('disabled', false);
			$('#occupation_brgy').prop('disabled', true);
			$('#soccupation_city').prop('required', true);
			$('#occupation_brgy').prop('required', false);
			$('#soccupation_city').empty();
			$("#soccupation_city").append('<option value="" selected disabled>Choose...</option>');
			$('#occupation_brgy').empty();
			$("#occupation_brgy").append('<option value="" selected disabled>Choose...</option>');
			$("#occupation_province").val($('#soccupation_province option:selected').text());
			$("#occupation_provincejson").val($('#soccupation_province').val());
			
			$.getJSON("{{asset('json/refcitymun.json')}}", function(data) {
				var sorted = data.sort(function(a, b) {
					if (a.citymunDesc > b.citymunDesc) {
					return 1;
					}
					if (a.citymunDesc < b.citymunDesc) {
					return -1;
					}
					return 0;
				});
				$.each(sorted, function(key, val) {
					if($('#soccupation_province').val() == val.provCode) {
						$("#soccupation_city").append('<option value="'+val.citymunCode+'">'+val.citymunDesc+'</option>');
					}
				});
			});
		});

		$('#saddress_city').change(function (e) { 
			e.preventDefault();
			$('#address_brgy').prop('disabled', false);
			$('#address_brgy').empty();
			$("#address_brgy").append('<option value="" selected disabled>Choose...</option>');
			$("#address_city").val($('#saddress_city option:selected').text());
			$('#address_cityjson').val($('#saddress_city').val());

			$.getJSON("{{asset('json/refbrgy.json')}}", function(data) {
				var sorted = data.sort(function(a, b) {
					if (a.brgyDesc > b.brgyDesc) {
					return 1;
					}
					if (a.brgyDesc < b.brgyDesc) {
					return -1;
					}
					return 0;
				});
				$.each(sorted, function(key, val) {
					if($('#saddress_city').val() == val.citymunCode) {
						$("#address_brgy").append('<option value="'+val.brgyDesc.toUpperCase()+'">'+val.brgyDesc.toUpperCase()+'</option>');
					}
				});
			});
		});

		$('#spermaaddress_city').change(function (e) { 
			e.preventDefault();
			$('#permaaddress_brgy').prop('disabled', false);
			$('#permaaddress_brgy').empty();
			$("#permaaddress_brgy").append('<option value="" selected disabled>Choose...</option>');
			$("#permaaddress_city").val($('#spermaaddress_city option:selected').text());
			$('#permaaddress_cityjson').val($('#spermaaddress_city').val());

			$.getJSON("{{asset('json/refbrgy.json')}}", function(data) {
				var sorted = data.sort(function(a, b) {
					if (a.brgyDesc > b.brgyDesc) {
					return 1;
					}
					if (a.brgyDesc < b.brgyDesc) {
					return -1;
					}
					return 0;
				});
				$.each(sorted, function(key, val) {
					if($('#spermaaddress_city').val() == val.citymunCode) {
						$("#permaaddress_brgy").append('<option value="'+val.brgyDesc.toUpperCase()+'">'+val.brgyDesc.toUpperCase()+'</option>');
					}
				});
			});
		});

		$('#soccupation_city').change(function (e) { 
			e.preventDefault();
			$('#occupation_brgy').prop('disabled', false);
			$('#occupation_brgy').prop('required', true);
			$('#occupation_brgy').empty();
			$("#occupation_brgy").append('<option value="" selected disabled>Choose...</option>');
			$("#occupation_city").val($('#soccupation_city option:selected').text());
			$('#occupation_cityjson').val($('#soccupation_city').val());

			$.getJSON("{{asset('json/refbrgy.json')}}", function(data) {
				var sorted = data.sort(function(a, b) {
					if (a.brgyDesc > b.brgyDesc) {
					return 1;
					}
					if (a.brgyDesc < b.brgyDesc) {
					return -1;
					}
					return 0;
				});
				$.each(sorted, function(key, val) {
					if($('#soccupation_city').val() == val.citymunCode) {
						$("#occupation_brgy").append('<option value="'+val.brgyDesc.toUpperCase()+'">'+val.brgyDesc.toUpperCase()+'</option>');
					}
				});
			});
		});

		$('#pdiv').hide();
		$('#occupation_div').hide();
		if($('#hasoccupation_yes').is(':checked')) {
			$('#occupation_div').show();
		}

		if($('#hasoccupation_no').is(':checked')) {
			$('#occupation_div').hide();
		}

		$('#addresscheck').change(function() {
			if($("input[name='paddressdifferent']:checked").val() == 0) {
				$('#permaaddress_div').hide();

				$('#spermaaddress_province').prop('required', false);
				$('#spermaaddress_city').prop('required', false);
				$('#permaaddress_brgy').prop('required', false);
				$('#permaaddress_houseno').prop('required', false);
				$('#permaaddress_street').prop('required', false);
				$('#permamobile').prop('required', false);
			}
			else {
				$('#permaaddress_div').show();

				$('#spermaaddress_province').prop('required', true);
				$('#spermaaddress_city').prop('required', true);
				$('#permaaddress_brgy').prop('required', true);
				$('#permaaddress_houseno').prop('required', true);
				$('#permaaddress_street').prop('required', true);
				$('#permamobile').prop('required', true);
			}
		}).trigger('change');

		$('#gender').change(function (e) {
			e.preventDefault();
			if($('#gender').val() == 'FEMALE') {
				$('#pdiv').show();
			}
			else {
				$('#pdiv').hide();
			}
		}).trigger('change');

		$('input[type=radio][name=hasoccupation]').change(function() {
			if(this.value == "0") {
				$('#occupation_div').hide();

				$('#occupation_name').prop('required', false);
				$('occupation').prop('required', false);
				$('soccupation_province').prop('required', false);
				$('soccupation_city').prop('required', false);
				$('occupation_brgy').prop('required', false);
				$('occupation_lotbldg').prop('required', false);
				$('occupation_street').prop('required', false);
				$('worksInClosedSetting').prop('required', false);
			}
			else {
				$('#occupation_div').show();

				$('#occupation_name').prop('required', false);
				$('occupation').prop('required', true);
				$('soccupation_province').prop('required', false);
				$('soccupation_city').prop('required', false);
				$('occupation_brgy').prop('required', false);
				$('occupation_lotbldg').prop('required', false);
				$('occupation_street').prop('required', false);
				$('worksInClosedSetting').prop('required', true);
			}
		});
	});
</script>
@endsection
