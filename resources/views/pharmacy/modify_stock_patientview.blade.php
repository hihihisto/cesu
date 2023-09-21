@extends('layouts.app')

@section('content')

@if($prescription)
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <form action="{{route('pharmacy_patient_addcart', $d->id)}}" method="POST" id="myForm">
                    @csrf
                    <div class="card">
                        <div class="card-header"><b>Issuance of Meds to Patient</b> (Branch: {{auth()->user()->pharmacybranch->name}})</div>
                        <div class="card-body">
                            @if(session('msg'))
                            <div class="alert alert-{{session('msgtype')}} text-center" role="alert">
                                {{session('msg')}}
                            </div>
                            @endif
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <td class="bg-light">Name of Patient</td>
                                        <td class="text-center"><b><a href="{{route('pharmacy_view_patient', $d->id)}}">{{$d->getName()}} <small>(#{{$d->id}})</small></a></b></td>
                                        <td class="bg-light">Age / Sex</td>
                                        <td class="text-center">{{$d->getAge()}} / {{$d->sg()}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light">Birthdate</td>
                                        <td class="text-center">{{date('m/d/Y', strtotime($d->bdate))}}</td>
                                        <td class="bg-light">Barangay</td>
                                        <td class="text-center">{{$d->address_brgy_text}}</td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light">Prescription ID</td>
                                        <td class="text-center" colspan="3">#{{$prescription->id}} <button type="button" class="btn btn-success ml-2" id="new_prescription_btn">New Prescription</button></td>
                                    </tr>
                                    <tr>
                                        <td class="bg-light">Requesting Meds for</td>
                                        <td class="text-center" colspan="3">{{$prescription->concerns_list}}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <hr>
                            <div class="form-group">
                                <label for="">Scan QR of Meds to Issue</label>
                                <input type="text" class="form-control" name="meds" id="meds" autocomplete="off" autofocus>
                            </div>
                            <div class="form-group">
                              <label for="alt_meds_id">OR Manually Select from Inventory List</label>
                              <select class="form-control" name="alt_meds_id" id="alt_meds_id">
                                <option value="" disabled {{(is_null(old('alt_meds_id'))) ? 'selected' : ''}}>Choose...</option>
                                @foreach($meds_list as $m)
                                <option value="{{$m->pharmacysupplymaster->sku_code}}" {{(!($m->ifHasStock())) ? 'disabled' : ''}}>{{$m->pharmacysupplymaster->name}} - {{$m->displayQty()}} {{(!($m->ifHasStock())) ? '- NO STOCK' : ''}}</option>
                                @endforeach
                              </select>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                      <label for="type_to_process">Type to Process</label>
                                      <select class="form-control" name="type_to_process" id="type_to_process" required>
                                        <option value="PIECE">Piece</option>
                                        <!--<option value="BOX">Box</option>-->
                                      </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="qty_to_process">Quantity <span id="qty_span"></span></label>
                                        <input type="text" class="form-control" name="qty_to_process" id="qty_to_process" min="1" max="999" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-check">
                              <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="enable_override" id="enable_override" value="checkedValue"> Enable Override <i>(Ignore Quantity and Duration Limit)</i>
                              </label>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block" name="submit" value="add_cart">Add</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="col-md-6">
                <form action="{{route('pharmacy_patient_process_cart', $d->id)}}" method="POST">
                    @csrf
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex justify-content-between">
                                <div><b>Cart</b> ({{$load_subcart->count()}})</div>
                                <div><button type="submit" class="btn btn-outline-secondary" name="submit" value="clear" {{($load_subcart->count() == 0) ? 'disabled' : ''}}>Reset/Clear</button></div>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($load_subcart->count())
                            <table class="table table-bordered table-striped text-center">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>ITEM</th>
                                        <th>QTY TO ISSUE</th>
                                        <th>MAX QTY LIMIT (BASED ON RX)</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($load_subcart as $ind => $c)
                                    <tr>
                                        <td style="vertical-align: middle;">{{$ind+1}}</td>
                                        <td style="vertical-align: middle;"><b>{{$c->pharmacysub->pharmacysupplymaster->name}}</b></td>
                                        <td style="vertical-align: middle;">{{$c->qty_to_process}} {{Str::plural($c->type_to_process, $c->qty_to_process)}}</td>
                                        <td style="vertical-align: middle;">
                                            @if($c->displayPrescriptionLimit())
                                            {{$c->displayPrescriptionLimit()}}
                                            @else
                                            <input type="number" class="form-control" name="set_pieces_limit[]" id="set_pieces_limit" min="1" max="900">
                                            @endif
                                            
                                        </td>
                                        <td style="vertical-align: middle;"><button type="submit" name="delete" value="{{$c->id}}" class="btn btn-danger">X</button></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            @else
                            <h6 class="text-center">Cart is still empty.</h6>
                            @endif
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" name="submit" value="process" class="btn btn-success" {{($load_subcart->count() == 0) ? 'disabled' : ''}}>Process</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <form action="{{route('pharmacy_patient_addcart', $d->id)}}" method="POST" class="d-none">
        @csrf
        <button type="submit" class="btn btn-primary" id="new_prescription_hdn" name="submit" value="new_prescription"></button>
    </form>

    <script>
        $('#alt_meds_id').select2({
            theme: 'bootstrap',
        });
        

        $(document).ready(function () {
            $("#myForm").submit(function (event) {
                var medsValue = $("#meds").val();
                var altMedsValue = $("#alt_meds_id").val();

                // Check if either field is empty
                if (medsValue === "" && altMedsValue === null) {
                    // Prevent the form from submitting
                    event.preventDefault();
                    alert("Please scan or manually input the item to issue before proceeding.");
                }
            });

            $('#new_prescription_btn').click(function (e) { 
                e.preventDefault();
                
                var result = confirm("Current Prescription will be marked as Finished. Continue?");

                if (result) {
                    $('#new_prescription_hdn').click();
                } else {

                }
            });
        });

        $('#type_to_process').change(function (e) { 
            e.preventDefault();
            if($(this).val() == 'PIECE') {
                $('#qty_span').text('(in Pieces)');
            }
            else {
                $('#qty_span').text('(in Boxes)');
            }
        }).trigger('change');
    </script>
@else
<div class="container">
    <form action="{{route('pharmacy_patient_addcart', $d->id)}}" method="POST" id="myForm">
        @csrf
        <div class="card">
            <div class="card-header"><b>Initialize Patient Record</b></div>
            <div class="card-body">
                <div class="alert alert-info text-center" role="alert">
                    @if(!is_null($d->itr_id))
                    <b>Patient was encoded from OPD.</b> Please fill-out the fields below before the patient can request medicines.
                    @else
                    Please fill-out the criteria below before the patient can request medicines.
                    @endif
                </div>
                <table class="table table-bordered">
                    <tr>
                        <td class="bg-light">Name of Patient / ID</td>
                        <td class="text-center"><b><a href="{{route('pharmacy_view_patient', $d->id)}}">{{$d->getName()}} <small>(#{{$d->id}})</small></a></b></td>
                        <td class="bg-light">Age / Sex</td>
                        <td class="text-center">{{$d->getAge()}} / {{$d->sg()}}</td>
                    </tr>
                    <tr>
                        <td class="bg-light">Birthdate</td>
                        <td class="text-center">{{date('m/d/Y', strtotime($d->bdate))}}</td>
                        <td class="bg-light">Barangay</td>
                        <td class="text-center">{{$d->address_brgy_text}}</td>
                    </tr>
                    @if(!is_null($d->itr_id))
                    <tr>
                        <td class="bg-light">Date of Consultation</td>
                        <td class="text-center">{{date('m/d/Y', strtotime($d->getLatestItr()->consultation_date))}}</td>
                        <td class="bg-light">Chief Complain</td>
                        <td class="text-center"><b>{{$d->getLatestItr()->chief_complain}}</b></td>
                    </tr>
                    <tr>
                        <td class="bg-light">Diagnosis</td>
                        <td class="text-center" colspan="3">{{$d->getLatestItr()->dcnote_assessment}}</td>
                    </tr>
                    <tr>
                        <td class="bg-light">RX</td>
                        <td class="text-center" colspan="3">{{$d->getLatestItr()->dcnote_plan}}</td>
                    </tr>
                    @endif
                </table>
                <hr>
                <div class="form-group">
                    <label for="concerns_list"><span class="text-danger font-weight-bold">*</span>Requesting Medicine/s for <i>(Select all that apply)</i></label>
                    <select class="form-control" name="concerns_list[]" id="concerns_list" multiple required>
                      <option value="ACCIDENT/INJURIES/WOUNDS">ACCIDENT/INJURIES/WOUNDS</option>
                      <option value="CHILDREN">CHILDREN</option>
                      <option value="COLDS">COLDS</option>
                      <option value="DIABETES">DIABETES</option>
                      <option value="DERMA/SKIN PROBLEM">DERMA/SKIN PROBLEM</option>
                      <option value="FAMILY PLANNING">FAMILY PLANNING</option>
                      <option value="FEVER/HEADACHE">FEVER/HEADACHE</option>
                      <option value="HYPERTENSION/HEART/HIGH CHOLESTEROL">HYPERTENSION/HEART/HIGH CHOLESTEROL</option>
                      <option value="IMMUNE DEFICIENCY">IMMUNE DEFICIENCY</option>
                      <option value="IMMUNIZATION">IMMUNIZATION</option>
                      <option value="INFECTION">INFECTION</option>
                      <option value="KIDNEY PROBLEM">KIDNEY PROBLEM</option>
                      <option value="LIVER PROBLEM">LIVER PROBLEM</option>
                      <option value="MENTAL HEALTH">MENTAL HEALTH</option>
                      <option value="MICROBIAL INFECTIONS">MICROBIAL INFECTIONS</option>
                      <option value="MILD/SEVERE PAIN">MILD/SEVERE PAIN</option>
                      <option value="MUSCLE PROBLEM">MUSCLE PROBLEM</option>
                      <option value="NERVES PROBLEM">NERVES PROBLEM</option>
                      <option value="RESPIRATORY PROBLEM">RESPIRATORY PROBLEM</option>
                      <option value="TB-DOTS">TB-DOTS</option>
                      <option value="WOMEN">WOMEN</option>
                      <option value="OTHERS">OTHERS</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-success btn-block" name="submit" value="submit_changes">Submit Changes</button>
            </div>
        </div>
    </form>
</div>

<script>
    $('#concerns_list').select2({
        theme: 'bootstrap',
    });
</script>
@endif
@endsection