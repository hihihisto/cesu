@extends('layouts.app')

@section('content')
<style>
    @media print {
        #printDiv, #divFoot {
            display: none;
        }

        @page {
            margin: 0;
        }

        body {
            background-color: white;
            margin-top: 0;
        }
    }

    .content {
        text-align: center;
    }
    .inner {
        display:inline-block;
    }
</style>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div id="printDiv">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <a href="{{route('abtc_print_new', $f->id)}}" type="button" class="btn btn-primary btn-block" id="printnew">Print (New Card)</a>
                            </div>
                            <div class="col-md-6">
                                <button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#philhealthForms">Print PhilHealth Forms</button>
                            </div>
                        </div>
                        
                        <button type="button" class="btn btn-primary btn-block" onclick="window.print()" id="printbtn"><i class="fas fa-print mr-2"></i>Print (Old Form)</button>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <div><a href="{{route('abtc_schedule_index')}}" class="btn btn-link"><i class="fas fa-calendar-alt mr-2"></i>Back to Todays Schedule</a></div>
                            <div><a href="{{route('abtc_encode_edit', ['br_id' => $f->id])}}" class="btn btn-link"><i class="fas fa-backward mr-2"></i>Back to Patient Details</a></div>
                            <div><a href="{{route('abtc_patient_create')}}" class="btn btn-link"><i class="fas fa-user-plus mr-2"></i>Add NEW Patient</a></div>
                        </div>
                        <hr>
                    </div>
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6><b>{{$f->getBranch()}}</b></h6>
                            <h6><b>ANIMAL BITE TREATMENT CENTER</b></h6>
                        </div>
                        <div class="font-weight-bold">
                            <ul>
                                <i class="fas fa-clock mr-2"></i>Schedule: Mon,Tue,Thu,Fri
                                <li class="text-success">New Patients: 8AM - 11AM</li>
                                <li class="text-primary">Follow-up: 1PM</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table text-center table-borderless" style="margin-top: -20px;">
                        <tbody>
                            <tr >
                                <td style="vertical-align: middle;">
                                    <img src="{{asset('assets/images/gentri_icon_large.png')}}" style="width: 8rem;" class="img-fluid mr-3" alt="">
                                    <img src="{{asset('assets/images/cho_icon_large.png')}}" style="width: 8rem;" class="img-fluid" alt="">
                                </td>
                                <td class="content">
                                    <div class="inner">
                                        <div>{!! QrCode::size(120)->generate(route('abtc_qr_process', $f->patient->qr)) !!}</div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-borderless" style="margin-top: -20px;">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold">Registration No.:</td>
                                <td><u>{{$f->case_id}}</u></td>
                                <td class="font-weight-bold">Date Registered:</td>
                                <td><u>{{date('m/d/Y', strtotime($f->case_date))}}</u></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="table table-borderless" style="margin-top: -20px;">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold">Name:</td>
                                <td><u>{{$f->patient->getName()}}</u></td>
                                <td class="font-weight-bold">Age/Gender:</td>
                                <td><u>{{$f->patient->getAge()}} / {{$f->patient->sg()}}</u></td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-borderless" style="margin-top: -20px;">
                        <tbody>
                            <tr>
                                <td class="font-weight-bold">Address:</td>
                                <td><u>{{$f->patient->getAddressMini()}}</u></td>
                            </tr>
                        </tbody>
                    </table>

                    <ul>
                        <b>History of Exposure</b>
                        <li class="ml-5"><b>Date of Exposure:</b> <u>{{date('m/d/Y', strtotime($f->bite_date))}}</u></li>
                        <li class="ml-5"><b>Place of Exposure:</b> <u>{{$f->case_location}}</u></li>
                        <li class="ml-5"><b>Type of Exposure:</b> <u>{{$f->getBiteType()}} {{(!is_null($f->body_site)) ? ' / '.$f->body_site : ''}}</u></li>
                        <li class="ml-5"><b>Source of Exposure:</b> <u>{{$f->getSource()}}</u></li>
                    </ul>

                    <table class="table table-borderless table-sm">
                        <tbody>
                            <tr>
                                <td><b>Category of Exposure:</b> <u>{{($f->d3_done == 0) ? '__________' : $f->category_level}}</u></td>
                                <td><b>Post Exposure Prophylaxis:</b> <u>Y {{($f->is_booster == 1) ? ' - BOOSTER' : ''}}</u></td>
                            </tr>
                            <tr>
                                <td><b>A. Washing of Bite Wound:</b> <u>{{($f->washing_of_bite == 1) ? 'Y' : 'N'}}</u></td>
                                <td><b>B. RIG:</b> <u>{{$f->showRig()}}</u></td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <table class="table table-bordered text-center table-sm">
                        <tbody>
                            <thead class="thead-light text-center">
                                <th colspan="3">Generic Name: <u>{{$f->getGenericName()}}</u> | Brand Name: <u>{{$f->brand_name}}</u></th>
                            </thead>
                            <thead class="thead-light">
                                <th>Day</th>
                                <th>Date</th>
                                <th>Signature</th>
                            </thead>
                            <tr class="font-weight-bold">
                                <td>Day 0</td>
                                <td>{{date('m/d/Y (D)', strtotime($f->d0_date))}}</td>
                                <td class="{{($f->d0_done == 1) ? 'text-success' : ''}}"><b>{{($f->d0_done == 1) ? 'DONE' : ''}}</b></td>
                            </tr>
                            <tr class="font-weight-bold">
                                <td>Day 3</td>
                                <td>{{date('m/d/Y (D)', strtotime($f->d3_date))}}</td>
                                <td class="{{($f->d3_done == 1) ? 'text-success' : ''}}"><b>{{($f->d3_done == 1) ? 'DONE' : ''}}</b></td>
                            </tr>
                            @if($f->is_booster != 1)
                            <tr class="font-weight-bold">
                                <td>Day 7</td>
                                <td>{{date('m/d/Y (D)', strtotime($f->d7_date))}}</td>
                                <td class="{{($f->d7_done == 1) ? 'text-success' : ''}}"><b>{{($f->d7_done == 1) ? 'DONE' : ''}}</b></td>
                            </tr>
                            @if($f->pep_route == 'IM')
                            <tr class="font-weight-bold">
                                <td>Day 14 (M)</td>
                                <td>{{date('m/d/Y (D)', strtotime($f->d14_date))}}</td>
                                <td class="{{($f->d14_done == 1) ? 'text-success' : ''}}"><b>{{($f->d14_done == 1) ? 'DONE' : ''}}</b></td>
                            </tr>
                            @endif
                            <tr class="font-weight-bold">
                                <td>
                                    <div>Day 28</div>
                                    <div><small>(If Animal Died/Lost)</small></div>
                                </td>
                                <td style="vertical-align: middle;">{{date('m/d/Y (D)', strtotime($f->d28_date))}}</td>
                                <td class="{{($f->d28_done == 1) ? 'text-success' : ''}}"><b>{{($f->d28_done == 1) ? 'DONE' : ''}}</b></td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                    @if($f->is_booster != 1)
                    <ul>
                        <small>Note:</small>
                        <li><small>Observe the biting animal for 14 days. If the animal died, report to ABTC as soon as possible.</small></li>
                        <li><small>Day 3, Day 7, and Day 28 <i>(If Animal Died/Lost)</i> is your <b>Follow-up</b> schedule.</small></li>
                    </ul>
                    @endif
                    <p class="mt-3"><b>Status of animal 14 days after exposure:</b> <u>{{$f->biting_animal_status}}</u></p>
                    <hr>
                    <div class="text-center">
                        <h4 style="color: blue"><i>Be a Responsible Pet Owner</i></h4>
                        <h4 style="color: green"><i>Let's Join Forces for a Rabies-free Gentri</i></h4>
                    </div>
                </div>
                <div class="card-footer text-center" id="divFoot">
                    
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <ul class="mt-5">
                <h3><b>MGA PAALALA / MGA DAPAT GAWIN KAPAG NAKAGAT O NAKALMOT NG ASO/PUSA</b></h3>
                <li>Hugasan agad ang sugat gamit ng malinis na tubig at sabon sa loob ng 10-15 minutos.</li>
                <li>Linisin ang sugat gamit ng 70% Alcohol/Ethanol at Povidone-iodine (Betadine), kung mayroon.</li>
                <li>Magpa-konsulta agad sa doktor o malapit na Animal Bite Treatment Center (ABTC).</li>
            </ul>
            <ul>
                <h3 class="text-danger"><b>IWASAN ANG MGA SUMUSUNOD</b></h3>
                <li>Huwag sipsipin ang sugat gamit ang bibig.</li>
                <li>Huwag lagyan ng bawang, barya, o bato sa sugat.</li>
                <li>Huwag magpagamot sa tandok ng kagat/kalmot ng aso/pusa.</li>
                <li>Huwag balutan ang sugat ng damit o bandahe.</li>
                <li>Huwag balewalain ang kagat ng hayop.</li>
            </ul>
            <p>Bigyan ng pansin at seryosohin ang potensyal na exposure sa rabies.</p>
            <p>Kapag lumabas ang sintomas, kamatayan ay halos di na maiiwasan pa.</p>
        </div>
    </div>
</div>

<form action="{{route('abtc_print_philhealth', $f->id)}}" method="POST">
    @csrf
    <div class="modal fade" id="philhealthForms" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Print Philhealth Forms</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-primary" role="alert">
                        <h5>Paki-handa na po sa pasyente ang kopya ng kanyang <b>Philhealth Member Data Record (MDR)</b>, <b>Valid ID</b>, at <b>Philhealth Benefit Eligibility Form</b></h5>
                    </div>
                    @if(!$f->patient->philhealth)
                    <div class="form-group">
                        <label for="philhealth" class="form-label">Philhealth Identification No. (PIN)</label>
                        <input type="text" class="form-control" id="philhealth" name="philhealth" value="{{old('philhealth')}}" pattern="[0-9]{12}" required>
                    </div>

                    <div class="form-group">
                      <label for="status_type">Philhealth Status Type</label>
                      <select class="form-control" name="philhealth_statustype" id="philhealth_statustype" required>
                        <option value="" disalbed {{(is_null(old('status_type'))) ? 'selected' : ''}}>Choose...</option>
                        <option value="MEMBER">Member</option>
                        <option value="DEPENDENT">Dependent</option>
                      </select>
                    </div>
                    <div id="ifDependentDiv" class="d-none">

                    </div>
                    <div class="form-group">
                        <label for="status_type">Philhealth Status Type</label>
                        <select class="form-control" name="philhealth_statustype" id="philhealth_statustype" required>
                          <option value="" disalbed {{(is_null(old('status_type'))) ? 'selected' : ''}}>Choose...</option>
                          <option value="MEMBER">Member</option>
                          <option value="DEPENDENT">Dependent</option>
                        </select>
                    </div>
                    @endif
                    <hr>
                    <button type="submit" class="btn btn-primary btn-block" name="submit" value="card">Card</button>
                    <button type="submit" class="btn btn-primary btn-block" name="submit" value="csf">CSF</button>
                    <button type="submit" class="btn btn-primary btn-block" name="submit" value="cf2">CF2</button>
                    <button type="submit" class="btn btn-primary btn-block" name="submit" value="soa">SOA</button>

                </div>
            </div>
        </div>
    </div>
</form>

<script>
    @if(request()->input('t'))
        $(document).ready(function () {
            $('#printnew').click();
        });
    @endif

    $('#philhealth_statustype').change(function (e) { 
        e.preventDefault();
        if($(this).val() == 'MEMBER') {

        }
        else {

        }
    }).trigger('change');
</script>
@endsection