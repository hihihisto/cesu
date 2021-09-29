@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="/forms/{{$id}}/create" method="POST">
            @csrf       
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div>eCIF (version 9) - Create</div>
                        <div>
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#appendix"><i class="fa fa-file mr-2" aria-hidden="true"></i>Appendix</button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info" role="alert">
                        <p>1.) The Case Investigation Form (CIF) is meant to be administered as an interview by a health care worker or any personnel of the DRU. <b>This is not a self-administered questionnaire.</b></p>
                        <p>2.) Please be advised that DRUs are only allowed to obtain <b>1 copy of accomplished CIF</b> from a patient.</p>
                        <p>3.) Please fill out all blanks and put a check mark on the appropriate box. <b>Items with asterisk mark <span class="text-danger">(*)</span> are required fields.</b></p>
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
                        <label for=""><span class="text-danger font-weight-bold">*</span>Currently Creating CIF record for</label>
                        <input type="text" class="form-control" value="{{$records->lname}}, {{$records->fname}} {{$records->mname}} | {{$records->getAge().'/'.substr($records->gender, 0, 1)}} | {{date('m/d/Y', strtotime($records->bdate))}}" disabled>
                    </div>
                    <div class="form-group">
                      <label for="remarks">Remarks</label>
                      <textarea class="form-control" name="remarks" id="remarks" rows="3">{{old('remarks')}}</textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="morbidityMonth">Morbidity Month (MM)</label>
                              <input type="text" class="form-control" id="morbidityMonth" name="morbidityMonth" min="2020-01-01" max="{{date('Y-m-d')}}" value="{{old('morbidityMonth', date('m/d/Y'))}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="">Morbidity Week (MW)</label>
                              <input type="text" class="form-control" value="{{!is_null(old('morbidityMonth')) ? date('W', strtotime(old('morbidityMonth'))) : date('W')}}" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                      <label for="dateReported"><span class="text-danger font-weight-bold">*</span>Date Reported</label>
                      <input type="date" class="form-control" name="dateReported" id="dateReported" min="2020-01-01" max="{{date('Y-m-d')}}" value="{{old('dateReported', date('Y-m-d'))}}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                              <label for="drunit"><span class="text-danger font-weight-bold">*</span>Disease Reporting Unit (DRU)</label>
                              <input type="text" class="form-control" name="drunit" id="drunit" value="{{old('drunit', 'CHO GENERAL TRIAS')}}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="drregion"><span class="text-danger font-weight-bold">*</span>DRU Region</label>
                                        <input type="text" class="form-control" name="drregion" id="drregion" value="{{old('drunit', '4A')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="drprovince"><span class="text-danger font-weight-bold">*</span>DRU Province</label>
                                        <input type="text" class="form-control" name="drprovince" id="drprovince" value="{{old('drunit', 'CAVITE')}}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for=""><span class="text-danger font-weight-bold">*</span>Philhealth No.</label>
                                <input type="text" name="" id="" class="form-control" value="{{(is_null($records->philhealth)) ? 'N/A' : $records->philhealth}}" disabled>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            @if(!auth()->user()->isCesuAccount())
                            <div class="form-group">
                                <label for="interviewerName"><span class="text-danger font-weight-bold">*</span>Name of Interviewer</label>
                                <input type="text" name="interviewerName" id="interviewerName" class="form-control" value="{{(!is_null(auth()->user()->defaultInterviewer())) ? auth()->user()->defaultInterviewer() : auth()->user()->name}}" readonly required>
                            </div>
                            @else
                            <div class="form-group">
                                <label for="interviewerName"><span class="text-danger font-weight-bold">*</span>Name of Interviewer</label>
                                <select name="interviewerName" id="interviewerName" required>
                                    <option value="" disabled {{(empty(old('interviewerName'))) ? 'selected' : ''}}>Choose...</option>
                                    @foreach($interviewers as $key => $interviewer)
                                        <option value="{{$interviewer->lname.", ".$interviewer->fname}}" {{(old('interviewerName') == $interviewer->lname.", ".$interviewer->fname) ? 'selected' : ''}}>{{$interviewer->lname.", ".$interviewer->fname." ".$interviewer->mname}}{{(!is_null($interviewer->brgy_id)) ? " (".$interviewer->brgy->brgyName.")" : ''}}{{(!is_null($interviewer->desc)) ? " - ".$interviewer->desc : ""}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-4">
                            @php
                                if(!is_null(auth()->user()->brgy_id) || !is_null(auth()->user()->company_id)) {
                                    $intMobile = '09190664324';
                                }
                                else {
                                    $intMobile = '09190664324';
                                }
                            @endphp
                            <div class="form-group">
                                <label for="interviewerMobile"><span class="text-danger font-weight-bold">*</span>Contact Number of Interviewer</label>
                                <input type="number" name="interviewerMobile" id="interviewerMobile" class="form-control" value="{{old('interviewerMobile', $intMobile)}}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="interviewDate"><span class="text-danger font-weight-bold">*</span>Date of Interview</label>
                                <input type="date" name="interviewDate" id="interviewDate" class="form-control" value="{{old('interviewDate', date('Y-m-d'))}}" max="{{date('Y-m-d')}}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="informantName">Name of Informant <small><i>(If patient unavailable)</i></small></label>
                                <input type="text" name="informantName" id="informantName" class="form-control" value="{{old('informantName')}}" style="text-transform: uppercase;">
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
                                        <input class="form-check-input" type="checkbox" value="1" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("1", old('existingCaseList'))) ? 'checked' : 'checked'}}>
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
                                            Update health status / outcome
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="5" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("5", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Update case classification
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="6" id="" name="existingCaseList[]" required {{(is_array(old('existingCaseList')) && in_array("6", old('existingCaseList'))) ? 'checked' : ''}}>
                                        <label class="form-check-label" for="">
                                            Update vaccination
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
                                            <input type="text" name="ecOthersRemarks" id="ecOthersRemarks" value="{{old('ecOthersRemarks')}}" class="form-control" style="text-transform: uppercase;">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="pType"><span class="text-danger font-weight-bold">*</span>Type of Client</label>
                                <select class="form-control" name="pType" id="pType" required>
                                <option value="PROBABLE" @if(old('pType') == "PROBABLE"){{'selected'}}@endif>COVID-19 Case (Suspect, Probable, or Confirmed)</option>
                                <option value="CLOSE CONTACT" @if(old('pType') == "CLOSE CONTACT"){{'selected'}}@endif>Close Contact</option>
                                <option value="TESTING" @if(old('pType') == "TESTING"){{'selected'}}@endif>For RT-PCR Testing (Not a Case of Close Contact)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                              <label for="isForHospitalization"><span class="text-danger font-weight-bold">*</span>For Hospitalization</label>
                              <select class="form-control" name="isForHospitalization" id="isForHospitalization" required>
                                <option value="1" {{(old('isForHospitalization') == 1) ? 'selected' : ''}}>Yes</option>
                                <option value="0" {{(old('isForHospitalization') == 0) ? 'selected' : 'selected'}}>No</option>
                              </select>
                            </div>
                        </div>
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
                                                <input type="text" class="form-control" value="{{$records->lname}}" id="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">First Name</label>
                                                <input type="text" class="form-control" value="{{$records->fname}}" id="" disabled>
                                            </div>
                                        </div> 
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Middle Name</label>
                                                <input type="text" class="form-control" value="{{$records->mname}}" id="" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Birthdate (MM/DD/YYYY)</label>
                                                <input type="text" class="form-control" value="{{date('m/d/Y', strtotime($records->bdate))}}" id="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Age</label>
                                                <input type="text" class="form-control" value="{{$records->getAge($records->bdate)}}" id="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Gender</label>
                                                <input type="text" class="form-control" value="{{$records->gender}}" id="" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="">Civil Status</label>
                                                <input type="text" class="form-control" value="{{$records->cs}}" id="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="">Nationality</label>
                                                <input type="text" class="form-control" value="{{$records->nationality}}" id="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Occupation</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->occupation)) ? 'N/A' : $records->occupation}}" id="" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Works in a Closed Setting</label>
                                                <input type="text" class="form-control" value="{{$records->worksInClosedSetting}}" disabled>
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
                                                <input type="text" class="form-control" value="{{$records->address_houseno}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Street/Purok/Sitio</label>
                                                <input type="text" class="form-control" value="{{$records->address_street}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Barangay</label>
                                                <input type="text" class="form-control" value="{{$records->address_brgy}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Municipality/City</label>
                                                <input type="text" class="form-control" value="{{$records->address_city}}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Province</label>
                                                <input type="text" class="form-control" value="{{$records->address_province}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Home Phone No. (& Area Code)</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->phoneno)) ? 'N/A' : $records->phoneno}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Cellphone No.</label>
                                                <input type="text" class="form-control" value="{{$records->mobile}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Email Address</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->email)) ? 'N/A' : $records->email}}" disabled>
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
                                                <input type="text" class="form-control" value="{{(is_null($records->permaaddress_houseno)) ? "N/A" : $records->permaaddress_houseno}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Street/Purok/Sitio</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->permaaddress_street)) ? "N/A" : $records->permaaddress_street}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Barangay</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->permaaddress_brgy)) ? "N/A" : $records->permaaddress_brgy}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Municipality/City</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->permaaddress_city)) ? "N/A" : $records->permaaddress_city}}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Province</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->permaaddress_province)) ? "N/A" : $records->permaaddress_province}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Home Phone No. (& Area Code)</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->permaphoneno)) ? "N/A" : $records->permaphoneno}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Cellphone No.</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->permamobile)) ? "N/A" : $records->permamobile}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Email Address</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->permaemail)) ? "N/A" : $records->permaemail}}" disabled>
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
                                                <input type="text" class="form-control" value="{{(is_null($records->occupation_lotbldg)) ? 'N/A' : $records->occupation_lotbldg}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Street</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->occupation_street)) ? 'N/A' : $records->occupation_street}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Barangay</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->occupation_brgy)) ? 'N/A' : $records->occupation_brgy}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Municipality/City</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->occupation_city)) ? 'N/A' : $records->occupation_city}}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Province</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->occupation_province)) ? 'N/A' : $records->occupation_province}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Name of Workplace</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->occupation_name)) ? 'N/A' : $records->occupation_name}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Phone No./Cellphone No.</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->occupation_mobile)) ? 'N/A' : $records->occupation_mobile}}" disabled>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="">Email Address</label>
                                                <input type="text" class="form-control" value="{{(is_null($records->occupation_email)) ? 'N/A' : $records->occupation_email}}" disabled>
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
                                                            <input type="text" class="form-control" name="healthCareCompanyName" id="healthCareCompanyName" value="{{old('healthCareCompanyName')}}" style="text-transform: uppercase;">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="healthCareCompanyLocation"><span class="text-danger font-weight-bold">*</span>Location</label>
                                                            <input type="text" class="form-control" name="healthCareCompanyLocation" id="healthCareCompanyLocation" value="{{old('healthCareCompanyLocation')}}" style="text-transform: uppercase;">
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
                                                  <label for="OFWPassportNo"><span class="text-danger font-weight-bold">*</span>Passport No.</label>
                                                  <input type="text" class="form-control" name="OFWPassportNo" id="OFWPassportNo" value="{{old('OFWPassportNo')}}" style="text-transform: uppercase;">
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
                                        <div class="col-md-4">
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
                                                <div class="form-group">
                                                    <label for="FNTPassportNo"><span class="text-danger font-weight-bold">*</span>Passport No.</label>
                                                    <input type="text" class="form-control" name="FNTPassportNo" id="FNTPassportNo" value="{{old('FNTPassportNo')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
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
                                        <div class="col-md-4">
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
                                                          <input type="text" class="form-control" name="institutionType" id="institutionType" value="{{old('institutionType')}}" style="text-transform: uppercase;">
                                                          <small><i>(e.g. prisons, residential facilities, retirement communities, care homes, camps etc.)</i></small>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="institutionName"><span class="text-danger font-weight-bold">*</span>Name of Institution</label>
                                                            <input type="text" class="form-control" name="institutionName" id="institutionName" value="{{old('institutionName')}}" style="text-transform: uppercase;">
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
                                                            <input type="text" class="form-control" name="facilityNameOfFirstConsult" id="facilityNameOfFirstConsult" value="{{old('facilityNameOfFirstConsult')}}" style="text-transform: uppercase;">
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
                                                    <option value="1" {{(old('dispositionType') == 1) ? 'selected' : ''}}>Admitted in hospital</option>
                                                    <option value="2" {{(old('dispositionType') == 2) ? 'selected' : ''}}>Admitted in isolation/quarantine facility</option>
                                                    <option value="3" {{(old('dispositionType') == 3 || is_null(old('dispositionType'))) ? 'selected' : ''}}>In home isolation/quarantine</option>
                                                    <option value="4" {{(old('dispositionType') == 4) ? 'selected' : ''}}>Discharged to home</option>
                                                    <option value="5" {{(old('dispositionType') == 5) ? 'selected' : ''}}>Others</option>
                                                </select>
                                            </div>
                                            <div id="divYes5">
                                                <div class="form-group">
                                                    <label for="dispositionName" id="dispositionlabel"></label>
                                                    <input type="text" class="form-control" name="dispositionName" id="dispositionName" value="{{old('dispositionName')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                            <div id="divYes6">
                                                <div class="form-group">
                                                    <label for="dispositionDate" id="dispositiondatelabel"></label>
                                                    <input type="datetime-local" class="form-control" name="dispositionDate" id="dispositionDate" value="{{old('dispositionDate', date('Y-m-d\TH:i'))}}">
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
                                                    <option value="Suspect" {{(old('caseClassification') == 'Suspect') ? 'selected' : 'selected'}}>Suspect</option>
                                                    <option value="Confirmed" {{(old('caseClassification') == 'Confirmed') ? 'selected' : ''}}>Confirmed</option>
                                                    <option value="Non-COVID-19 Case" {{(old('caseClassification') == 'Non-COVID-19 Case') ? 'selected' : ''}}>Non-COVID-19 Case</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">2.5 Vaccination Information</div>
                                <div class="card-body">
                                    <div class="form-group">
                                      <label for="howManyDoseVaccine"><span class="text-danger font-weight-bold">*</span>If vaccinated, how many dose?</label>
                                      <select class="form-control" name="howManyDoseVaccine" id="howManyDoseVaccine">
                                        <option value="" {{(is_null(old('howManyDoseVaccine'))) ? 'selected' : ''}}>N/A</option>
                                        <option value="1" {{(old('howManyDoseVaccine') == '1') ? 'selected' : ''}}>1st Dose only</option>
                                        <option value="2" {{(old('howManyDoseVaccine') == '2') ? 'selected' : ''}}>1st and 2nd Dose Completed</option>
                                      </select>
                                    </div>
                                    <div id="ifVaccinated">
                                        <div class="form-group">
                                          <label for="vaccineName"><span class="text-danger font-weight-bold">*</span>Name of Vaccine</label>
                                          <select class="form-control" name="vaccineName" id="vaccineName">
                                            <option value="" disabled {{is_null(old('vaccineName')) ? 'selected' : ''}}>Choose...</option>
                                            <option value="BHARAT BIOTECH" {{(old('vaccineName') == "BHARAT BIOTECH") ? 'selected' : ''}}>Bharat BioTech</option>
                                            <option value="GAMALEYA SPUTNIK V" {{(old('vaccineName') == 'GAMALEYA SPUTNIK V') ? 'selected' : ''}}>Gamaleya Sputnik V</option>
                                            <option value="JANSSEN" {{(old('vaccineName') == "JANSSEN") ? 'selected' : ''}}>Janssen</option>
                                            <option value="MODERNA" {{(old('vaccineName') == 'MODERNA') ? 'selected' : ''}}>Moderna</option>
                                            <option value="NOVARAX" {{(old('vaccineName') == 'NOVARAX') ? 'selected' : ''}}>Novarax</option>
                                            <option value="OXFORD ASTRAZENECA" {{(old('vaccineName') == 'OXFORD ASTRAZENECA') ? 'selected' : ''}}>Oxford AstraZeneca</option>
                                            <option value="PFIZER BIONTECH" {{(old('vaccineName') == 'PFIZER BIONTECH') ? 'selected' : ''}}>Pfizer BioNTech</option>
                                            <option value="SINOPHARM" {{(old('vaccineName') == 'SINOPHARM') ? 'selected' : ''}}>Sinopharm</option>
                                            <option value="SINOVAC CORONAVAC" {{(old('vaccineName') == 'SINOVAC CORONAVAC') ? 'selected' : ''}}>Sinovac Coronavac</option>
                                          </select>
                                        </div>
                                        <hr>
                                        <div id="ifFirstDoseVaccine">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vaccinationDate1"><span class="text-danger font-weight-bold">*</span>First (1st) Dose Date</label>
                                                        <input type="date" class="form-control" name="vaccinationDate1" id="vaccinationDate1" value="{{old('vaccinationDate1')}}" max="{{date('Y-m-d')}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="haveAdverseEvents1"><span class="text-danger font-weight-bold">*</span>Adverse Event/s</label>
                                                        <select class="form-control" name="haveAdverseEvents1" id="haveAdverseEvents1">
                                                            <option value="0" {{(old('haveAdverseEvents1') == '0') ? 'selected' : ''}}>No</option>
                                                            <option value="1" {{(old('haveAdverseEvents1') == '1') ? 'selected' : ''}}>Yes</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vaccinationFacility1">Vaccination Center/Facility <small>(Optional)</small></label>
                                                        <input type="text" class="form-control" name="vaccinationFacility1" id="vaccinationFacility1" value="{{old('vaccinationFacility1')}}" style="text-transform: uppercase;">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vaccinationRegion1">Region of Health Facility <small>(Optional)</small></label>
                                                        <input type="text" class="form-control" name="vaccinationRegion1" id="vaccinationRegion1" value="{{old('vaccinationRegion1')}}" style="text-transform: uppercase;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="ifSecondDoseVaccine">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vaccinationDate2"><span class="text-danger font-weight-bold">*</span>Second (2nd) Dose Date</label>
                                                        <input type="date" class="form-control" name="vaccinationDate2" id="vaccinationDate2" value="{{old('vaccinationDate2')}}" max="{{date('Y-m-d')}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="haveAdverseEvents2"><span class="text-danger font-weight-bold">*</span>Adverse Event/s</label>
                                                        <select class="form-control" name="haveAdverseEvents2" id="haveAdverseEvents2">
                                                            <option value="0" {{(old('haveAdverseEvents2') == '0') ? 'selected' : ''}}>No</option>
                                                            <option value="1" {{(old('haveAdverseEvents2') == '1') ? 'selected' : ''}}>Yes</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vaccinationFacility2">Vaccination Center/Facility <small>(Optional)</small></label>
                                                        <input type="text" class="form-control" name="vaccinationFacility2" id="vaccinationFacility2" value="{{old('vaccinationFacility2')}}" style="text-transform: uppercase;">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="vaccinationRegion2">Region of Health Facility <small>(Optional)</small></label>
                                                        <input type="text" class="form-control" name="vaccinationRegion2" id="vaccinationRegion2" value="{{old('vaccinationRegion2')}}" style="text-transform: uppercase;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">2.6 Clinical Information</div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                              <label for="dateOnsetOfIllness">Date of Onset of Illness</label>
                                              <input type="date" class="form-control" name="dateOnsetOfIllness" id="dateOnsetOfIllness" max="{{date('Y-m-d')}}" value="{{old('dateOnsetOfIllness')}}">
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
                                                                  <label for="SASFeverDeg"><span class="text-danger font-weight-bold">*</span>Degrees (in Celcius)</label>
                                                                  <input type="number" class="form-control" name="SASFeverDeg" id="SASFeverDeg" min="1" value="{{old('SASFeverDeg', '38')}}">
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
                                                                  <label for="SASOtherRemarks"><span class="text-danger font-weight-bold">*</span>Specify Findings</label>
                                                                  <input type="text" class="form-control" name="SASOtherRemarks" id="SASOtherRemarks" value="{{old('SASOtherRemarks')}}" style="text-transform: uppercase;">
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
                                                                  <label for="COMOOtherRemarks"><span class="text-danger font-weight-bold">*</span>Specify Findings</label>
                                                                  <input type="text" class="form-control" name="COMOOtherRemarks" id="COMOOtherRemarks" value="{{old('COMOOtherRemarks')}}" style="text-transform: uppercase;">
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
                                                        <input type="text" class="form-control" value="{{($records->isPregnant == 1) ? "Yes" : "No"}}" disabled>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="PregnantLMP"><span class="text-danger font-weight-bold">*</span>LMP</label>
                                                        <input type="date" class="form-control" name="PregnantLMP" id="PregnantLMP" value="{{old('PregnantLMP')}}" {{($records->gender == "FEMALE" && $records->isPregnant == 1) ? '' : 'disabled'}}>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                              <label for="highRiskPregnancy"><span class="text-danger font-weight-bold">*</span>High Risk Pregnancy?</label>
                                              <select class="form-control" name="highRiskPregnancy" id="highRiskPregnancy" {{($records->gender == "FEMALE" && $records->isPregnant == 1) ? 'required' : 'disabled'}}>
                                                <option value="0" {{(old('highRiskPregnancy') == 0) ? 'selected' : ''}}>No</option>
                                                <option value="1" {{(is_null(old('highRiskPregnancy')) || old('highRiskPregnancy') == 1) ? 'selected' : ''}}>Yes</option>
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
                                                      <input type="date" class="form-control" name="imagingDoneDate" id="imagingDoneDate" value="{{old('imagingDoneDate')}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="form-group">
                                                      <label for="imagingDone">Imaging done</label>
                                                      <select class="form-control" name="imagingDone" id="imagingDone" required>
                                                        <option value="None" {{(old('imagingDone') == "None") ? 'selected' : ''}}>None</option>
                                                        <option value="Chest Radiography" {{(old('imagingDone') == "Chest Radiography") ? 'selected' : ''}}>Chest Radiography</option>
                                                        <option value="Chest CT" {{(old('imagingDone') == "Chest CT") ? 'selected' : ''}}>Chest CT</option>
                                                        <option value="Lung Ultrasound" {{(old('imagingDone') == "Lung Ultrasound") ? 'selected' : ''}}>Lung Ultrasound</option>
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
                                                          <input type="text" class="form-control" name="imagingOtherFindings" id="imagingOtherFindings" value="{{old('imagingOtherFindings')}}" style="text-transform: uppercase;">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-header">2.7 Laboratory Information</div>
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
                                                  <label for="testedPositiveLab">Laboratory</label>
                                                  <input type="text" class="form-control" name="testedPositiveLab" id="testedPositiveLab" value="{{old('testedPositiveLab')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                              <label for="testDateCollected1"><span class="text-danger font-weight-bold">*</span>1. Date Collected</label>
                                              <input type="date" class="form-control" name="testDateCollected1" id="testDateCollected1" min="{{date('Y-01-01')}}" max="{{date('Y-12-31')}}" value="{{old('testDateCollected1')}}" required>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="">Time Collected <small><i>(for ONI, Leave Blank for Auto-fillup)</i></small></label>
                                                <input type="time" name="oniTimeCollected1" id="oniTimeCollected1" class="form-control" value="{{old('oniTimeCollected1')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="testDateReleased1">Date released</label>
                                                <input type="date" class="form-control" name="testDateReleased1" id="testDateReleased1" min="{{date('Y-01-01')}}" value="{{old('testDateReleased1')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="testLaboratory1">Laboratory <small><i>(Leave Blank if N/A)</i></small></label>
                                                <input type="text" class="form-control" name="testLaboratory1" id="testLaboratory1" value="{{old('testLaboratory1')}}" style="text-transform: uppercase;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="testType1"><span class="text-danger font-weight-bold">*</span>Type of test</label>
                                                <select class="form-control" name="testType1" id="testType1" required>
                                                  <option value="OPS" {{(old('testType1') == 'OPS') ? 'selected' : ''}}>RT-PCR (OPS)</option>
                                                  <option value="NPS" {{(old('testType1') == 'NPS') ? 'selected' : ''}}>RT-PCR (NPS)</option>
                                                  <option value="OPS AND NPS" {{(old('testType1') == 'OPS AND NPS') ? 'selected' : ''}}>RT-PCR (OPS and NPS)</option>
                                                  <option value="ANTIGEN" {{(old('testType1') == 'ANTIGEN') ? 'selected' : ''}}>Antigen Test</option>
                                                  <option value="ANTIBODY" {{(old('testType1') == 'ANTIBODY') ? 'selected' : ''}}>Antibody Test</option>
                                                  <option value="OTHERS" {{(old('testType1') == 'OTHERS') ? 'selected' : ''}}>Others</option>
                                                </select>
                                            </div>
                                            <div id="divTypeOthers1">
                                                <div class="form-group">
                                                    <label for="testTypeOtherRemarks1"><span class="text-danger font-weight-bold">*</span>Specify Reason</label>
                                                    <input type="text" class="form-control" name="testTypeOtherRemarks1" id="testTypeOtherRemarks1" value="{{old('testTypeOtherRemarks1')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                            <div id="ifAntigen1">
                                                <div class="form-group">
                                                    <label for="antigenKit1"><span class="text-danger font-weight-bold">*</span>Antigen Kit</label>
                                                    <input type="text" class="form-control" name="antigenKit1" id="antigenKit1" value="{{old('antigenKit1')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
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
                                                      <label for="testResultOtherRemarks1"><span class="text-danger font-weight-bold">*</span>Specify</label>
                                                      <input type="text" class="form-control" name="testResultOtherRemarks1" id="testResultOtherRemarks1" value="{{old('testResultOtherRemarks1')}}" style="text-transform: uppercase;">
                                                  </div>
                                              </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="testDateCollected2">2. Date Collected</label>
                                                <input type="date" class="form-control" name="testDateCollected2" id="testDateCollected2" min="{{date('Y-01-01')}}" max="{{date('Y-12-31')}}" value="{{old('testDateCollected2')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="oniTimeCollected2">Time Collected <small><i>(for ONI, Leave Blank for Auto-fillup)</i></small></label>
                                                <input type="time" name="oniTimeCollected2" id="oniTimeCollected2" class="form-control" value="{{old('oniTimeCollected2')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="testDateReleased2">Date released</label>
                                                <input type="date" class="form-control" name="testDateReleased2" id="testDateReleased2" min="{{date('Y-01-01')}}" value="{{old('testDateReleased2')}}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="testLaboratory2">Laboratory <small><i>(Leave Blank if N/A)</i></small></label>
                                                <input type="text" class="form-control" name="testLaboratory2" id="testLaboratory2" value="{{old('testLaboratory2')}}" style="text-transform: uppercase;">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                              <label for="testType2"><span class="text-danger font-weight-bold">*</span>Type of test</label>
                                              <select class="form-control" name="testType2" id="testType2">
                                                    <option value="">N/A</option>
                                                    <option value="OPS" {{(old('testType2') == 'OPS') ? 'selected' : ''}}>RT-PCR (OPS)</option>
                                                    <option value="NPS" {{(old('testType2') == 'NPS') ? 'selected' : ''}}>RT-PCR (NPS)</option>
                                                    <option value="OPS AND NPS" {{(old('testType2') == 'OPS AND NPS') ? 'selected' : ''}}>RT-PCR (OPS and NPS)</option>
                                                    <option value="ANTIGEN" {{(old('testType2') == 'ANTIGEN') ? 'selected' : ''}}>Antigen Test</option>
                                                    <option value="ANTIBODY" {{(old('testType2') == 'ANTIBODY') ? 'selected' : ''}}>Antibody Test</option>
                                                    <option value="OTHERS" {{(old('testType2') == 'OTHERS') ? 'selected' : ''}}>Others</option>
                                              </select>
                                            </div>
                                            <div id="divTypeOthers2">
                                                <div class="form-group">
                                                  <label for="testTypeOtherRemarks2"><span class="text-danger font-weight-bold">*</span>Specify Type/Reason</label>
                                                  <input type="text" class="form-control" name="testTypeOtherRemarks2" id="testTypeOtherRemarks2" value="{{old('testTypeOtherRemarks2')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                            <div id="ifAntigen2">
                                                <div class="form-group">
                                                    <label for="antigenKit2"><span class="text-danger font-weight-bold">*</span>Antigen Kit</label>
                                                    <input type="text" class="form-control" name="antigenKit2" id="antigenKit2" value="{{old('antigenKit2')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                              <label for="testResult2"><span class="text-danger font-weight-bold">*</span>Results</label>
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
                                                    <label for="testResultOtherRemarks2"><span class="text-danger font-weight-bold">*</span>Specify</label>
                                                    <input type="text" class="form-control" name="testResultOtherRemarks2" id="testResultOtherRemarks2" value="{{old('testResultOtherRemarks2')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">2.8 Outcome/Condition at Time of Report</div>
                                <div class="card-body">
                                    <div class="form-group">
                                      <label for="outcomeCondition"><span class="text-danger font-weight-bold">*</span>Select Outcome/Condition</label>
                                      <select class="form-control" name="outcomeCondition" id="outcomeCondition" required>
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
                                                    <input type="text" class="form-control" name="deathImmeCause" id="deathImmeCause" value="{{old('deathImmeCause')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="deathAnteCause">Antecedent Cause</label>
                                                    <input type="text" class="form-control" name="deathAnteCause" id="deathAnteCause" value="{{old('deathAnteCause')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="deathUndeCause">Underlying Cause</label>
                                                    <input type="text" class="form-control" name="deathUndeCause" id="deathUndeCause" value="{{old('deathUndeCause')}}" style="text-transform: uppercase;">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="deathUndeCause">Contributory Conditions</label>
                                                    <input type="text" class="form-control" name="contriCondi" id="contriCondi" value="{{old('contriCondi')}}" style="text-transform: uppercase;">
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
                                          <option value="2" {{(old('expoitem2') == 2) ? 'selected' : ''}}>Yes, International</option>
                                          <option value="3" {{(old('expoitem2') == 3) ? 'selected' : ''}}>Unknown exposure</option>
                                        </select>
                                    </div>
                                    <div id="divTravelInt">
                                        <div class="form-group">
                                            <label for="intCountry"><span class="text-danger font-weight-bold">*</span>If International Travel, country of origin</label>
                                            <select class="form-control" name="intCountry" id="intCountry">
                                                <option value="" {{(is_null(old('intCountry'))) ? 'selected disabled' : ''}}>Choose...</option>
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
                                                                  <input type="date" class="form-control" name="intDateFrom" id="intDateFrom" value="{{old('intDateFrom')}}">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="intDateTo">From</label>
                                                                    <input type="date" class="form-control" name="intDateTo" id="intDateTo" value="{{old('intDateTo')}}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label for="intWithOngoingCovid">With ongoing COVID-19 community transmission?</label>
                                                    <select class="form-control" name="intWithOngoingCovid" id="intWithOngoingCovid">
                                                        <option value="NO" {{(old('intWithOngoingCovid') == "NO") ? 'selected' : ''}}>No</option>
                                                        <option value="YES" {{(old('intWithOngoingCovid') == "YES") ? 'selected' : ''}}>Yes</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                          <label for="intVessel">Airline/Sea vessel</label>
                                                          <input type="text" class="form-control" name="intVessel" id="intVessel" value="{{old('intVessel')}}" style="text-transform: uppercase;">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="intVesselNo">Flight/Vessel Number</label>
                                                            <input type="text" class="form-control" name="intVesselNo" id="intVesselNo" value="{{old('intVesselNo')}}" style="text-transform: uppercase;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="intDateDepart">Date of departure</label>
                                                            <input type="date" class="form-control" name="intDateDepart" id="intDateDepart" value="{{old('intDateDepart')}}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="intDateArrive">Date of arrival in PH</label>
                                                            <input type="date" class="form-control" name="intDateArrive" id="intDateArrive" value="{{old('intDateArrive')}}">
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
                                                    <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited1" value="Health Facility" {{(is_array(old('placevisited')) && in_array("Health Facility", old('placevisited'))) ? 'checked' : ''}}>
                                                    Health Facility
                                                  </label>
                                                </div>
                                                <div id="divLocal1" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName1">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName1" id="locName1" value="{{old('locName1')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress1">Location</label>
                                                                <input class="form-control" type="text" name="locAddress1" id="locAddress1" value="{{old('locAddress1')}}" style="text-transform: uppercase;">
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
                                                                                <input class="form-control" type="date" name="locDateFrom1" id="locDateFrom1" value="{{old('locDateFrom1')}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo1">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo1" id="locDateTo1" value="{{old('locDateTo1')}}">
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
                                                                <option value="NO" {{(old('locWithOngoingCovid1') == "NO") ? 'selected' : ''}}>No</option>
                                                                <option value="YES" {{(old('locWithOngoingCovid1') == "YES") ? 'selected' : ''}}>Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited2" value="Closed Settings" {{(is_array(old('placevisited')) && in_array("Cloed Settings", old('placevisited'))) ? 'checked' : ''}}>
                                                      Closed Settings
                                                    </label>
                                                </div>
                                                <div id="divLocal2" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName2">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName2" id="locName2" value="{{old('locName2')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress2">Location</label>
                                                                <input class="form-control" type="text" name="locAddress2" id="locAddress2" value="{{old('locAddress2')}}" style="text-transform: uppercase;">
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
                                                                                <input class="form-control" type="date" name="locDateFrom2" id="locDateFrom2" value="{{old('locDateFrom2')}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo2">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo2" id="locDateTo2" value="{{old('locDateTo2')}}">
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
                                                                <option value="NO" {{(old('locWithOngoingCovid2') == "NO") ? 'selected' : ''}}>No</option>
                                                                <option value="YES" {{(old('locWithOngoingCovid2') == "YES") ? 'selected' : ''}}>Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited3" value="School" {{(is_array(old('placevisited')) && in_array("School", old('placevisited'))) ? 'checked' : ''}}>
                                                      School
                                                    </label>
                                                </div>
                                                <div id="divLocal3" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName3">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName3" id="locName3" value="{{old('locName3')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress3">Location</label>
                                                                <input class="form-control" type="text" name="locAddress3" id="locAddress3" value="{{old('locAddress3')}}" style="text-transform: uppercase;">
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
                                                                                <input class="form-control" type="date" name="locDateFrom3" id="locDateFrom3" value="{{old('locDateFrom3')}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo3">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo3" id="locDateTo3" value="{{old('locDateTo3')}}">
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
                                                                <option value="NO" {{(old('locWithOngoingCovid3') == "NO") ? 'selected' : ''}}>No</option>
                                                                <option value="YES" {{(old('locWithOngoingCovid3') == "YES") ? 'selected' : ''}}>Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited4" value="Workplace" {{(is_array(old('placevisited')) && in_array("Workplace", old('placevisited'))) ? 'checked' : ''}}>
                                                      Workplace
                                                    </label>
                                                </div>
                                                <div id="divLocal4" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName4">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName4" id="locName4" value="{{old('locName4')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress4">Location</label>
                                                                <input class="form-control" type="text" name="locAddress4" id="locAddress4" value="{{old('locAddress4')}}" style="text-transform: uppercase;">
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
                                                                                <input class="form-control" type="date" name="locDateFrom4" id="locDateFrom4" value="{{old('locDateFrom4')}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo4">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo4" id="locDateTo4" value="{{old('locDateTo4')}}">
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
                                                                <option value="NO" {{(old('locWithOngoingCovid4') == "NO") ? 'selected' : ''}}>No</option>
                                                                <option value="YES" {{(old('locWithOngoingCovid4') == "YES") ? 'selected' : ''}}>Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited5" value="Market" {{(is_array(old('placevisited')) && in_array("Market", old('placevisited'))) ? 'checked' : ''}}>
                                                      Market
                                                    </label>
                                                </div>
                                                <div id="divLocal5" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName5">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName5" id="locName5" value="{{old('locName5')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress5">Location</label>
                                                                <input class="form-control" type="text" name="locAddress5" id="locAddress5" value="{{old('locAddress5')}}" style="text-transform: uppercase;">
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
                                                                                <input class="form-control" type="date" name="locDateFrom5" id="locDateFrom5" value="{{old('locDateFrom5')}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo5">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo5" id="locDateTo5" value="{{old('locDateTo5')}}">
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
                                                                <option value="NO" {{(old('locWithOngoingCovid5') == "NO") ? 'selected' : ''}}>No</option>
                                                                <option value="YES" {{(old('locWithOngoingCovid5') == "YES") ? 'selected' : ''}}>Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited6" value="Social Gathering" {{(is_array(old('placevisited')) && in_array("Social Gathering", old('placevisited'))) ? 'checked' : ''}}>
                                                      Social Gathering
                                                    </label>
                                                </div>
                                                <div id="divLocal6" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName6">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName6" id="locName6" value="{{old('locName6')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress6">Location</label>
                                                                <input class="form-control" type="text" name="locAddress6" id="locAddress6" value="{{old('locAddress6')}}" style="text-transform: uppercase;">
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
                                                                                <input class="form-control" type="date" name="locDateFrom6" id="locDateFrom6" value="{{old('locDateFrom6')}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo6">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo6" id="locDateTo6" value="{{old('locDateTo6')}}">
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
                                                                <option value="NO" {{(old('locWithOngoingCovid6') == "NO") ? 'selected' : ''}}>No</option>
                                                                <option value="YES" {{(old('locWithOngoingCovid6') == "YES") ? 'selected' : ''}}>Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited7" value="Others" {{(is_array(old('placevisited')) && in_array("Others", old('placevisited'))) ? 'checked' : ''}}>
                                                      Others
                                                    </label>
                                                </div>
                                                <div id="divLocal7" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                              <label for="locName7">Name of Place</label>
                                                              <input class="form-control" type="text" name="locName7" id="locName7" value="{{old('locName7')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="locAddress7">Location</label>
                                                                <input class="form-control" type="text" name="locAddress7" id="locAddress7" value="{{old('locAddress7')}}" style="text-transform: uppercase;">
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
                                                                                <input class="form-control" type="date" name="locDateFrom7" id="locDateFrom7" value="{{old('locDateFrom7')}}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group">
                                                                                <label for="locDateTo7">To</label>
                                                                                <input class="form-control" type="date" name="locDateTo7" id="locDateTo7" value="{{old('locDateTo7')}}">
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
                                                                <option value="NO" {{(old('locWithOngoingCovid7') == "NO") ? 'selected' : ''}}>No</option>
                                                                <option value="YES" {{(old('locWithOngoingCovid7') == "YES") ? 'selected' : ''}}>Yes</option>
                                                              </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-check">
                                                    <label class="form-check-label">
                                                      <input type="checkbox" class="form-check-input" name="placevisited[]" id="placevisited8" value="Transport Service" {{(is_array(old('placevisited')) && in_array("Transport Service", old('placevisited'))) ? 'checked' : ''}}>
                                                      Transport Service
                                                    </label>
                                                </div>
                                                <div id="divLocal8" class="mt-3">
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                              <label for="localVessel1">1. Airline/Sea vessel/Bus line/Train</label>
                                                              <input type="text" class="form-control" name="localVessel1" id="localVessel1" value="{{old('localVessel1')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localVesselNo1">Flight/Vessel/Bus No.</label>
                                                                <input type="text" class="form-control" name="localVesselNo1" id="localVesselNo1" value="{{old('localVesselNo1')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localOrigin1">Place of Origin</label>
                                                                <input type="text" class="form-control" name="localOrigin1" id="localOrigin1" value="{{old('localOrigin1')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDateDepart1">Departure Date</label>
                                                                <input type="date" class="form-control" name="localDateDepart1" id="localDateDepart1" value="{{old('localDateDepart1')}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDest1">Destination</label>
                                                                <input type="text" class="form-control" name="localDest1" id="localDest1" value="{{old('localDest1')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDateArrive1">Date of Arrival</label>
                                                                <input type="text" class="form-control" name="localDateArrive1" id="localDateArrive1" value="{{old('localDateArrive1')}}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <hr>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                              <label for="localVessel2">2. Airline/Sea vessel/Bus line/Train</label>
                                                              <input type="text" class="form-control" name="localVessel2" id="localVessel2" value="{{old('localVessel2')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localVesselNo2">Flight/Vessel/Bus No.</label>
                                                                <input type="text" class="form-control" name="localVesselNo2" id="localVesselNo2" value="{{old('localVesselNo2')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localOrigin2">Place of Origin</label>
                                                                <input type="text" class="form-control" name="localOrigin2" id="localOrigin2" value="{{old('localOrigin2')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDateDepart2">Departure Date</label>
                                                                <input type="date" class="form-control" name="localDateDepart2" id="localDateDepart2" value="{{old('localDateDepart2')}}">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDest2">Destination</label>
                                                                <input type="text" class="form-control" name="localDest2" id="localDest2" value="{{old('localDest2')}}" style="text-transform: uppercase;">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="localDateArrive2">Date of Arrival</label>
                                                                <input type="date" class="form-control" name="localDateArrive2" id="localDateArrive2" value="{{old('localDateArrive2')}}">
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
                                                              <input type="text" class="form-control" name="contact1Name" id="contact1Name" value="{{old('contact1Name')}}" style="text-transform: uppercase;">
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="contact2Name" id="contact2Name" value="{{old('contact2Name')}}" style="text-transform: uppercase;">
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="contact3Name" id="contact3Name" value="{{old('contact3Name')}}" style="text-transform: uppercase;">
                                                            </div>
                                                            <div class="form-group">
                                                                <input type="text" class="form-control" name="contact4Name" id="contact4Name" value="{{old('contact4Name')}}" style="text-transform: uppercase;">
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
                                                                <input type="text" class="form-control" name="contact4No" id="contact4No" value="{{old('contact3No')}}">
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
                    <button type="submit" class="btn btn-primary" id="formsubmit"><i class="fas fa-save mr-2"></i>Save</button>
                </div>
            </div>
        </form>
        <div class="modal fade bd-example-modal-lg" id="appendix" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Appendix</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                    <div class="modal-body">
                        <div id="accordianId" role="tablist" aria-multiselectable="true">
                            <div class="card">
                                <div class="card-header" role="tab" id="section1HeaderId">
                                    <h6 class="mb-0">
                                        <a data-toggle="collapse" data-parent="#accordianId" href="#section1ContentId" aria-expanded="true" aria-controls="section1ContentId">
                                            Appendix 1. COVID-19 Case Definitions
                                        </a>
                                    </h6>
                                </div>
                                <div id="section1ContentId" class="collapse in" role="tabpanel" aria-labelledby="section1HeaderId">
                                    <div class="card-body">
                                        <div class="card mb-3">
                                            <div class="card-header font-weight-bold">SUSPECT</div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li>A.) A person who meets the <b>clinical AND epidemiological criteria</b></li>
                                                    <li><b>- Clinical criteria:</b></li>
                                                    <ul>
                                                        <li>1.) Acute onset of fever AND cough <b>OR</b></li>
                                                        <li>2.) Acute onset of <b>ANY THREE OR MORE</b> of the following signs of symptoms; fever, cough, general weakness/fatigue, headache, myalgia, sore throat, coryza, dyspnea, anorexia / nausea / vomiting, diarrhea, altered mental status. <b>AND</b></li>
                                                    </ul>
                                                    <li><b>- Epidemiological criteria</b></li>
                                                    <ul>
                                                        <li>1.) Residing/working in an area with high risk of transmission of the virus
                                                            (e.g closed residential settings and humanitarian settings, such as
                                                            camp and camp-like setting for displaced persons), any time w/in the
                                                            14 days prior to symptoms onset <b>OR</b></li>
                                                        <li>Residing in or travel to an area with community transmission anytime
                                                            w/in the 14 days prior to symptoms onset; <b>OR</b></li>
                                                        <li>Working in health setting, including w/in the health facilities and w/in
                                                            households, anytime w/in the 14 days prior to symptom onset; OR</li>
                                                    </ul>
                                                    <li>B.) A patient with <b>severe acute respiratory illness</b> (SARI: acute respiratory
                                                        infection with history of fever or measured fever of ≥ 38°C; cough with
                                                        onset w/in the last 10 days; and who requires hospitalization)</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card mb-3">
                                            <div class="card-header font-weight-bold">PROBABLE</div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li>A.) A <b>patient</b> who meets the <b>clinical criteria</b> (on the top) <b>AND is contact of a probable or
                                                        confirmed case</b>, or <b>epidemiologically linked to a cluster of cases</b> which had had at least one
                                                        confirmed identified within that cluster</li>
                                                    <li>B.) A <b>suspect case</b> (on the top) with <b>chest imaging showing findings suggestive of COVID-19
                                                        disease.</b> Typical chest imaging findings include (Manna, 2020):</li>
                                                    <ul>
                                                        <li>Chest radiography: hazy opacities, often rounded in morphology, with peripheral and lower
                                                            lung distribution</li>
                                                        <li>Chest CT: multiple bilateral ground glass opacities, often rounded in morphology, with
                                                            peripheral and lower lung distribution</li>
                                                        <li>Lung ultrasound: thickened pleural lines, B lines (multifocal, discrete, or confluent),
                                                            consolidative patterns with or without air bronchograms</li>
                                                    </ul>
                                                    <li>C.) A person with <b>recent onset of anosmia (loss of smell), ageusia (loss of taste) in the absence of any other identified cause</b></li>
                                                    <li>D.) Death, not otherwise explained, in an <b>adult with respiratory distress preceding death AND
                                                        who was a contact of a probable or confirmed case or epidemiologically linked to a cluster</b>
                                                        which has had at least one confirmed case identified with that cluster</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header font-weight-bold">CONFIRMED</div>
                                            <div class="card-body">
                                                <p>A person with <b>laboratory confirmation of COVID-19 infection</b>, irrespective of clinical signs and symptoms.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" role="tab" id="section2HeaderId">
                                    <h6 class="mb-0">
                                        <a data-toggle="collapse" data-parent="#accordianId" href="#section2ContentId" aria-expanded="true" aria-controls="section2ContentId">
                                            Appendix 2. Testing Category / Subgroup
                                        </a>
                                    </h6>
                                </div>
                                <div id="section2ContentId" class="collapse in" role="tabpanel" aria-labelledby="section2HeaderId">
                                    <div class="card-body">
                                        <ul class="list-unstyled">
                                            <li><b>A.</b> Individuals with severe/critical symptoms and relevant history of travel/contact</li>
                                            <li><b>B.</b> Individuals with <b>mild</b> symptoms, <b>relevant history</b> of travel/contact, and considered
                                                <b>vulnerable</b>; vulnerable populations include those elderly and with preexisting
                                                medical conditions that predispose them to severe presentation and complications
                                                of COVID-19
                                            </li>
                                            <li><b>C.</b> Individuals with <b>mild</b> symptoms, and <b>relevant history</b> of travel and/or contact</li>
                                            <li><b>D.</b> Individuals with <b>no symptoms</b> but with <b>relevant history</b> of travel and/or contact or
                                                high risk of exposure. These include:</li>
                                            <ul>
                                                <li>D1 - <b>Contact-traced individuals</b></li>
                                                <li>D2 - <b>Healthcare workers</b>, who shall be prioritized for regular testing in order to ensure
                                                    the stability of our healthcare system</li>
                                                <li>D3 - <b>Returning Overseas Filipino</b> (ROF) workers, who shall immediately be tested at
                                                    port of entry</li>
                                                <li>D4 - Filipino citizens in a specific locality within the Philippines who have expressed
                                                    intention to return to their place of residence/home origin (<b>Locally Stranded
                                                        Individuals</b>) may be tested subject to the existing protocols of the IATF
                                                    </li>
                                            </ul>
                                            <li><b>E.</b> <b>Frontliners indirectly involved in health care provision</b> in the response against
                                                COVID-19 may be tested as follows:</li>
                                            <ul>
                                                <li>E1 - Those with <b>high or direct exposure to COVID-19 regardless of location</b> may be
                                                    tested up to once a week. These include: <b>(1)</b> Personnel manning the Temporary
                                                    Treatment and Quarantine Facilities (LGU and Nationally-managed); <b>(2)</b> Personnel
                                                    serving at the COVID-19 swabbing center; <b>(3)</b> Contact tracing personnel; and <b>(4)</b>
                                                    Any personnel conducting swabbing for COVID-19 testing.</li>
                                                <li>E2 - Those who <b>do not have high or direct exposure to COVID-19</b> but who <b>live or work
                                                    in Special Concern Areas</b> may be tested up to every two to four weeks. These
                                                    include the following: <b>(1)</b> Personnel manning Quarantine Control Points, including
                                                    those from Armed Forces of the Philippines, Bureau of Fire Protection; <b>(2)</b> National
                                                    / Regional / Local Risk Reduction and Management Teams; <b>(3)</b> Officials from any
                                                    local government / city / municipality health office (CEDSU, CESU, etc.); <b>(4)</b>
                                                    Barangay Health Emergency Response Teams and barangay officials providing
                                                    barangay border control and performing COVID-19-related tasks; <b>(5)</b> Personnel of
                                                    Bureau of Corrections and Bureau of Jail Penology & Management; <b>(6)</b> Personnel
                                                    manning the One-Stop-Shop in the Management of ROFs; <b>(7)</b> Border control or
                                                    patrol officers, such as immigration officers and the Philippine Coast Guard; and <b>(8)</b>
                                                    Social workers providing amelioration and relief assistance to communities and
                                                    performing COVID-19-related tasks.</li>
                                            </ul>
                                            <li><b>F.</b> Other <b>vulnerable patients</b> and those <b>living in confined spaces</b>. These include but
                                                are not limited to: <b>(1)</b> Pregnant patients who shall be tested during the peripartum
                                                period; <b>(2)</b> Dialysis patients; <b>(3)</b> Patients who are immunocompromised, such as
                                                those who have HIV/AIDS, inherited diseases that affect the immune system; <b>(4)</b>
                                                Patients undergoing chemotherapy or radiotherapy; <b>(5)</b> Patients who will undergo
                                                elective surgical procedures with high risk for transmission; <b>(6)</b> Any person who
                                                have had organ transplants, or have had bone marrow or stem cell transplant in
                                                the past 6 months; <b>(7)</b> Any person who is about to be admitted in enclosed
                                                institutions such as jails, penitentiaries, and mental institutions.</li>
                                            <li><b>G.</b> Residents, occupants or workers in a <b>localized area with an active COVID-19
                                                cluster</b>, as identified and declared by the local chief executive in accordance with
                                                existing DOH Guidelines and consistent with the National Task Force Memorandum
                                                Circular No. 02 s.2020 or the Operational Guidelines on the Application of the
                                                Zoning Containment Strategy in the Localization of the National Action Plan Against
                                                COVID-19 Response. The local chief executive shall conduct the necessary testing in
                                                order to protect the broader community and critical economic activities and to
                                                avoid a declaration of a wider community quarantine.</li>
                                            <li><b>H.</b> Frontliners in <b>Tourist Zones</b>: </li>
                                            <ul>
                                                <li>H1 - All workers and employees in the <b>hospitality and tourism sectors</b> in El Nido,
                                                    Boracay, Coron, Panglao, Siargao and other tourist zones, as identified and declared
                                                    by the Department of Tourism. These workers and employees may be tested once
                                                    every four (4) weeks.</li>
                                                <li>H2 - All <b>travelers</b>, whether of domestic or foreign origin, may be tested at least once, at
                                                    their own expense, prior to entry into any designated tourist zone, as identified and
                                                    declared by the Department of Tourism.</li>
                                            </ul>
                                            <li><b>I.</b> All workers and employees of <b>manufacturing companies and public service
                                                providers registered in economic zones</b> located in Special Concern Areas may be
                                                tested regularly.</li>
                                            <li><b>J. Economy Workers</b></li>
                                            <ul>
                                                <li>J1 - <b>Frontline and Economic Priority Workers</b>, defined as those 1) who work in high
                                                    priority sectors, both public and private, 2) have high interaction with and exposure
                                                    to the public, and 3) who live or work in Special Concerns Areas, may be tested
                                                    every three (3) months. These include but not limited to:</li>
                                                <ul>
                                                    <li><b>Transport and Logistics</b>: drivers of taxis, ride hailing services, buses, public
                                                        transport vehicle, conductors, pilots, flight attendants, flight engineers, rail
                                                        operators, mechanics, servicemen, delivery staff, water transport workers (ferries,
                                                        inter-island shipping, ports)</li>
                                                    <li><b>Food Retails</b>: waiters, waitress, bar attendants, baristas, chefs, cooks, restaurant
                                                        managers, supervisors</li>
                                                    <li><b>Education</b>: teachers at all levels of education and other school frontliners such as
                                                        guidance counselors, librarians, cashiers</li>
                                                    <li><b>Financial Services</b>: bank tellers</li>
                                                    <li><b>Non-Food Retails</b>: cashiers, stock clerks, retail salespersons</li>
                                                    <li><b>Services</b>: hairdressers, barbers, manicurists, pedicurists, massage therapists,
                                                        embalmers, morticians, undertakers, funeral directors, parking lot attendants,
                                                        security guards, messengers</li>
                                                    <li><b>Construction</b>: construction workers including carpenters, stonemasons,
                                                        electricians, painters, foremen, supervisors, civil engineers, structural engineers,
                                                        construction managers, crane/tower operators, elevator installers, repairmen</li>
                                                    <li><b>Water Supply, Sewerage, Waster Management</b>: plumbers, recycling/ reclamation
                                                        workers, garbage collectors, water/wastewater engineers, janitors, cleaners</li>
                                                    <li><b>Public Sector</b>: judges, courtroom clerks, staff and security, all national and local
                                                        government employees rendering frontline services in special concern areas</li>
                                                    <li><b>Mass Media</b>: field reporters, photographers, cameramen</li>
                                                </ul>
                                                <li>J2 - All employees <b>not covered above are not required to undergo testing but are
                                                    encouraged to be tested every quarter.</b> Private sector employers are highly
                                                    encouraged to send their employees for regular testing at the employers’ expense
                                                    in order to avoid lockdowns that may do more damage to their companies.</li>
                                            </ul>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" role="tab" id="section3HeaderId">
                                    <h6 class="mb-0">
                                        <a data-toggle="collapse" data-parent="#accordianId" href="#section3ContentId" aria-expanded="true" aria-controls="section3ContentId">
                                            Appendix 3. Severity of the Disease
                                        </a>
                                    </h6>
                                </div>
                                <div id="section3ContentId" class="collapse in" role="tabpanel" aria-labelledby="section3HeaderId">
                                    <div class="card-body">
                                        <div class="card mb-3">
                                            <div class="card-header font-weight-bold">MILD</div>
                                            <div class="card-body">
                                                <p>Symptomatic patients presenting with fever, cough, fatigue, anorexia,
                                                    myalgias; other non-specific symptoms such as sore throat, nasal
                                                    congestion, headache, diarrhea, nausea and vomiting; loss of smell
                                                    (anosmia) or loss of taste (ageusia) preceding the onset of respiratory
                                                    symptoms with <b>NO signs of pneumonia or hypoxia</b></p>
                                            </div>
                                        </div>
                                        <div class="card mb-3">
                                            <div class="card-header font-weight-bold">MODERATE</div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li>
                                                        Adolescent or adult with <b>clinical signs of non-severe pneumonia</b> (e.g.
                                                        fever, cough, dyspnea, respiratory rate <b>(RR) = 21-30 breaths/minute</b>,
                                                        peripheral capillary oxygen saturation (SpO2) >92% on room air).
                                                    </li>
                                                    <li>
                                                        Child with clinical signs of non-severe pneumonia (cough or difficulty of
                                                        breathing and fast breathing [ < 2 months: > 60; 2-11 months: > 50; 1-5
                                                        years: > 40] and/or chest indrawing)
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card mb-3">
                                            <div class="card-header font-weight-bold">SEVERE</div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li>Adolescent or adult with <b>clinical signs of severe pneumonia or severe
                                                        acute respiratory infection</b> as follows: fever, cough, dyspnea, <b>RR>30
                                                        breaths/minute</b>, severe respiratory distress or SpO2 < 92% on room air</li>
                                                    <li>Child with clinical signs of pneumonia (cough or difficulty in breathing)
                                                        plus at least one of the following:</li>
                                                    <ul>
                                                        <li>a. Central cyanosis or SpO2 < 90%; severe <b>respiratory distress</b> (e.g. fast
                                                            breathing, grunting, very severe chest indrawing); general danger sign:
                                                            <b>inability to breastfeed or drink, lethargy or unconsciousness</b>, or
                                                            convulsions.</li>
                                                        <li><b>Fast breathing (in breaths/min): < 2 months: > 60; 2-11 months: > 50;
                                                            1-5 years: > 40.</b></li>
                                                    </ul>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header font-weight-bold">CRITICAL</div>
                                            <div class="card-body">
                                                <ul class="list-unstyled">
                                                    <li>Patients manifesting with acute respiratory distress syndrome, sepsis and/or septic shock:</li>
                                                    <li>1. <b>Acute Respiratory Distress Syndrome (ARDS)</b></li>
                                                    <ul>
                                                        <li>a. Patients with onset within 1 week of known clinical insult (pneumonia) or new or worsening
                                                            respiratory symptoms, progressing infiltrates on chest X-ray or chest CT scan, with respiratory
                                                            failure not fully explained by cardiac failure or fluid overload.</li>
                                                    </ul>
                                                    <li>2. <b>Sepsis</b></li>
                                                    <ul>
                                                        <li>a. Adults with life-threatening organ dysfunction caused by a dysregulated host response to
                                                            suspected or proven infection. Signs of organ dysfunction include altered mental status, difficult
                                                            or fast breathing, low oxygen saturation, reduced urine output, fast heart rate, weak pulse, cold
                                                            extremities or low blood pressure, skin mottling, or laboratory evidence of coagulopathy,
                                                            thrombocytopenia, acidosis, high lactate or hyperbilirubinemia.</li>
                                                        <li>b. Children with suspected or proven infection and > 2 age-based systemic inflammatory response
                                                            syndrome criteria (abnormal temperature [> 38.5 °C or < 36 °C); tachycardia for age or
                                                            bradycardia for age if < 1year; tachypnea for age or need for mechanical ventilation; abnormal
                                                            white blood cell count for age or > 10% bands), of which one must be abnormal temperature or
                                                            white blood cell count.</li>
                                                    </ul>
                                                    <li>3. <b>Septic Shock</b></li>
                                                    <ul>
                                                        <li>a. Adults with persistent hypotension despite volume resuscitation, requiring vasopressors to
                                                            maintain MAP > 65 mmHg and serum lactate level >2mmol/L</li>
                                                        <li>b. Children with any hypotension (SBP < Sth centile or > 2 SD below normal for age) or two or three
                                                            of the following: altered mental status; bradycardia or tachycardia (HR < 90 bpm or > 160 bpm in
                                                            infants and heart rate < 70 bpm or > 150 bpm in children); prolonged capillary refill (> 2 sec) or
                                                            weak pulse; fast breathing; mottled or cool skin or petechial or purpuric rash; high lactate;
                                                            reduced urine output; hyperthermia or hypothermia.</li>
                                                    </ul>
                                                </ul>
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

    <script>
        $(document).ready(function () {

            //$('#records_id').selectize();
            @if(is_null(auth()->user()->brgy_id) && is_null(auth()->user()->company_id))
            $('#interviewerName').selectize();
            @endif

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

            $('#testDateCollected2').keydown(function (e) {
                if(!Date.parse($(this).val())) {
                    $('#testType2').prop('required', false);
                    $('#testResult2').prop('required', false);
                }
                else {
                    $('#testType2').prop('required', true);
                    $('#testResult2').prop('required', true);
                }
            }).trigger('keydown');

            $('#howManyDoseVaccine').change(function (e) { 
                e.preventDefault();
                if($(this).val() == '') {
                    $('#vaccineName').prop('required', false);

                    $('#ifVaccinated').hide();
                    $('#ifFirstDoseVaccine').hide();
                    $('#ifSecondDoseVaccine').hide();

                    $('#vaccinationDate1').prop('required', false);
                    $('#haveAdverseEvents1').prop('required', false);
                    $('#vaccinationDate2').prop('required', false);
                    $('#haveAdverseEvents2').prop('required', false);
                }
                else if($(this).val() == '1') {
                    $('#vaccineName').prop('required', true);

                    $('#ifVaccinated').show();
                    $('#ifFirstDoseVaccine').show();
                    $('#ifSecondDoseVaccine').hide();

                    $('#vaccinationDate1').prop('required', true);
                    $('#haveAdverseEvents1').prop('required', true);
                    $('#vaccinationDate2').prop('required', false);
                    $('#haveAdverseEvents2').prop('required', false);
                }
                else if($(this).val() == '2') {
                    $('#vaccineName').prop('required', true);

                    $('#ifVaccinated').show();
                    $('#ifFirstDoseVaccine').show();
                    $('#ifSecondDoseVaccine').show();

                    $('#vaccinationDate1').prop('required', true);
                    $('#haveAdverseEvents1').prop('required', true);
                    $('#vaccinationDate2').prop('required', true);
                    $('#haveAdverseEvents2').prop('required', true);
                }
            }).trigger('change');

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
                    $('#OFWPassportNo').prop('required', false);
                }
                else {
                    $('#divisOFW').show();
                    $('#OFWPassportNo').prop('required', true);
                    $('#oaddressscountry').val('N/A');
                    $('#OFWCountyOfOrigin').prop('required', true);
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
                    $('#FNTPassportNo').prop('required', false);
                }
                else {
                    $('#divisFNT').show();
                    $('#FNTCountryOfOrigin').prop('required', true);
                    $('#FNTPassportNo').prop('required', true);
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
                if($(this).val() == 'OTHERS' || $(this).val() == 'ANTIGEN') {
                    $('#divTypeOthers1').show();
                    $('#testTypeOtherRemarks1').prop('required', true);
                    if($(this).val() == 'ANTIGEN') {
                        $('#ifAntigen1').show();
                        $('#antigenKit1').prop('required', true);
                    }
                    else {
                        $('#ifAntigen1').hide();
                        $('#antigenKit1').prop('required', false);
                    }
                }
                else {
                    $('#divTypeOthers1').hide();
                    $('#testTypeOtherRemarks1').empty();
                    $('#testTypeOtherRemarks1').prop('required', false);

                    $('#ifAntigen1').hide();
                    $('#antigenKit1').prop('required', false);
                }
            }).trigger('change');

            $('#testResult1').change(function (e) { 
                e.preventDefault();
                if($(this).val() == "OTHERS") {
                    $('#divResultOthers1').show();
                    $('#testResultOtherRemarks1').prop('required', true);
                    $('#testDateReleased1').prop('required', true);
                }
                else {
                    $('#divResultOthers1').hide();
                    $('#testResultOtherRemarks1').empty();
                    $('#testResultOtherRemarks1').prop('required', false);

                    if($(this).val() == "POSITIVE" || $(this).val() == "NEGATIVE" || $(this).val() == "EQUIVOCAL") {
                        $('#testDateReleased1').prop('required', true);
                    }
                    else {
                        $('#testDateReleased1').prop('required', false);
                    }
                }
            }).trigger('change');

            $('#testType2').change(function (e) { 
                e.preventDefault();
                if($(this).val() == 'OTHERS' || $(this).val() == 'ANTIGEN') {
                    $('#divTypeOthers2').show();
                    $('#testTypeOtherRemarks2').prop('required', true);
                    $('#testDateCollected2').prop('required', true);

                    if($(this).val() == 'ANTIGEN') {
                        $('#ifAntigen2').show();
                        $('#antigenKit2').prop('required', true);
                    }
                    else {
                        $('#ifAntigen2').hide();
                        $('#antigenKit2').prop('required', false);
                    }
                }
                else {
                    $('#divTypeOthers2').hide();
                    $('#testTypeOtherRemarks2').empty();
                    $('#testTypeOtherRemarks2').prop('required', false);

                    if($(this).val() == "") {
                        $('#testDateCollected2').prop('required', false);
                        $('#testType2').prop('required', false);
                        $('#testResult2').prop('required', false);
                    }
                    else {
                        $('#testDateCollected2').prop('required', true);
                        $('#testType2').prop('required', true);
                        $('#testResult2').prop('required', true);
                    }

                    $('#ifAntigen2').hide();
                    $('#antigenKit2').prop('required', false);
                }
            }).trigger('change');

            $('#testResult2').change(function (e) { 
                e.preventDefault();
                if($(this).val() == "OTHERS") {
                    $('#divResultOthers2').show();
                    $('#testResultOtherRemarks2').prop('required', true);
                    $('#testDateReleased2').prop('required', true);
                }
                else {
                    $('#divResultOthers2').hide();
                    $('#testResultOtherRemarks2').empty();
                    $('#testResultOtherRemarks2').prop('required', false);

                    if($(this).val() == "POSITIVE" || $(this).val() == "NEGATIVE" || $(this).val() == "EQUIVOCAL") {
                        $('#testDateReleased2').prop('required', true);
                    }
                    else {
                        $('#testDateReleased2').prop('required', false);
                    }
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
                    $('#deathAnteCause').prop('required', false);
                    $('#deathUndeCause').prop('required', false);
                    $('#contriCondi').prop('required', false);
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
                    $('#expoDateLastCont').val(null);
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
    </script>
@endsection