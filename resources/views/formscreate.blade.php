@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{route('forms.store')}}" method="POST">
            <div class="card">
                <div class="card-body">
                    @csrf
                    <div class="alert alert-info" role="alert">
                        All fields marked with an asterisk (<span class="text-danger font-weight-bold">*</span>) are required.
                    </div>
                    <hr>

                    @if($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)
                            <p>{{$error}}</p>
                            <hr>
                        @endforeach
                    </div>
                    <hr>
                    @endif
    
                    <div class="form-group">
                      <label for="records_id"><span class="text-danger font-weight-bold">*</span>Select Patient</label>
                      <select name="records_id" id="records_id" required>
                          <option value="" selected disabled>Choose...</option>
                        @forelse($records as $record)
                            <option value="{{$record->id}}" {{($record->id == old('records_id')) ? 'selected' : ""}}>{{$record->lname}}, {{$record->fname}} | {{$record->gender}} | {{date("m/d/Y", strtotime($record->bdate))}}</option>
                        @empty
    
                        @endforelse
                      </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="drunit"><span class="text-danger font-weight-bold">*</span>Disease Reporting Unit</label>
                                <select class="form-control" name="drunit" id="drunit" required>
                                    <option value="" disabled {{(empty(old('drunit'))) ? 'selected' : ''}}>Choose...</option>
                                    <option class="CHO GENERAL TRIAS" {{(old('drunit') == "CHO GENERAL TRIAS") ? 'selected' : 'selected'}}>CHO GENERAL TRIAS</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="drregion"><span class="text-danger font-weight-bold">*</span>DRU Region and Province</label>
                                <select class="form-control" name="drregion" id="drregion" required>
                                    <option value="" disabled {{(empty('drregion')) ? 'selected' : ''}}>Choose...</option>
                                    <option class="4A CAVITE" {{(old('drregion') == "4A CAVITE") ? 'selected' : 'selected'}}>4A CAVITE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for=""><span class="text-danger font-weight-bold">*</span>Philhealth No.</label>
                                <input type="text" name="" id="rec_philhealth" class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="interviewerName"><span class="text-danger font-weight-bold">*</span>Name of Interviewer</label>
                                <input type="text" name="interviewerName" id="interviewerName" class="form-control" value="{{old('interviewerName')}}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="interviewerMobile"><span class="text-danger font-weight-bold">*</span>Contact Number of Interviewer</label>
                                <input type="number" name="interviewerMobile" id="interviewerMobile" class="form-control" value="{{old('interviewerMobile')}}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="interviewDate"><span class="text-danger font-weight-bold">*</span>Date of Interview</label>
                                <input type="date" name="interviewDate" id="interviewDate" class="form-control" value="@if(!is_null(old('interviewDate'))){{old('interviewDate')}}@else{{date('Y-m-d')}}@endif" max="{{date('Y-m-d')}}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="informantName">Name of Informant <small><i>(If patient unavailable)</i></small></label>
                                <input type="text" name="informantName" id="informantName" class="form-control" value="{{old('informantName')}}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="informantRelationship">Relationship</label>
                                <select class="form-control" name="informantRelationship" id="informantRelationship">
                                <option value="" disabled {{(is_null(old('informantRelationship'))) ? 'selected' : ''}}>Choose...</option>
                                <option value="Relative" {{(old('informantRelationship') == "Relative") ? 'selected' : ''}}>Family/Relative</option>
                                <option value="Friend" {{(old('informantRelationship') == "Friend") ? 'selected' : ''}}>Friend</option>
                                <option value="Others" {{(old('informantRelationship') == "Others") ? 'selected' : ''}}>Others</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="informantMobile">Contact Number of Informant</label>
                                <input type="number" name="informantMobile" id="informantMobile" class="form-control" value="{{old('informantMobile')}}">
                            </div>
                        </div>
                    </div>
                    <div class="card mb-3">
                        <div class="card-header">
                            <span class="text-danger font-weight-bold">*</span>If existing case (<i>check all that apply</i>)
                        </div>
                        <div class="card-body exCaseList">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("1", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Not applicable (New case)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="2" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("2", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Not applicable (Unknown)
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="3" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("3", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Update symptoms
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="4" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("4", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Update health status
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="5" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("5", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Update outcome
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="6" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("6", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Update case classification
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="7" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("7", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Update lab result
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="8" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("8", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Update chest imaging findings
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="9" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("9", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Update disposition
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="10" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("10", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Update exposure / travel history
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="11" id="ecothers" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("11", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Others
                                        </label>
                                    </div>
                                    <div id="divECOthers">
                                        <div class="form-group mt-2">
                                            <label for="ecOthersRemarks"><span class="text-danger font-weight-bold">*</span>Specify</label>
                                          <input type="text" name="ecOthersRemarks" id="ecOthersRemarks" class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="pType"><span class="text-danger font-weight-bold">*</span>Type of Client</label>
                        <select class="form-control" name="pType" id="pType" required>
                        <option value="PROBABLE" @if(old('pType') == "PROBABLE"){{'selected'}}@endif>COVID-19 Case (Suspect, Probable, or Confirmed)</option>
                        <option value="CLOSE CONTACT" @if(old('pType') == "CLOSE CONTACT"){{'selected'}}@endif>Close Contact</option>
                        <option value="TESTING" @if(old('pType') == "TESTING"){{'selected'}}@endif>For RT-PCR Testing (Not a Case of Close Contact)</option>
                        </select>
                    </div>
                    <div><label for=""><span class="text-danger font-weight-bold">*</span>Testing Category/Subgroup <i>(Check all that apply)</i></label></div>
                    <div class="form-check form-check-inline testingCatOptions">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="testingCat[]" id="testingCat_A" value="A" required @if(is_array(old('testingCat')) && in_array("A", old('testingCat'))) checked @endif> A
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input ml-3" type="checkbox" name="testingCat[]" id="testingCat_B" value="B" required @if(is_array(old('testingCat')) && in_array("B", old('testingCat'))) checked @endif> B
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input ml-3" type="checkbox" name="testingCat[]" id="testingCat_C" value="C" required @if(is_array(old('testingCat')) && in_array("C", old('testingCat'))) checked @else checked @endif> C
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input ml-3" type="checkbox" name="testingCat[]" id="testingCat_D" value="D" required @if(is_array(old('testingCat')) && in_array("D", old('testingCat'))) checked @endif> D
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input ml-3" type="checkbox" name="testingCat[]" id="testingCat_E" value="E" required @if(is_array(old('testingCat')) && in_array("E", old('testingCat'))) checked @endif> E
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input ml-3" type="checkbox" name="testingCat[]" id="testingCat_F" value="F" required @if(is_array(old('testingCat')) && in_array("F", old('testingCat'))) checked @endif> F
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input ml-3" type="checkbox" name="testingCat[]" id="testingCat_G" value="G" required @if(is_array(old('testingCat')) && in_array("G", old('testingCat'))) checked @endif> G
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input ml-3" type="checkbox" name="testingCat[]" id="testingCat_H" value="H" required @if(is_array(old('testingCat')) && in_array("H", old('testingCat'))) checked @endif> H
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input ml-3" type="checkbox" name="testingCat[]" id="testingCat_I" value="I" required @if(is_array(old('testingCat')) && in_array("I", old('testingCat'))) checked @endif> I
                        </label>
                        <label class="form-check-label">
                            <input class="form-check-input ml-3" type="checkbox" name="testingCat[]" id="testingCat_J" value="J" required @if(is_array(old('testingCat')) && in_array("J", old('testingCat'))) checked @endif> J
                        </label>
                    </div>
                    
                    <div class="card mt-3">
                        <div class="card-header font-weight-bold">Part 1. Patient Information</div>
                        <div class="card-body">
                            <div class="card mb-3">
                                <div class="card-header">1.1 Patient Profile</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Last Name</label>
                                                <input type="text" class="form-control" name="" id="rec_lname" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">First Name</label>
                                                <input type="text" class="form-control" name="" id="rec_fname" disabled>
                                            </div>
                                        </div> 
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Middle Name</label>
                                                <input type="text" class="form-control" name="" id="rec_mname" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Birthdate (MM/DD/YYYY)</label>
                                                <input type="text" class="form-control" name="" id="rec_bdate" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Age</label>
                                                <input type="text" class="form-control" name="" id="rec_age" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Gender</label>
                                                <input type="text" class="form-control" name="" id="rec_gender" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="">Civil Status</label>
                                                <input type="text" class="form-control" name="" id="rec_cs" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="">Nationality</label>
                                                <input type="text" class="form-control" name="" id="rec_nationality" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Occupation</label>
                                                <input type="text" class="form-control" name="" id="rec_occupation" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Works in a Closed Setting</label>
                                                <input type="text" class="form-control" name="" id="rec_wiacs" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">1.2 Current Address in the Philippines and Contact Information</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">House No./Lot/Bldg.</label>
                                                <input type="text" class="form-control" name="" id="rec_lot" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Street/Purok/Sitio</label>
                                                <input type="text" class="form-control" name="" id="rec_street" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Barangay</label>
                                                <input type="text" class="form-control" name="" id="rec_brgy" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Municipality/City</label>
                                                <input type="text" class="form-control" name="" id="rec_city" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Province</label>
                                                <input type="text" class="form-control" name="" id="rec_province" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Home Phone No. (& Area Code)</label>
                                                <input type="text" class="form-control" name="" id="rec_phoneno" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Cellphone No.</label>
                                                <input type="text" class="form-control" name="" id="rec_mobile" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Email Address</label>
                                                <input type="text" class="form-control" name="" id="rec_email" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">1.3 Permanent Address and Contact Information (If different from current address)</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">House No./Lot/Bldg.</label>
                                                <input type="text" class="form-control" name="" id="rec_plot" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Street/Purok/Sitio</label>
                                                <input type="text" class="form-control" name="" id="rec_pstreet" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Barangay</label>
                                                <input type="text" class="form-control" name="" id="rec_pbrgy" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Municipality/City</label>
                                                <input type="text" class="form-control" name="" id="rec_pcity" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Province</label>
                                                <input type="text" class="form-control" name="" id="rec_pprovince" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Home Phone No. (& Area Code)</label>
                                                <input type="text" class="form-control" name="" id="rec_pphone" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Cellphone No.</label>
                                                <input type="text" class="form-control" name="" id="rec_pmobile" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Email Address</label>
                                                <input type="text" class="form-control" name="" id="rec_pemail" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">1.4 Current Workplace Address and Contact Information</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Lot/Bldg.</label>
                                                <input type="text" class="form-control" name="" id="rec_olot" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Street</label>
                                                <input type="text" class="form-control" name="" id="rec_ostreet" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Barangay</label>
                                                <input type="text" class="form-control" name="" id="rec_obrgy" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Municipality/City</label>
                                                <input type="text" class="form-control" name="" id="rec_ocity" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Province</label>
                                                <input type="text" class="form-control" name="" id="rec_oprovince" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Name of Workplace</label>
                                                <input type="text" class="form-control" name="" id="rec_oname" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Phone No./Cellphone No.</label>
                                                <input type="text" class="form-control" name="" id="rec_omobile" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Email Address</label>
                                                <input type="text" class="form-control" name="" id="rec_oemail" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">1.5 Special Population</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="isHealthCareWorker"><span class="text-danger font-weight-bold">*</span>Health Care Worker</label>
                                                <select class="form-control" name="isHealthCareWorker" id="isHealthCareWorker" required>
                                                    <option value="1" {{(old('isHealthCareWorker') == 1) ? 'selected' : ''}}>Yes</option>
                                                    <option value="0" {{(old('isHealthCareWorker') == 0 || is_null(old('isHealthCareWorker'))) ? 'selected' : ''}}>No</option>
                                                </select>
                                            </div>
                                            <div id="divisHealthCareWorker">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="healthCareCompanyName"><span class="text-danger font-weight-bold">*</span>Name of Health Facility</label>
                                                            <input type="text" class="form-control" name="healthCareCompanyName" id="healthCareCompanyName" value="{{old('healthCareCompanyName')}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="healthCareCompanyLocation"><span class="text-danger font-weight-bold">*</span>Location</label>
                                                            <input type="text" class="form-control" name="healthCareCompanyLocation" id="healthCareCompanyLocation" value="{{old('healthCareCompanyLocation')}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="isOFW"><span class="text-danger font-weight-bold">*</span>Returning Overseas Filipino</label>
                                                <select class="form-control" name="isOFW" id="isOFW" required>
                                                    <option value="1" {{(old('isOFW') == 1) ? 'selected' : ''}}>Yes</option>
                                                    <option value="0" {{(old('isOFW') == 0 || is_null(old('isOFW'))) ? 'selected' : ''}}>No</option>
                                                </select>
                                            </div>
                                            <div id="divisOFW">
                                                <div class="form-group">
                                                    <label for="OFWCountyOfOrigin"><span class="text-danger font-weight-bold">*</span>Country of Origin</label>
                                                    <select class="form-control" name="OFWCountyOfOrigin" id="OFWCountyOfOrigin">
                                                        <option value="" disabled {{(is_null(old('OFWCountyOfOrigin'))) ? 'selected' : ''}}>Choose...</option>
                                                        @foreach ($countries as $country)
                                                            @if($country != 'Philippines')
                                                                <option value="{{$country}}" {{(old('OFWCountyOfOrigin') == $country) ? 'selected' : ''}}>{{$country}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="form-group">
                                                  <label for="ofwType"><span class="text-danger font-weight-bold">*</span>OFW?</label>
                                                  <select class="form-control" name="ofwType" id="ofwType">
                                                    <option value="1" {{(old('ofwType') == "YES") ? 'selected' : ''}}>Yes</option>
                                                    <option value="2" {{(old('ofwType') == "NO") ? 'selected' : ''}}>No (Non-OFW)</option>
                                                  </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="isFNT"><span class="text-danger font-weight-bold">*</span>Foreign National Traveler</label>
                                                <select class="form-control" name="isFNT" id="isFNT" required>
                                                    <option value="1" {{(old('isFNT') == 1) ? 'selected' : ''}}>Yes</option>
                                                    <option value="0" {{(old('isFNT') == 0 || is_null(old('isFNT'))) ? 'selected' : ''}}>No</option>
                                                </select>
                                            </div>
                                            <div id="divisFNT">
                                                <div class="form-group">
                                                    <label for="FNTCountryOfOrigin"><span class="text-danger font-weight-bold">*</span>Country of Origin</label>
                                                    <select class="form-control" name="FNTCountryOfOrigin" id="FNTCountryOfOrigin">
                                                        <option value="" selected disabled>Choose...</option>
                                                        @foreach ($countries as $country)
                                                            @if($country != 'Philippines')
                                                                <option value="{{$country}}" {{(old('FNTCountryOfOrigin') == $country) ? 'selected' : ''}}>{{$country}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="isLSI"><span class="text-danger font-weight-bold">*</span>Locally Stranded Individual/APOR/Traveler</label>
                                                <select class="form-control" name="isLSI" id="isLSI" required>
                                                    <option value="1" {{(old('isLSI') == 1) ? 'selected' : ''}}>Yes</option>
                                                    <option value="0" {{(old('isLSI') == 0 || is_null(old('isLSI'))) ? 'selected' : ''}}>No</option>
                                                </select>
                                            </div>
                                            <div id="divisLSI">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                          <label for="LSIProvince"><span class="text-danger font-weight-bold">*</span>Province of Origin</label>
                                                          <select class="form-control" name="LSIProvince" id="LSIProvince">
                                                                <option value="" selected disabled>Choose...</option>
                                                          </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="LSICity"><span class="text-danger font-weight-bold">*</span>City of Origin</label>
                                                            <select class="form-control" name="LSICity" id="LSICity">
                                                                  <option value="" selected disabled>Choose...</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                  <label for="lsiType"><span class="text-danger font-weight-bold">*</span>Type</label>
                                                  <select class="form-control" name="lsiType" id="lsiType">
                                                    <option value="1" {{(old('lsiType') == 1) ? 'selected' : ''}}>Locally Stranted Individual</option>
                                                    <option value="0" {{(old('lsiType') == 2) ? 'selected' : ''}}>Authorized Person Outside Residence/Local Traveler</option>
                                                  </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>        
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="isLivesOnClosedSettings"><span class="text-danger font-weight-bold">*</span>Lives in Closed Settings</label>
                                                <select class="form-control" name="isLivesOnClosedSettings" id="isLivesOnClosedSettings" required>
                                                    <option value="1" {{(old('isLivesOnClosedSettings') == 1) ? 'selected' : ''}}>Yes</option>
                                                    <option value="0" {{(old('isLivesOnClosedSettings') == 0 || is_null(old('isLivesOnClosedSettings'))) ? 'selected' : ''}}>No</option>
                                                </select>
                                            </div>
                                            <div id="divisLivesOnClosedSettings">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                          <label for="institutionType"><span class="text-danger font-weight-bold">*</span>Specify Institution Type</label>
                                                          <input type="text" class="form-control" name="institutionType" id="institutionType" value="{{old('institutionType')}}">
                                                          <small><i>(e.g. prisons, residential facilities, retirement communities, care homes, camps etc.)</i></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="institutionName"><span class="text-danger font-weight-bold">*</span>Name of Institution</label>
                                                            <input type="text" class="form-control" name="institutionName" id="institutionName" value="{{old('institutionName')}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                              <label for="isIndg"><span class="text-danger font-weight-bold">*</span>Indigenous Person</label>
                                              <select class="form-control" name="isIndg" id="isIndg" required>
                                                <option value="1" {{(old('isIndg') == 1) ? 'selected' : ''}}>Yes</option>
                                                <option value="0" {{(old('isIndg') == 0 || is_null(old('isIndg'))) ? 'selected' : ''}}>No</option>
                                              </select>
                                            </div>
                                            <div id="divIsIndg">
                                                <div class="form-group">
                                                  <label for="indgSpecify"><span class="text-danger font-weight-bold">*</span>Specify Group</label>
                                                  <input type="text" class="form-control" name="indgSpecify" id="indgSpecify" value="{{old('indgSpecify')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header font-weight-bold">Part 2. Case Investigation Details</div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header">2.1 Consultation Information</div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="havePreviousCovidConsultation"><span class="text-danger font-weight-bold">*</span>Have previous COVID-19 related consultation?</label>
                                                <select class="form-control" name="havePreviousCovidConsultation" id="havePreviousCovidConsultation" required>
                                                    <option value="" selected disabled>Choose...</option>
                                                    <option value="1" {{(old('havePreviousCovidConsultation') == 1) ? 'selected' : ''}}>Yes</option>
                                                    <option value="0" {{(old('havePreviousCovidConsultation') == 0) ? 'selected' : ''}}>No</option>
                                                </select>
                                            </div>
                                            <div id="divYes1">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="facilityNameOfFirstConsult"><span class="text-danger font-weight-bold">*</span>Name of facility where first consult was done</label>
                                                            <input type="text" class="form-control" name="facilityNameOfFirstConsult" id="facilityNameOfFirstConsult" value="{{old('facilityNameOfFirstConsult')}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="dateOfFirstConsult"><span class="text-danger font-weight-bold">*</span>Date of First Consult</label>
                                                            <input type="date" class="form-control" name="dateOfFirstConsult" id="dateOfFirstConsult" value="{{old('dateOfFirstConsult')}}" max="{{date('Y-m-d')}}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header">2.2 Disposition at Time of Report</div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="dispositionType"><span class="text-danger font-weight-bold">*</span>Status</label>
                                                <select class="form-control" name="dispositionType" id="dispositionType">
                                                    <option value="" {{(is_null(old('dispositionType'))) ? 'selected' : ''}}>N/A</option>
                                                    <option value="1" {{(old('dispositionType') == 1) ? 'selected' : ''}}>Admitted in hospital</option>
                                                    <option value="2" {{(old('dispositionType') == 2) ? 'selected' : ''}}>Admitted in isolation/quarantine facility</option>
                                                    <option value="3" {{(old('dispositionType') == 3) ? 'selected' : ''}}>In home isolation/quarantine</option>
                                                    <option value="4" {{(old('dispositionType') == 4) ? 'selected' : ''}}>Discharged to home</option>
                                                    <option value="5" {{(old('dispositionType') == 5) ? 'selected' : ''}}>Others</option>
                                                </select>
                                            </div>
                                            <div id="divYes5">
                                                <div class="form-group">
                                                    <label for="dispositionName" id="dispositionlabel"></label>
                                                    <input type="text" class="form-control" name="dispositionName" id="dispositionName" value="{{old('dispositionName')}}">
                                                </div>
                                            </div>
                                            <div id="divYes6">
                                                <div class="form-group">
                                                    <label for="dispositionDate" id="dispositiondatelabel"></label>
                                                    <input type="datetime-local" class="form-control" name="dispositionDate" id="dispositionDate" value="{{old('dispositionDate')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header"><span class="text-danger font-weight-bold">*</span>2.3 Health Status at Consult</div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <select class="form-control" name="healthStatus" id="healthStatus" required>
                                                    <option value="Asymptomatic" {{(old('healthStatus') == 'Asymptomatic') ? 'selected' : ''}}>Asymptomatic </option>
                                                    <option value="Mild" {{(old('healthStatus') == 'Mild') ? 'selected' : ''}}>Mild</option>
                                                    <option value="Moderate" {{(old('healthStatus') == 'Moderate') ? 'selected' : ''}}>Moderate</option>
                                                    <option value="Severe" {{(old('healthStatus') == 'Severe') ? 'selected' : ''}}>Severe</option>
                                                    <option value="Critical" {{(old('healthStatus') == 'Critical') ? 'selected' : ''}}>Critical</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header"><span class="text-danger font-weight-bold">*</span>2.4 Case Classification</div>
                                        <div class="card-body">
                                            <div class="form-group">
                                                <select class="form-control" name="caseClassification" id="caseClassification" required>
                                                    <option value="Probable" {{(old('caseClassification') == 'Probable') ? 'selected' : ''}}>Probable</option>
                                                    <option value="Suspect" {{(old('caseClassification') == 'Suspect') ? 'selected' : ''}}>Suspect</option>
                                                    <option value="Confirmed" {{(old('caseClassification') == 'Confirmed') ? 'selected' : ''}}>Confirmed</option>
                                                    <option value="Non-COVID-19 Case" {{(old('caseClassification') == 'Non-COVID-19 Case') ? 'selected' : ''}}>Non-COVID-19 Case</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">2.5 Clinical Information</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                              <label for="dateOnsetOfIllness">Date of Onset of Illness</label>
                                              <input type="date" class="form-control" name="dateOnsetOfIllness" id="dateOnsetOfIllness" max="{{date('Y-m-d')}}">
                                            </div>
                                            <div class="card">
                                                <div class="card-header">Signs and Symptoms (Check all that apply)</div>
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Asymptomatic"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck1"
                                                                  {{(is_array(old('sasCheck')) && in_array("Asymptomatic", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck1">Asymptomatic</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Fever"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck2"
                                                                  {{(is_array(old('sasCheck')) && in_array("Fever", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck2">Fever</label>
                                                            </div>
                                                            <div id="divFeverChecked">
                                                                <div class="form-group mt-2">
                                                                  <label for="SASFeverDeg">Degrees (in Celcius)</label>
                                                                  <input type="number" class="form-control" name="SASFeverDeg" id="SASFeverDeg" min="1" value="{{old('SASFeverDeg')}}">
                                                                </div>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Cough"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck3"
                                                                  {{(is_array(old('sasCheck')) && in_array("Cough", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck3">Cough</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="General Weakness"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck4"
                                                                  {{(is_array(old('sasCheck')) && in_array("General Weakness", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck4">General Weakness</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Fatigue"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck5"
                                                                  {{(is_array(old('sasCheck')) && in_array("Fatigue", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck5">Fatigue</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Headache"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck6"
                                                                  {{(is_array(old('sasCheck')) && in_array("Headache", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck6">Headache</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Myalgia"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck7"
                                                                  {{(is_array(old('sasCheck')) && in_array("Myalgia", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck7">Myalgia</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Sore throat"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck8"
                                                                  {{(is_array(old('sasCheck')) && in_array("Sore throat", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck8">Sore Throat</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Coryza"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck9"
                                                                  {{(is_array(old('sasCheck')) && in_array("Coryza", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck9">Coryza</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Dyspnea"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck10"
                                                                  {{(is_array(old('sasCheck')) && in_array("Dyspnea", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck10">Dyspnea</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Anorexia"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck11"
                                                                  {{(is_array(old('sasCheck')) && in_array("Anorexia", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck11">Anorexia</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Nausea"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck12"
                                                                  {{(is_array(old('sasCheck')) && in_array("Nausea", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck12">Nausea</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Vomiting"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck13"
                                                                  {{(is_array(old('sasCheck')) && in_array("Vomiting", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck13">Vomiting</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Diarrhea"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck14"
                                                                  {{(is_array(old('sasCheck')) && in_array("Diarrhea", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck14">Diarrhea</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Altered Mental Status"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck15"
                                                                  {{(is_array(old('sasCheck')) && in_array("Altered Mental Status", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck15">Altered Mental Status</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Anosmia (Loss of Smell)"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck16"
                                                                  {{(is_array(old('sasCheck')) && in_array("Anosmia (Loss of Smell)", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck16">Anosmia <small>(loss of smell, w/o any identified cause)</small></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Ageusia (Loss of Taste)"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck17"
                                                                  {{(is_array(old('sasCheck')) && in_array("Ageusia (Loss of Taste)", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck17">Ageusia <small>(loss of taste, w/o any identified cause)</small></label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Others"
                                                                  name="sasCheck[]"
                                                                  id="signsCheck18"
                                                                  {{(is_array(old('sasCheck')) && in_array("Others", old('sasCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="signsCheck18">Others</label>
                                                            </div>
                                                            <div id="divSASOtherChecked">
                                                                <div class="form-group mt-2">
                                                                  <label for="SASOtherRemarks">Specify Findings</label>
                                                                  <input type="text" class="form-control" name="SASOtherRemarks" id="SASOtherRemarks" value="{{old('SASOtherRemarks')}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card mb-3">
                                                <div class="card-header">Comorbidities (Check all that apply if present)</div>
                                                <div class="card-body">
                                                    <div class="row comoOpt">
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="None"
                                                                  name="comCheck[]"
                                                                  id="comCheck1"
                                                                  required
                                                                  {{(is_array(old('comCheck')) && in_array("None", old('comCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="comCheck1">None</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Hypertension"
                                                                  name="comCheck[]"
                                                                  id="comCheck2"
                                                                  required
                                                                  {{(is_array(old('comCheck')) && in_array("Hypertension", old('comCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="comCheck2">Hypertension</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Diabetes"
                                                                  name="comCheck[]"
                                                                  id="comCheck3"
                                                                  required
                                                                  {{(is_array(old('comCheck')) && in_array("Diabetes", old('comCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="comCheck3">Diabetes</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Heart Disease"
                                                                  name="comCheck[]"
                                                                  id="comCheck4"
                                                                  required
                                                                  {{(is_array(old('comCheck')) && in_array("Heart Disease", old('comCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="comCheck4">Heart Disease</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Lung Disease"
                                                                  name="comCheck[]"
                                                                  id="comCheck5"
                                                                  required
                                                                  {{(is_array(old('comCheck')) && in_array("Lung Disease", old('comCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="comCheck5">Lung Disease</label>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Gastrointestinal"
                                                                  name="comCheck[]"
                                                                  id="comCheck6"
                                                                  required
                                                                  {{(is_array(old('comCheck')) && in_array("Gastrointestinal", old('comCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="comCheck6">Gastrointestinal</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Genito-urinary"
                                                                  name="comCheck[]"
                                                                  id="comCheck7"
                                                                  required
                                                                  {{(is_array(old('comCheck')) && in_array("Genito-urinary", old('comCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="comCheck7">Genito-urinary</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Neurological Disease"
                                                                  name="comCheck[]"
                                                                  id="comCheck8"
                                                                  required
                                                                  {{(is_array(old('comCheck')) && in_array("Neurological Disease", old('comCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="comCheck8">Neurological Disease</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Cancer"
                                                                  name="comCheck[]"
                                                                  id="comCheck9"
                                                                  required
                                                                  {{(is_array(old('comCheck')) && in_array("Cancer", old('comCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="comCheck9">Cancer</label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input
                                                                  class="form-check-input"
                                                                  type="checkbox"
                                                                  value="Others"
                                                                  name="comCheck[]"
                                                                  id="comCheck10"
                                                                  required
                                                                  {{(is_array(old('comCheck')) && in_array("Others", old('comCheck'))) ? 'checked' : ''}}
                                                                />
                                                                <label class="form-check-label" for="comCheck10">Others</label>
                                                            </div>
                                                            <div id="divComOthersChecked">
                                                                <div class="form-group mt-2">
                                                                  <label for="COMOOtherRemarks">Specify Findings</label>
                                                                  <input type="text" class="form-control" name="COMOOtherRemarks" id="COMOOtherRemarks" value="{{old('COMOOtherRemarks')}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for=""><span class="text-danger font-weight-bold">*</span>Pregnant?</label>
                                                        <input type="text" class="form-control" name="" id="rec_ispregnant" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="PregnantLMP"><span class="text-danger font-weight-bold">*</span>LMP</label>
                                                        <input type="date" class="form-control" name="PregnantLMP" id="PregnantLMP" value="{{old('PregnantLMP')}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                              <label for="highRiskPregnancy"><span class="text-danger font-weight-bold">*</span>High Risk Pregnancy?</label>
                                              <select class="form-control" name="highRiskPregnancy" id="highRiskPregnancy">
                                                <option value="0" {{(is_null(old('highRiskPregnancy')) || old('highRiskPregnancy') == 0) ? 'selected' : ''}}>No</option>
                                                <option value="1" {{(old('highRiskPregnancy') == 1) ? 'selected' : ''}}>Yes</option>
                                              </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group mt-3">
                                      <label for="diagWithSARI"><span class="text-danger font-weight-bold">*</span>Was diagnosed to have Severe Acute Respiratory Illness?</label>
                                      <select class="form-control" name="diagWithSARI" id="diagWithSARI" required>
                                        <option value="1" {{(old('diagWithSARI') == 1) ? 'selected' : ''}}>Yes</option>
                                        <option value="0" {{(is_null(old('diagWithSARI')) || old('diagWithSARI') == 0) ? 'selected' : ''}}>No</option>
                                      </select>
                                    </div>
                                    <div class="card">
                                        <div class="card-header">
                                            Chest imaging findings suggestive of COVID-19
                                            <hr>
                                            <span class="text-danger font-weight-bold">*</span>Imaging Done
                                        </div>
                                        <div class="card-body imaOptions">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                      <label for="">Date done</label>
                                                      <input type="date" class="form-control" name="imagingDoneDate" id="imagingDoneDate">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                      <label for="imagingDone">Imaging done</label>
                                                      <select class="form-control" name="imagingDone" id="imagingDone" required>
                                                        <option value="None">None</option>
                                                        <option value="Chest Radiography">Chest Radiography</option>
                                                        <option value="Chest CT">Chest CT</option>
                                                        <option value="Lung Ultrasound">Lung Ultrasound</option>
                                                      </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                      <label for="imagingResult">Results</label>
                                                      <select class="form-control" name="imagingResult" id="imagingResult">
                                                      </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-8">
                                                </div>
                                                <div class="col-md-4">
                                                    <div id="divImagingOthers">
                                                        <div class="form-group">
                                                          <label for="imagingOtherFindings"><span class="text-danger font-weight-bold">*</span>Specify findings</label>
                                                          <input type="text" class="form-control" name="imagingOtherFindings" id="imagingOtherFindings">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">2.6 Laboratory Information</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="testedPositiveUsingRTPCRBefore"><span class="text-danger font-weight-bold">*</span>Have you ever tested positive using RT-PCR before?</label>
                                                <select class="form-control" name="testedPositiveUsingRTPCRBefore" id="testedPositiveUsingRTPCRBefore" required>
                                                  <option value="1" {{(old('testedPositiveUsingRTPCRBefore') == 1) ? 'selected' : ''}}>Yes</option>
                                                  <option value="0" {{(is_null(old('testedPositiveUsingRTPCRBefore')) || old('testedPositiveUsingRTPCRBefore') == 0) ? 'selected' : ''}}>No</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="testedPositiveNumOfSwab"><span class="text-danger font-weight-bold">*</span>Number of previous RT-PCR swabs done</label>
                                                <input type="number" class="form-control" name="testedPositiveNumOfSwab" id="testedPositiveNumOfSwab" min="0" value="{{(is_null(old('testedPositiveNumOfSwab'))) ? '0' : old('testedPositiveNumOfSwab')}}" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="divIfTestedPositiveUsingRTPCR">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="testedPositiveSpecCollectedDate"><span class="text-danger font-weight-bold">*</span>Date of Specimen Collection</label>
                                                    <input type="date" class="form-control" name="testedPositiveSpecCollectedDate" id="testedPositiveSpecCollectedDate" max="{{date('Y-m-d')}}" value="{{old('testedPositiveSpecCollectedDate')}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                  <label for="testedPositiveLab"><span class="text-danger font-weight-bold">*</span>Laboratory</label>
                                                  <input type="text" class="form-control" name="testedPositiveLab" id="testedPositiveLab" value="{{old('testedPositiveLab')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                              <label for="testDateCollected1"><span class="text-danger font-weight-bold">*</span>1. Date Collected</label>
                                              <input type="date" class="form-control" name="testDateCollected1" id="testDateCollected1" value="{{old('testDateCollected1')}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="testDateReleased1">Date released</label>
                                                <input type="date" class="form-control" name="testDateReleased1" id="testDateReleased1" value="{{old('testDateReleased1')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="testLaboratory1"><span class="text-danger font-weight-bold">*</span>Laboratory</label>
                                                <input type="text" class="form-control" name="testLaboratory1" id="testLaboratory1" value="{{old('testLaboratory1')}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="testType1"><span class="text-danger font-weight-bold">*</span>Type of test</label>
                                              <select class="form-control" name="testType1" id="testType1" required>
                                                <option value="OPS" {{(old('testType1') == 'OPS') ? 'selected' : ''}}>RT-PCR (OPS)</option>
                                                <option value="NPS" {{(old('testType1') == 'NPS') ? 'selected' : ''}}>RT-PCR (NPS)</option>
                                                <option value="OPS AND NPS" {{(old('testType1') == 'OPS AND NPS') ? 'selected' : ''}}>RT-PCR (OPS and NPS)</option>
                                                <option value="ANTIGEN" {{(old('testType1') == 'ANTIGEN') ? 'selected' : ''}}>Antigen Test</option>
                                                <option value="ANTIBODY" {{(old('testType1') == 'ANTIBODY') ? 'selected' : ''}}>Antibody Test</option>
                                                <option value="OTHERS" {{(old('testType1') == 'Others') ? 'selected' : ''}}>Others</option>
                                              </select>
                                            </div>
                                            <div id="divTypeOthers1">
                                                <div class="form-group">
                                                  <label for="testTypeOtherRemarks1">Specify</label>
                                                  <input type="text" class="form-control" name="testTypeOtherRemarks1" id="testTypeOtherRemarks1" value="{{old('testTypeOtherRemarks1')}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                              <label for="testResult1"><span class="text-danger font-weight-bold">*</span>Results</label>
                                              <select class="form-control" name="testResult1" id="testResult1" required>
                                                <option value="PENDING" {{(old('testResult1') == 'PENDING') ? 'selected' : ''}}>Pending</option>
                                                <option value="POSITIVE" {{(old('testResult1') == 'POSITIVE') ? 'selected' : ''}}>Positive</option>
                                                <option value="NEGATIVE" {{(old('testResult1') == 'NEGATIVE') ? 'selected' : ''}}>Negative</option>
                                                <option value="EQUIVOCAL" {{(old('testResult1') == 'EQUIVOCAL') ? 'selected' : ''}}>Equivocal</option>
                                                <option value="OTHERS" {{(old('testResult1') == 'OTHERS') ? 'selected' : ''}}>Others</option>
                                              </select>
                                            </div>
                                            <div id="divResultOthers1">
                                                <div class="form-group">
                                                    <label for="testResultOtherRemarks1">Specify</label>
                                                    <input type="text" class="form-control" name="testResultOtherRemarks1" id="testResultOtherRemarks1" value="{{old('testResultOtherRemarks1')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                              <label for="testDateCollected2">2. Date Collected</label>
                                              <input type="date" class="form-control" name="testDateCollected2" id="testDateCollected2" value="{{old('testDateCollected2')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="testDateReleased2">Date released</label>
                                                <input type="date" class="form-control" name="testDateReleased2" id="testDateReleased2" value="{{old('testDateReleased2')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="testLaboratory2">Laboratory</label>
                                                <input type="text" class="form-control" name="testLaboratory2" id="testLaboratory2" value="{{old('testLaboratory2')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                              <label for="testType2">Type of test</label>
                                              <select class="form-control" name="testType2" id="testType2">
                                                    <option value="N/A">N/A</option>
                                                    <option value="OPS" {{(old('testType1') == 'OPS') ? 'selected' : ''}}>RT-PCR (OPS)</option>
                                                    <option value="NPS" {{(old('testType1') == 'NPS') ? 'selected' : ''}}>RT-PCR (NPS)</option>
                                                    <option value="OPS AND NPS" {{(old('testType1') == 'OPS AND NPS') ? 'selected' : ''}}>RT-PCR (OPS and NPS)</option>
                                                    <option value="ANTIGEN" {{(old('testType1') == 'ANTIGEN') ? 'selected' : ''}}>Antigen Test</option>
                                                    <option value="ANTIBODY" {{(old('testType1') == 'ANTIBODY') ? 'selected' : ''}}>Antibody Test</option>
                                                    <option value="OTHERS" {{(old('testType1') == 'Others') ? 'selected' : ''}}>Others</option>
                                              </select>
                                            </div>
                                            <div id="divTypeOthers2">
                                                <div class="form-group">
                                                  <label for="testTypeOtherRemarks2">Specify</label>
                                                  <input type="text" class="form-control" name="testTypeOtherRemarks2" id="testTypeOtherRemarks2" value="{{old('testTypeOtherRemarks2')}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                              <label for="testResult2">Results</label>
                                              <select class="form-control" name="testResult2" id="testResult2">
                                                <option value="PENDING" {{(old('testResult2') == 'PENDING') ? 'selected' : ''}}>Pending</option>
                                                <option value="POSITIVE" {{(old('testResult2') == 'POSITIVE') ? 'selected' : ''}}>Positive</option>
                                                <option value="NEGATIVE" {{(old('testResult2') == 'NEGATIVE') ? 'selected' : ''}}>Negative</option>
                                                <option value="EQUIVOCAL" {{(old('testResult2') == 'EQUIVOCAL') ? 'selected' : ''}}>Equivocal</option>
                                                <option value="OTHERS" {{(old('testResult2') == 'OTHERS') ? 'selected' : ''}}>Others</option>
                                              </select>
                                            </div>
                                            <div id="divResultOthers2">
                                                <div class="form-group">
                                                    <label for="testResultOtherRemarks2">Specify</label>
                                                    <input type="text" class="form-control" name="testResultOtherRemarks2" id="testResultOtherRemarks2" value="{{old('testResultOtherRemarks2')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">2.7 Outcome/Condition at Time of Report</div>
                                <div class="card-body">
                                    <div class="form-group">
                                      <label for="outcomeCondition"><span class="text-danger font-weight-bold">*</span>Select Condition</label>
                                      <select class="form-control" name="outcomeCondition" id="outcomeCondition">
                                        <option value="" {{(is_null(old('outcomeCondition'))) ? 'selected' : ''}}>N/A</option>
                                        <option value="Active" {{(old('outcomeCondition') == 'Active') ? 'selected' : ''}}>Active (Currently admitted or in isolation/quarantine)</option>
                                        <option value="Recovered" {{(old('outcomeCondition') == 'Recovered') ? 'selected' : ''}}>Recovered</option>
                                        <option value="Died" {{(old('outcomeCondition') == 'Died') ? 'selected' : ''}}>Died</option>
                                      </select>
                                    </div>
                                    <div id="ifOutcomeRecovered">
                                        <div class="form-group">
                                          <label for="outcomeRecovDate"><span class="text-danger font-weight-bold">*</span>Date of Recovery</label>
                                          <input type="date" class="form-control" name="outcomeRecovDate" id="outcomeRecovDate" max="{{date('Y-m-d')}}" value="{{old('outcomeRecovDate')}}">
                                        </div>
                                    </div>
                                    <div id="ifOutcomeDied">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="outcomeDeathDate"><span class="text-danger font-weight-bold">*</span>Date of Death</label>
                                                    <input type="date" class="form-control" name="outcomeDeathDate" id="outcomeDeathDate" max="{{date('Y-m-d')}}" value="{{old('outcomeDeathDate')}}">
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="deathImmeCause"><span class="text-danger font-weight-bold">*</span>Immediate Cause</label>
                                                    <input type="text" class="form-control" name="deathImmeCause" id="deathImmeCause" value="{{old('deathImmeCause')}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="deathAnteCause">Antecedent Cause</label>
                                                    <input type="text" class="form-control" name="deathAnteCause" id="deathAnteCause" value="{{old('deathAnteCause')}}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="deathUndeCause">Underlying Cause</label>
                                                    <input type="text" class="form-control" name="deathUndeCause" id="deathUndeCause" value="{{old('deathUndeCause')}}">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="deathUndeCause">Contributory Conditions</label>
                                                    <input type="text" class="form-control" name="contriCondi" id="contriCondi" value="{{old('contriCondi')}}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header font-weight-bold">Part 3. Contact Tracing: Exposure and Travel History</div>
                        <div class="card-body">
                            <div class="card mb-3">
                                <div class="card-header">15. Exposure History</div>
                                <div class="card-body">
                                    <div class="form-group">
                                      <label for="expoitem1"><span class="text-danger font-weight-bold">*</span>History of exposure to known probable and/or confirmed COVID-19 case 14 days before the onset of signs and symptoms?  OR If Asymptomatic, 14 days before swabbing or specimen collection?</label>
                                      <select class="form-control" name="expoitem1" id="expoitem1" required>
                                            <option value="2" {{(old('expoitem1') == 2) ? 'selected' : ''}}>No</option>
                                            <option value="1" {{(old('expoitem1') == 1) ? 'selected' : ''}}>Yes</option>
                                            <option value="3" {{(old('expoitem1') == 3) ? 'selected' : ''}}>Unknown</option>
                                      </select>
                                    </div>
                                    <div id="divExpoitem1">
                                        <div class="form-group">
                                          <label for=""><span class="text-danger font-weight-bold">*</span>Date of Last Contact</label>
                                          <input type="date" class="form-control" name="expoDateLastCont" id="expoDateLastCont" max="{{date('Y-m-d')}}" value="{{old('expoDateLastCont')}}">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="expoitem2"><span class="text-danger font-weight-bold">*</span>Has the patient been in a place with a known COVID-19 transmission 14 days before the onset of signs and symptoms? OR If Asymptomatic, 14 days before swabbing or specimen collection?</label>
                                        <select class="form-control" name="expoitem2" id="expoitem2" required>
                                          <option value="0" {{(old('expoitem2') == 2) ? 'selected' : ''}}>No</option>
                                          <option value="1" {{(old('expoitem2') == 1) ? 'selected' : ''}}>Yes, Local</option>
                                          <option value="2" {{(old('expoitem2') == 1) ? 'selected' : ''}}>Yes, International</option>
                                          <option value="3" {{(old('expoitem2') == 3) ? 'selected' : ''}}>Unknown exposure</option>
                                        </select>
                                    </div>
                                    <div id="divTravelInt">
                                        <div class="form-group">
                                            <label for="intCountry"><span class="text-danger font-weight-bold">*</span>If International Travel, country of origin</label>
                                            <select class="form-control" name="intCountry" id="intCountry">
                                                <option value="" {{(is_null('intCountry')) ? 'selected disabled' : ''}}>Choose...</option>
                                                  @foreach ($countries as $country)
                                                      @if($country != 'Philippines')
                                                          <option value="{{$country}}" {{(old('intCountry') == $country) ? 'selected' : ''}}>{{$country}}</option>
                                                      @endif
                                                  @endforeach
                                            </select>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="card mb-3">
                                                    <div class="card-header">Inclusive travel dates</div>
                                                    <div class="card-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                  <label for="intDateFrom">From</label>
                                                                  <input type="date" class="form-control" name="intDateFrom" id="intDateFrom">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="intDateTo">From</label>
                                                                    <input type="date" class="form-control" name="intDateTo" id="intDateTo">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="intWithOngoingCovid">With ongoing COVID-19 community transmission?</label>
                                                    <select class="form-control" name="intWithOngoingCovid" id="intWithOngoingCovid">
                                                      <option value="NO">No</option>
                                                      <option value="YES">Yes</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                          <label for="intVessel">Airline/Sea vessel</label>
                                                          <input type="text" class="form-control" name="intVessel" id="intVessel">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="intVesselNo">Flight/Vessel Number</label>
                                                            <input type="text" class="form-control" name="intVesselNo" id="intVesselNo">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="intDateDepart">Date of departure</label>
                                                            <input type="date" class="form-control" name="intDateDepart" id="intDateDepart">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="intDateArrive">Date of arrival in PH</label>
                                                            <input type="date" class="form-control" name="intDateArrive" id="intDateArrive">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="divTravelLoc">
                                        <div class="card">
                                            <div class="card-header">
                                                If Local Travel, specify travel places (<i>Check all that apply, provide name of facility, address, and inclusive travel dates</i>)
                                            </div>
                                            <div class="card-body">
                                                <div class="form-check">
                                                  <label class="form-check-label">
                                                    <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited1" value="Health Facility">
                                                    Health Facility
                                                  </label>
                                                </div>
                                                <div id="divLocal1" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName1">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName1" id="locName1">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress1">Location</label>
                                                                <input class="form-control" type="text" name="locAddress1" id="locAddress1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">Inclusive Travel Dates</div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateFrom1">From</label>
                                                                                <input class="form-control" type="date" name="locDateFrom1" id="locDateFrom1">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo1">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo1" id="locDateTo1">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locWithOngoingCovid1">With ongoing COVID-19 Community Transmission?</label>
                                                              <select class="form-control" name="locWithOngoingCovid1" id="locWithOngoingCovid1">
                                                                <option value="NO">No</option>
                                                                <option value="YES">Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited2" value="Closed Settings">
                                                      Closed Settings
                                                    </label>
                                                </div>
                                                <div id="divLocal2" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName2">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName2" id="locName2">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress2">Location</label>
                                                                <input class="form-control" type="text" name="locAddress2" id="locAddress2">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">Inclusive Travel Dates</div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateFrom2">From</label>
                                                                                <input class="form-control" type="date" name="locDateFrom2" id="locDateFrom2">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo2">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo2" id="locDateTo2">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locWithOngoingCovid2">With ongoing COVID-19 Community Transmission?</label>
                                                              <select class="form-control" name="locWithOngoingCovid2" id="locWithOngoingCovid2">
                                                                <option value="NO">No</option>
                                                                <option value="YES">Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited3" value="School">
                                                      School
                                                    </label>
                                                </div>
                                                <div id="divLocal3" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName3">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName3" id="locName3">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress3">Location</label>
                                                                <input class="form-control" type="text" name="locAddress3" id="locAddress3">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">Inclusive Travel Dates</div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateFrom3">From</label>
                                                                                <input class="form-control" type="date" name="locDateFrom3" id="locDateFrom3">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo3">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo3" id="locDateTo3">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locWithOngoingCovid3">With ongoing COVID-19 Community Transmission?</label>
                                                              <select class="form-control" name="locWithOngoingCovid3" id="locWithOngoingCovid3">
                                                                <option value="NO">No</option>
                                                                <option value="YES">Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited4" value="Workplace">
                                                      Workplace
                                                    </label>
                                                </div>
                                                <div id="divLocal4" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName4">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName4" id="locName4">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress4">Location</label>
                                                                <input class="form-control" type="text" name="locAddress4" id="locAddress4">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">Inclusive Travel Dates</div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateFrom4">From</label>
                                                                                <input class="form-control" type="date" name="locDateFrom4" id="locDateFrom4">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo4">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo4" id="locDateTo4">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locWithOngoingCovid4">With ongoing COVID-19 Community Transmission?</label>
                                                              <select class="form-control" name="locWithOngoingCovid4" id="locWithOngoingCovid4">
                                                                <option value="NO">No</option>
                                                                <option value="YES">Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited5" value="Market">
                                                      Market
                                                    </label>
                                                </div>
                                                <div id="divLocal5" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName5">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName5" id="locName5">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress5">Location</label>
                                                                <input class="form-control" type="text" name="locAddress5" id="locAddress5">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">Inclusive Travel Dates</div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateFrom5">From</label>
                                                                                <input class="form-control" type="date" name="locDateFrom5" id="locDateFrom5">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo5">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo5" id="locDateTo5">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locWithOngoingCovid5">With ongoing COVID-19 Community Transmission?</label>
                                                              <select class="form-control" name="locWithOngoingCovid5" id="locWithOngoingCovid5">
                                                                <option value="NO">No</option>
                                                                <option value="YES">Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited6" value="Social Gathering">
                                                      Social Gathering
                                                    </label>
                                                </div>
                                                <div id="divLocal6" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName6">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName6" id="locName6">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress6">Location</label>
                                                                <input class="form-control" type="text" name="locAddress6" id="locAddress6">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">Inclusive Travel Dates</div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateFrom6">From</label>
                                                                                <input class="form-control" type="date" name="locDateFrom6" id="locDateFrom6">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo6">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo6" id="locDateTo6">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locWithOngoingCovid6">With ongoing COVID-19 Community Transmission?</label>
                                                              <select class="form-control" name="locWithOngoingCovid6" id="locWithOngoingCovid6">
                                                                <option value="NO">No</option>
                                                                <option value="YES">Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited7" value="Others">
                                                      Others
                                                    </label>
                                                </div>
                                                <div id="divLocal7" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName7">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName7" id="locName7">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress7">Location</label>
                                                                <input class="form-control" type="text" name="locAddress7" id="locAddress7">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="card">
                                                                <div class="card-header">Inclusive Travel Dates</div>
                                                                <div class="card-body">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateFrom7">From</label>
                                                                                <input class="form-control" type="date" name="locDateFrom7" id="locDateFrom7">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo7">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo7" id="locDateTo7">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locWithOngoingCovid7">With ongoing COVID-19 Community Transmission?</label>
                                                              <select class="form-control" name="locWithOngoingCovid7" id="locWithOngoingCovid7">
                                                                <option value="NO">No</option>
                                                                <option value="YES">Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited8" value="Transport Service">
                                                      Transport Service
                                                    </label>
                                                </div>
                                                <div id="divLocal8" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                              <label for="localVessel1">1. Airline/Sea vessel/Bus line/Train</label>
                                                              <input type="text" class="form-control" name="localVessel1" id="localVessel1">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localVesselNo1">Flight/Vessel/Bus No.</label>
                                                                <input type="text" class="form-control" name="localVesselNo1" id="localVesselNo1">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localOrigin1">Place of Origin</label>
                                                                <input type="text" class="form-control" name="localOrigin1" id="localOrigin1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDateDepart1">Departure Date</label>
                                                                <input type="date" class="form-control" name="localDateDepart1" id="localDateDepart1">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDest1">Destination</label>
                                                                <input type="text" class="form-control" name="localDest1" id="localDest1">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDateArrive1">Date of Arrival</label>
                                                                <input type="text" class="form-control" name="localDateArrive1" id="localDateArrive1">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                              <label for="localVessel2">2. Airline/Sea vessel/Bus line/Train</label>
                                                              <input type="text" class="form-control" name="localVessel2" id="localVessel2">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localVesselNo2">Flight/Vessel/Bus No.</label>
                                                                <input type="text" class="form-control" name="localVesselNo2" id="localVesselNo2">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localOrigin2">Place of Origin</label>
                                                                <input type="text" class="form-control" name="localOrigin2" id="localOrigin2">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDateDepart2">Departure Date</label>
                                                                <input type="date" class="form-control" name="localDateDepart2" id="localDateDepart2">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDest2">Destination</label>
                                                                <input type="text" class="form-control" name="localDest2" id="localDest2">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDateArrive2">Date of Arrival</label>
                                                                <input type="date" class="form-control" name="localDateArrive2" id="localDateArrive2">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mt-3">
                                        <div class="card-header">List the names of persons who were with you two days prior to onset of illness until this date and their contact numbers.</div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <div class="alert alert-info" role="alert">
                                                        <p>- If symptomatic, provide names and contact numbers of persons who were with the patient two days prior to onset of illness until this date.</p>
                                                        <p>- If asymptomatic, provide names and contact numbers of persons who were with the patient on the day specimen was submitted for testing until this date.</p>
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="card">
                                                        <div class="card-header">Name</div>
                                                        <div class="card-body">
                                                            <div class="form-group">
                                                              <input type="text" class="form-control" name="contact1Name" id="contact1Name" value="{{old('contact1Name')}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="contact2Name" id="contact2Name" value="{{old('contact2Name')}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="contact3Name" id="contact3Name" value="{{old('contact3Name')}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="contact4Name" id="contact4Name" value="{{old('contact4Name')}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card">
                                                        <div class="card-header">Contact Number</div>
                                                        <div class="card-body">
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="contact1No" id="contact1No" value="{{old('contact1No')}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="contact2No" id="contact2No" value="{{old('contact2No')}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="contact3No" id="contact3No" value="{{old('contact3No')}}">
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="contact3No" id="contact3No" value="{{old('contact3No')}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary" id="formsubmit">Submit</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        $(document).ready(function () {

            $('#records_id').selectize();

            $('#records_id').change(function (e) { 
                e.preventDefault();
                var uid = $('#records_id').val();
                if ($('#records_id').val().length != 0) {
                    fetchRecords(uid);
                }
            }).trigger('change');

            $('#informantName').keydown(function (e) { 
                if($(this).val().length <= 0 || $(this).val() == "") {
                    $('#informantRelationship').prop({disabled: true, required: false});
                    $('#informantMobile').prop({disabled: true, required: false});
                }
                else {
                    $('#informantRelationship').val("");
                    $('#informantRelationship').prop({disabled: false, required: true});
                    $('#informantMobile').prop({disabled: false, required: true});
                }
            }).trigger('keydown');

            $('#ecothers').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divECOthers').show();
                    $('#ecOthersRemarks').prop('required', true);
                }
                else {
                    $('#divECOthers').hide();
                    $('#ecOthersRemarks').prop('required', false);
                }
            });

            $(function(){
                var requiredCheckboxes = $('.exCaseList :checkbox[required]');
                requiredCheckboxes.change(function(){
                    if(requiredCheckboxes.is(':checked')) {
                        requiredCheckboxes.removeAttr('required');
                    } else {
                        requiredCheckboxes.attr('required', 'required');
                    }
                }).trigger('change');
            });

            $(function(){
                var requiredCheckboxes = $('.testingCatOptions :checkbox[required]');
                requiredCheckboxes.change(function(){
                    if(requiredCheckboxes.is(':checked')) {
                        requiredCheckboxes.removeAttr('required');
                    } else {
                        requiredCheckboxes.attr('required', 'required');
                    }
                }).trigger('change');
            });

            $(function(){
                var requiredCheckboxes = $('.imaOptions :checkbox[required]');
                requiredCheckboxes.change(function(){
                    if(requiredCheckboxes.is(':checked')) {
                        requiredCheckboxes.removeAttr('required');
                    } else {
                        requiredCheckboxes.attr('required', 'required');
                    }
                }).trigger('change');;
            });

            $(function(){
                var requiredCheckboxes = $('.comoOpt :checkbox[required]');
                requiredCheckboxes.change(function(){
                    if(requiredCheckboxes.is(':checked')) {
                        requiredCheckboxes.removeAttr('required');
                    } else {
                        requiredCheckboxes.attr('required', 'required');
                    }
                }).trigger('change');;
            });

            $(function(){
                var requiredCheckboxes = $('.labOptions :checkbox[required]');
                requiredCheckboxes.change(function(){
                    if(requiredCheckboxes.is(':checked')) {
                        requiredCheckboxes.removeAttr('required');
                    } else {
                        requiredCheckboxes.attr('required', 'required');
                    }
                }).trigger('change');;
            });

            $('#LSICity').prop({'disabled': true, 'required': false});

            $.getJSON("{{asset('json/refregion.json')}}", function(data) {
                var sorted = data.sort(function(a, b) {
                    if (a.regDesc > b.regDesc) {
                    return 1;
                    }
                    if (a.regDesc < b.regDesc) {
                    return -1;
                    }
                    return 0;
                });
                $.each(sorted, function(key, val) {
                    $("#sfacilityregion").append('<option value="'+val.regCode+'">'+val.regDesc+'</option>');
                });
            });

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
                    $("#LSIProvince").append('<option value="'+val.provCode+'">'+val.provDesc+'</option>');
                });
            });

            $('#sfacilityregion').change(function (e) {
                e.preventDefault();
                $('#facilityprovince').prop({'disabled': false, 'required': true});
                $('#facilityprovince').empty();
                $("#facilityprovince").append('<option value="" selected disabled>Choose...</option>');

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
                        if($('#sfacilityregion').val() == val.regCode) {
                            $("#facilityprovince").append('<option value="'+val.provCode+'">'+val.provDesc+'</option>');
                        }
                    });
			    });
            });

            $('#LSIProvince').change(function (e) { 
                e.preventDefault();
                $('#LSICity').prop({'disabled': false, 'required': true});
                $('#LSICity').empty();
                $("#LSICity").append('<option value="" selected disabled>Choose...</option>');
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
                        if($('#LSIProvince').val() == val.provCode) {
                            $("#LSICity").append('<option value="'+val.citymunCode+'">'+val.citymunDesc+'</option>');
                        }
                    });
			    });
            });

            //$('#OFWCountyOfOrigin').selectize();
            //$('#FNTCountryOfOrigin').selectize();
        
            $('#divYes1').hide();
            $('#divYes5').hide();
            $('#divYes6').hide();
            
            $('#dispositionDate').prop("type", "datetime-local");

            $('#havePreviousCovidConsultation').change(function (e) { 
                e.preventDefault();
                if($(this).val() == '1') {
                    $('#divYes1').show();

                    $('#dateOfFirstConsult').prop('required', true);
                    $('#facilityNameOfFirstConsult').prop('required', true);
                }
                else {
                    $('#divYes1').hide();

                    $('#dateOfFirstConsult').prop('required', false);
                    $('#facilityNameOfFirstConsult').prop('required', false);
                }
            }).trigger('change');

            $('#dispositionType').change(function (e) {
                e.preventDefault();
                $('#dispositionDate').prop("type", "datetime-local");
                
                if($(this).val() == '1' || $(this).val() == '2') {
                    $('#dispositionName').prop('required', true);
                    $('#dispositionDate').prop('required', true);
                }
                else if ($(this).val() == '3' || $(this).val() == '4') {
                    $('#dispositionName').prop('required', false);
                    $('#dispositionDate').prop('required', true);
                }
                else if ($(this).val() == '5') {
                    $('#dispositionName').prop('required', true);
                    $('#dispositionDate').prop('required', false);
                }
                else if($(this).val().length == 0){
                    $('#dispositionName').prop('required', false);
                    $('#dispositionDate').prop('required', false);
                }

                if($(this).val() == '1') {
                    $('#divYes5').show();
                    $('#divYes6').show();

                    $('#dispositionlabel').text("Name of Hospital");
                    $('#dispositiondatelabel').text("Date and Time Admitted in Hospital");
                }
                if($(this).val() == '2') {
                    $('#divYes5').show();
                    $('#divYes6').show();

                    $('#dispositionlabel').text("Name of Facility");
                    $('#dispositiondatelabel').text("Date and Time Admitted in Hospital");
                }
                if($(this).val() == '3') {
                    $('#divYes5').hide();
                    $('#divYes6').show();

                    $('#dispositiondatelabel').text("Date and Time isolated/quarantined at home");
                }
                if($(this).val() == '4') {
                    $('#divYes5').hide();
                    $('#divYes6').show();

                    $('#dispositionDate').prop("type", "date");

                    $('#dispositiondatelabel').text("Date of Discharge");
                }
                if($(this).val() == '5') {
                    $('#divYes5').show();
                    $('#divYes6').hide();

                    $('#dispositionlabel').text("State Reason");
                }
                else if($(this).val().length == 0){
                    $('#divYes5').hide();
                    $('#divYes6').hide();
                }
            }).trigger('change');

            $('#isHealthCareWorker').change(function (e) { 
                e.preventDefault();
                if($(this).val() == '0') {
                    $('#divisHealthCareWorker').hide();
                    $('#healthCareCompanyName').prop('required', false);
                    $('#healthCareCompanyLocation').prop('required', false);
                }
                else {
                    $('#divisHealthCareWorker').show();
                    $('#healthCareCompanyName').prop('required', true);
                    $('#healthCareCompanyLocation').prop('required', true);
                }
            }).trigger('change');

            $('#isOFW').change(function (e) {
                if($(this).val() == '0') {
                    $('#divisOFW').hide();
                    $('#OFWCountyOfOrigin').prop('required', false);

                    /*
                    $('#oaddresslotbldg').prop({'required': false, 'disabled': true});
                    $('#oaddressstreet').prop({'required': false, 'disabled': true});
                    $('#oaddressscity').prop({'required': false, 'disabled': true});
                    $('#oaddresssprovince').prop({'required': false, 'disabled': true});
                    $('#oaddressscountry').prop({'required': false, 'disabled': true});
                    $('#placeofwork').prop({'required': false, 'disabled': true});
                    $('#employername').prop({'required': false, 'disabled': true});
                    $('#employercontactnumber').prop({'required': false, 'disabled': true});

                    $('#oaddresslotbldg').val('N/A');
                    $('#oaddressstreet').val('N/A');
                    $('#oaddressscity').val('N/A');
                    $('#oaddresssprovince').val('N/A');
                    $('#oaddressscountry').val('N/A');
                    $('#placeofwork').val('N/A');
                    $('#employername').val('N/A');
                    $('#employercontactnumber').val('N/A');
                    */
                }
                else {
                    $('#divisOFW').show();
                    var control = $('#OFWCountyOfOrigin')[0].selectize;
                    control.clear();
                    $('#oaddressscountry').val('N/A');
                    $('#OFWCountyOfOrigin').prop('required', true);

                    /*
                    $('#oaddresslotbldg').prop({required: true, disabled: false});
                    $('#oaddressstreet').prop({required: true, disabled: false});
                    $('#oaddressscity').prop({required: true, disabled: false});
                    $('#oaddresssprovince').prop({required: true, disabled: false});
                    $('#oaddressscountry').prop({required: true, disabled: false});
                    $('#placeofwork').prop({required: true, disabled: false});
                    $('#employername').prop({required: true, disabled: false});
                    $('#employercontactnumber').prop({required: true, disabled: false});

                    $('#oaddresslotbldg').val('');
                    $('#oaddressstreet').val('');
                    $('#oaddressscity').val('');
                    $('#oaddresssprovince').val('');
                    $('#placeofwork').val('');
                    $('#employername').val('');
                    $('#employercontactnumber').val('');
                    */
                }
            }).trigger('change');

            $('#OFWCountyOfOrigin').change(function (e) { 
                e.preventDefault();
                $('#oaddressscountry').val($(this).val());
            });

            $('#isFNT').change(function (e) {
                if($(this).val() == '0') {
                    $('#divisFNT').hide();
                    $('#FNTCountryOfOrigin').prop('required', false);
                }
                else {
                    $('#divisFNT').show();
                    $('#FNTCountryOfOrigin').prop('required', true);
                }
            }).trigger('change');

            $('#isLSI').change(function (e) {
                if($(this).val() == '0') {
                    $('#divisLSI').hide();
                    $('#LSIProvince').prop('required', false);
                    $('#LSICity').prop('required', false);
                }
                else {
                    $('#divisLSI').show();
                    $('#LSIProvince').prop('required', true);
                    $('#LSICity').prop('required', true);
                }
            }).trigger('change');

            $('#isLivesOnClosedSettings').change(function (e) {
                if($(this).val() == '0') {
                    $('#divisLivesOnClosedSettings').hide();
                    $('#institutionType').prop('required', false);
                    $('#institutionName').prop('required', false);
                }
                else {
                    $('#divisLivesOnClosedSettings').show();
                    $('#institutionType').prop('required', true);
                    $('#institutionName').prop('required', true);
                }
            }).trigger('change');

            $('#isIndg').change(function (e) {
                e.preventDefault();
                if($(this).val() == '0') {
                    $('#divIsIndg').hide();
                    $('#indgSpecify').prop('required', false);
                }
                else {
                    $('#divIsIndg').show();
                    $('#indgSpecify').prop('required', true);
                }
            }).trigger('change');

            $('#signsCheck2').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divFeverChecked').show();
                    $('#SASFeverDeg').prop('required', true);
                }
                else {
                    $('#divFeverChecked').hide();
                    $('#SASFeverDeg').prop('required', false);
                }
            }).trigger('change');

            $('#signsCheck18').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divSASOtherChecked').show();
                    $('#SASOtherRemarks').prop('required', true);
                }
                else {
                    $('#divSASOtherChecked').hide();
                    $('#SASOtherRemarks').prop('required', false);
                }
            }).trigger('change');

            $('#comCheck10').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divComOthersChecked').show();
                    $('#COMOOtherRemarks').prop('required', true);
                }
                else {
                    $('#divComOthersChecked').hide();
                    $('#COMOOtherRemarks').prop('required', false);
                }
            }).trigger('change');
            
            $('#comCheck1').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#comCheck2').prop({'disabled': true, 'checked': false});
                    $('#comCheck3').prop({'disabled': true, 'checked': false});
                    $('#comCheck4').prop({'disabled': true, 'checked': false});
                    $('#comCheck5').prop({'disabled': true, 'checked': false});
                    $('#comCheck6').prop({'disabled': true, 'checked': false});
                    $('#comCheck7').prop({'disabled': true, 'checked': false});
                    $('#comCheck8').prop({'disabled': true, 'checked': false});
                    $('#comCheck9').prop({'disabled': true, 'checked': false});
                    $('#comCheck10').prop({'disabled': true, 'checked': false});
                }
                else {
                    $('#comCheck2').prop({'disabled': false, 'checked': false});
                    $('#comCheck3').prop({'disabled': false, 'checked': false});
                    $('#comCheck4').prop({'disabled': false, 'checked': false});
                    $('#comCheck5').prop({'disabled': false, 'checked': false});
                    $('#comCheck6').prop({'disabled': false, 'checked': false});
                    $('#comCheck7').prop({'disabled': false, 'checked': false});
                    $('#comCheck8').prop({'disabled': false, 'checked': false});
                    $('#comCheck9').prop({'disabled': false, 'checked': false});
                    $('#comCheck10').prop({'disabled': false, 'checked': false});
                }
            });

            @if(is_null(old('comCheck')))
                $('#comCheck1').prop('checked', true);
            @endif

            $('#rec_ispregnant').val("N/A");
            $('#PregnantLMP').prop({disabled: true, required: false});
            $('#highRiskPregnancy').prop({disabled: true, required: false});

            $('#imagingDone').change(function (e) { 
                e.preventDefault();
                $('#divImagingOthers').hide();
                $('#imagingOtherFindings').val("");
                if($(this).val() == "None") {
                    $('#imagingDoneDate').prop({disabled: true, required: false});
                    $('#imagingResult').prop({disabled: true, required: false});
                    $("#imagingResult").empty();
                }
                else {
                    $('#imagingDoneDate').prop({disabled: false, required: true});
                    $('#imagingResult').prop({disabled: false, required: true});
                    $("#imagingResult").empty();
                    $("#imagingResult").append(new Option("Normal", "NORMAL"));
                    $("#imagingResult").append(new Option("Pending", "PENDING"));

                    $('#divImagingOthers').hide();

                    if($(this).val() == "Chest Radiography") {
                        $("#imagingResult").append(new Option("Hazy opacities, often rounded in morphology, with peripheral and lower lung dist.", "HAZY"));
                    }
                    else if($(this).val() == "Chest CT") {
                        $("#imagingResult").append(new Option("Multiple bilateral ground glass opacities, often rounded in morphology, w/ peripheral and lower lung dist.", "MULTIPLE"));
                    }
                    else if($(this).val() == "Lung Ultrasound") {
                        $("#imagingResult").append(new Option("Thickened pleural lines, B lines, consolidative patterns with or without air bronchograms.", "THICKENED"));
                    }
                    
                    if($(this).val() != "OTHERS") {
                        $("#imagingResult").append(new Option("Other findings", "OTHERS"));
                    }
                }
            }).trigger('change');

            $('#imagingResult').change(function (e) { 
                e.preventDefault();
                $('#imagingOtherFindings').val("");
                if($(this).val() == "OTHERS") {
                    $('#divImagingOthers').show();
                    $('imagingOtherFindings').prop({disabled: false, required: true});
                }
                else {
                    $('#divImagingOthers').hide();
                    $('imagingOtherFindings').prop({disabled: true, required: false});
                }
            }).trigger('change');

            $('#testType1').change(function (e) { 
                e.preventDefault();
                if($(this).val() == 'Others') {
                    $('#divTypeOthers1').show();
                    $('#testTypeOtherRemarks1').prop('required', true);
                }
                else {
                    $('#divTypeOthers1').hide();
                    $('#testTypeOtherRemarks1').empty();
                    $('#testTypeOtherRemarks1').prop('required', false);
                }
            }).trigger('change');

            $('#testResult1').change(function (e) { 
                e.preventDefault();
                if($(this).val() == "OTHERS") {
                    $('#divResultOthers1').show();
                    $('#testResultOtherRemarks1').prop('required', true);
                }
                else {
                    $('#divResultOthers1').hide();
                    $('#testResultOtherRemarks1').empty();
                    $('#testResultOtherRemarks1').prop('required', false);
                }
            }).trigger('change');

            $('#testType2').change(function (e) { 
                e.preventDefault();
                if($(this).val() == 'Others') {
                    $('#divTypeOthers2').show();
                    $('#testTypeOtherRemarks2').prop('required', true);
                }
                else {
                    $('#divTypeOthers2').hide();
                    $('#testTypeOtherRemarks2').empty();
                    $('#testTypeOtherRemarks2').prop('required', false);
                }
            }).trigger('change');

            $('#testResult2').change(function (e) { 
                e.preventDefault();
                if($(this).val() == "OTHERS") {
                    $('#divResultOthers2').show();
                    $('#testResultOtherRemarks2').prop('required', true);
                }
                else {
                    $('#divResultOthers2').hide();
                    $('#testResultOtherRemarks2').empty();
                    $('#testResultOtherRemarks2').prop('required', false);
                }
            }).trigger('change');

            $('#testedPositiveUsingRTPCRBefore').change(function (e) { 
                e.preventDefault();
                if($(this).val() == "1") {
                    $('#divIfTestedPositiveUsingRTPCR').show();
                    $('#testedPositiveLab').prop('required', true);
                    $('#testedPositiveSpecCollectedDate').prop('required', true);
                }
                else {
                    $('#divIfTestedPositiveUsingRTPCR').hide();
                    $('#testedPositiveLab').prop('required', false);
                    $('#testedPositiveSpecCollectedDate').prop('required', false);
                }
            }).trigger('change');

            $('#outcomeCondition').change(function (e) { 
                e.preventDefault();
                if($(this).val() == 'Recovered') {
                    $('#ifOutcomeRecovered').show();
                    $('#outcomeRecovDate').prop('required', true);
                    $('#ifOutcomeDied').hide();
                    $('#outcomeDeathDate').prop('required', false);
                    $('#deathImmeCause').prop('required', false);
                    $('#deathAnteCause').prop('required', false);
                    $('#deathUndeCause').prop('required', false);
                    $('#contriCondi').prop('required', false);
                }
                else if($(this).val() == 'Died') {
                    $('#ifOutcomeRecovered').hide();
                    $('#outcomeRecovDate').prop('required', false);
                    $('#ifOutcomeDied').show();
                    $('#outcomeDeathDate').prop('required', true);
                    $('#deathImmeCause').prop('required', true);
                    $('#deathAnteCause').prop('required', true);
                    $('#deathUndeCause').prop('required', true);
                    $('#contriCondi').prop('required', true);
                }
                else {
                    $('#ifOutcomeRecovered').hide();
                    $('#outcomeRecovDate').prop('required', false);
                    $('#ifOutcomeDied').hide();
                    $('#outcomeDeathDate').prop('required', false);
                    $('#deathImmeCause').prop('required', false);
                    $('#deathAnteCause').prop('required', false);
                    $('#deathUndeCause').prop('required', false);
                    $('#contriCondi').prop('required', false);
                }
            }).trigger('change');

            $('#expoitem1').change(function (e) { 
                e.preventDefault();
                if($(this).val() == "1") {
                    $('#divExpoitem1').show();
                    $('#expoDateLastCont').prop('required', true);
                }
                else {
                    $('#divExpoitem1').hide();
                    $('#expoDateLastCont').prop('required', false);
                }
            }).trigger('change');

            $('#expoitem2').change(function (e) { 
                e.preventDefault();
                if($(this).val() == 0 || $(this).val() == 3) {
                    $('#divTravelInt').hide();
                    $('#divTravelLoc').hide();
                }
                else if($(this).val() == 1) {
                    $('#divTravelInt').hide();

                    $('#intCountry').prop('required', false);
                    $('#intDateFrom').prop('required', false);
                    $('#intDateTo').prop('required', false);
                    $('#intWithOngoingCovid').prop('required', false);
                    $('#intVessel').prop('required', false);
                    $('#intVesselNo').prop('required', false);
                    $('#intDateDepart').prop('required', false);
                    $('#intDateArrive').prop('required', false);
                    
                    $('#divTravelLoc').show();
                }
                else if($(this).val() == 2) {
                    $('#divTravelInt').show();

                    $('#intCountry').prop('required', true);
                    $('#intDateFrom').prop('required', false);
                    $('#intDateTo').prop('required', false);
                    $('#intWithOngoingCovid').prop('required', false);
                    $('#intVessel').prop('required', false);
                    $('#intVesselNo').prop('required', false);
                    $('#intDateDepart').prop('required', false);
                    $('#intDateArrive').prop('required', false);

                    $('#divTravelLoc').hide();
                }
            }).trigger('change');

            $('#placevisited1').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divLocal1').show();

                    $('#locName1').prop('required', true);
                    $('#locAddress1').prop('required', true);
                    $('#locDateFrom1').prop('required', true);
                    $('#locDateTo1').prop('required', true);
                    $('#locWithOngoingCovid1').prop('required', true);
                }
                else {
                    $('#divLocal1').hide();

                    $('#locName1').prop('required', false);
                    $('#locAddress1').prop('required', false);
                    $('#locDateFrom1').prop('required', false);
                    $('#locDateTo1').prop('required', false);
                    $('#locWithOngoingCovid1').prop('required', false);
                }
            }).trigger('change');

            $('#placevisited2').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divLocal2').show();

                    $('#locName2').prop('required', true);
                    $('#locAddress2').prop('required', true);
                    $('#locDateFrom2').prop('required', true);
                    $('#locDateTo2').prop('required', true);
                    $('#locWithOngoingCovid2').prop('required', true);
                }
                else {
                    $('#divLocal2').hide();

                    $('#locName2').prop('required', false);
                    $('#locAddress2').prop('required', false);
                    $('#locDateFrom2').prop('required', false);
                    $('#locDateTo2').prop('required', false);
                    $('#locWithOngoingCovid2').prop('required', false);
                }
            }).trigger('change');

            $('#placevisited3').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divLocal3').show();

                    $('#locName3').prop('required', true);
                    $('#locAddress3').prop('required', true);
                    $('#locDateFrom3').prop('required', true);
                    $('#locDateTo3').prop('required', true);
                    $('#locWithOngoingCovid3').prop('required', true);
                }
                else {
                    $('#divLocal3').hide();

                    $('#locName3').prop('required', false);
                    $('#locAddress3').prop('required', false);
                    $('#locDateFrom3').prop('required', false);
                    $('#locDateTo3').prop('required', false);
                    $('#locWithOngoingCovid3').prop('required', false);
                }
            }).trigger('change');

            $('#placevisited4').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divLocal4').show();

                    $('#locName4').prop('required', true);
                    $('#locAddress4').prop('required', true);
                    $('#locDateFrom4').prop('required', true);
                    $('#locDateTo4').prop('required', true);
                    $('#locWithOngoingCovid4').prop('required', true);
                }
                else {
                    $('#divLocal4').hide();

                    $('#locName4').prop('required', false);
                    $('#locAddress4').prop('required', false);
                    $('#locDateFrom4').prop('required', false);
                    $('#locDateTo4').prop('required', false);
                    $('#locWithOngoingCovid4').prop('required', false);
                }
            }).trigger('change');

            $('#placevisited5').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divLocal5').show();

                    $('#locName5').prop('required', true);
                    $('#locAddress5').prop('required', true);
                    $('#locDateFrom5').prop('required', true);
                    $('#locDateTo5').prop('required', true);
                    $('#locWithOngoingCovid5').prop('required', true);
                }
                else {
                    $('#divLocal5').hide();

                    $('#locName5').prop('required', false);
                    $('#locAddress5').prop('required', false);
                    $('#locDateFrom5').prop('required', false);
                    $('#locDateTo5').prop('required', false);
                    $('#locWithOngoingCovid5').prop('required', false);
                }
            }).trigger('change');

            $('#placevisited6').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divLocal6').show();

                    $('#locName6').prop('required', true);
                    $('#locAddress6').prop('required', true);
                    $('#locDateFrom6').prop('required', true);
                    $('#locDateTo6').prop('required', true);
                    $('#locWithOngoingCovid6').prop('required', true);
                }
                else {
                    $('#divLocal6').hide();

                    $('#locName6').prop('required', false);
                    $('#locAddress6').prop('required', false);
                    $('#locDateFrom6').prop('required', false);
                    $('#locDateTo6').prop('required', false);
                    $('#locWithOngoingCovid6').prop('required', false);
                }
            }).trigger('change');

            $('#placevisited7').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divLocal7').show();

                    $('#locName7').prop('required', true);
                    $('#locAddress7').prop('required', true);
                    $('#locDateFrom7').prop('required', true);
                    $('#locDateTo7').prop('required', true);
                    $('#locWithOngoingCovid7').prop('required', true);
                }
                else {
                    $('#divLocal7').hide();

                    $('#locName7').prop('required', false);
                    $('#locAddress7').prop('required', false);
                    $('#locDateFrom7').prop('required', false);
                    $('#locDateTo7').prop('required', false);
                    $('#locWithOngoingCovid7').prop('required', false);
                }
            }).trigger('change');

            $('#placevisited8').change(function (e) { 
                e.preventDefault();
                if($(this).prop('checked') == true) {
                    $('#divLocal8').show();

                    //baguhin kapag kailangan kapag naka-check
                    $('#localVessel1').prop('required', false);
                    $('#localVesselNo1').prop('required', false);
                    $('#localOrigin1').prop('required', false);
                    $('#localDateDepart1').prop('required', false);
                    $('#localDest1').prop('required', false);
                    $('#localDateArrive1').prop('required', false);

                    $('#localVessel2').prop('required', false);
                    $('#localVesselNo2').prop('required', false);
                    $('#localOrigin2').prop('required', false);
                    $('#localDateDepart2').prop('required', false);
                    $('#localDest2').prop('required', false);
                    $('#localDateArrive2').prop('required', false);
                }
                else {
                    $('#divLocal8').hide();

                    $('#localVessel1').prop('required', false);
                    $('#localVesselNo1').prop('required', false);
                    $('#localOrigin1').prop('required', false);
                    $('#localDateDepart1').prop('required', false);
                    $('#localDest1').prop('required', false);
                    $('#localDateArrive1').prop('required', false);

                    $('#localVessel2').prop('required', false);
                    $('#localVesselNo2').prop('required', false);
                    $('#localOrigin2').prop('required', false);
                    $('#localDateDepart2').prop('required', false);
                    $('#localDest2').prop('required', false);
                    $('#localDateArrive2').prop('required', false);

                    $('localVessel1').val("");
                    $('localVesselNo1').val("");
                    $('localOrigin1').val("");
                    $('localDateDepart1').val("");
                    $('localDest1').val("");
                    $('localDateArrive1').val("");

                    $('localVessel2').val("");
                    $('localVesselNo2').val("");
                    $('localOrigin2').val("");
                    $('localDateDepart2').val("");
                    $('localDest2').val("");
                    $('localDateArrive2').val("");
                }
            }).trigger('change');
        });

        function fetchRecords(id) {
                $.ajax({
                    url: "/ajaxGetUserRecord/"+id,
                    type: "get",
                    dataType: "json",
                    cache: false,
                    processData: false,
                    error: function(xhr, status, error) {
                        var err = JSON.parse(xhr.responseText);
                        alert(err.Message);
                    },
                    success: function (response) {
                        if(response['data'][0].philhealth == null) {
                            var recph = "N/A"
                        }
                        else {
                            var recph = response['data'][0].philhealth;
                        }

                        $('#rec_philhealth').val(recph);
                        $('#rec_lname').val(response['data'][0].lname);
                        $('#rec_fname').val(response['data'][0].fname);
                        $('#rec_mname').val(response['data'][0].mname);

                        var formattedDate = new Date(response['data'][0].bdate);
                        var d = formattedDate.getDate();
                        var m =  formattedDate.getMonth();
                        m += 1;  // JavaScript months are 0-11
                        var y = formattedDate.getFullYear();

                        if (d < 10) {
                            d = "0" + d;
                        }
                        if (m < 10) {
                            m = "0" + m;
                        }

                        $('#rec_bdate').val(m + "/" + d + "/" + y);
                        
                        dob = new Date(response['data'][0].bdate);
                        var today = new Date();
                        var age = Math.floor((today-dob) / (365.25 * 24 * 60 * 60 * 1000));
                        $('#rec_age').val(age);
                        $('#rec_gender').val(response['data'][0].gender);
                        $('#rec_cs').val(response['data'][0].cs);
                        $('#rec_nationality').val(response['data'][0].nationality);
                        $('#rec_occupation').val((response['data'][0].occupation == null) ? "N/A" : response['data'][0].occupation);
                        $('#rec_wiacs').val(response['data'][0].worksInClosedSetting);
                        $('#rec_lot').val(response['data'][0].address_houseno);
                        $('#rec_street').val(response['data'][0].address_street);
                        $('#rec_brgy').val(response['data'][0].address_brgy);
                        $('#rec_city').val(response['data'][0].address_city);
                        $('#rec_province').val(response['data'][0].address_province);
                        $('#rec_phoneno').val((response['data'][0].phoneno == null) ? "N/A" : response['data'][0].phoneno);
                        $('#rec_mobile').val(response['data'][0].mobile);
                        $('#rec_email').val((response['data'][0].email == null) ? "N/A" : response['data'][0].email);

                        $('#rec_olot').val((response['data'][0].occupation_lotbldg == null) ? "N/A" : response['data'][0].occupation_lotbldg);
                        $('#rec_ostreet').val((response['data'][0].occupation_street == null) ? "N/A" : response['data'][0].occupation_street);
                        $('#rec_obrgy').val((response['data'][0].occupation_brgy == null) ? "N/A" : response['data'][0].occupation_brgy);
                        $('#rec_ocity').val((response['data'][0].occupation_city == null) ? "N/A" : response['data'][0].occupation_city);
                        $('#rec_oprovince').val((response['data'][0].occupation_province == null) ? "N/A" : response['data'][0].occupation_province);
                        $('#rec_oname').val((response['data'][0].occupation_name == null) ? "N/A" : response['data'][0].occupation_name);
                        $('#rec_omobile').val((response['data'][0].occupation_mobile == null) ? "N/A" : response['data'][0].occupation_mobile);
                        $('#rec_oemail').val((response['data'][0].occupation_email == null) ? "N/A" : response['data'][0].occupation_email);

                        $('#rec_plot').val((response['data'][0].permaaddress_houseno == null) ? "N/A" : response['data'][0].permaaddress_houseno);
                        $('#rec_pstreet').val((response['data'][0].permaaddress_street == null) ? "N/A" : response['data'][0].permaaddress_street);
                        $('#rec_pbrgy').val((response['data'][0].permaaddress_brgy == null) ? "N/A" : response['data'][0].permaaddress_brgy);
                        $('#rec_pcity').val((response['data'][0].permaaddress_city == null) ? "N/A" : response['data'][0].permaaddress_city);
                        $('#rec_pprovince').val((response['data'][0].permaaddress_province == null) ? "N/A" : response['data'][0].permaaddress_province);
                        $('#rec_pphone').val((response['data'][0].permaphoneno == null) ? "N/A" : response['data'][0].permaphoneno);
                        $('#rec_pmobile').val((response['data'][0].permamobile == null) ? "N/A" : response['data'][0].permamobile);
                        $('#rec_pemail').val((response['data'][0].permaemail == null) ? "N/A" : response['data'][0].permaemail);

                        $('#rec_ispregnant').val((response['data'][0].isPregnant == 1) ? "Yes" : "No");

                        if(response['data'][0].isPregnant == 1) {
                            $('#PregnantLMP').prop({disabled: false, required: true});
                            $('#highRiskPregnancy').prop({disabled: false, required: true});
                        }
                        else {
                            $('#PregnantLMP').prop({disabled: true, required: false});
                            $('#highRiskPregnancy').prop({disabled: true, required: false});
                        }
                    }
                });
            }
    </script>
@endsection