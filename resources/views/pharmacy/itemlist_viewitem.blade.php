@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div><b>View Item</b></div>
                    <div><!--<a href="{{route('pharmacy_modify_view')}}?code={{$d->sku_code}}" class="btn btn-success">Modify Stock</a>--></div>
                </div>
            </div>
            <div class="card-body">

                <form action="{{route('pharmacy_itemlist_updateitem', $d->id)}}">
                    <div class="card mb-3">
                        <div class="card-header"><b>Item Details</b></div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name"><b class="text-danger">*</b>Item Name</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{old('name', $d->name)}}" required>
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input type="text" class="form-control" name="description" id="description" value="{{old('description', $d->description)}}">
                            </div>
                            <div class="form-group">
                                <label for="category"><b class="text-danger">*</b>Category</label>
                                <select class="form-control" name="category" id="category" required>
                                    <option value="ANTIBIOTICS" {{(old('category', $d->category) == 'ANTIBIOTICS') ? 'selected' : ''}}>ANTIBIOTICS</option>
                                    <option value="FAMILY PLANNING" {{(old('category', $d->category) == 'FAMILY PLANNING') ? 'selected' : ''}}>FAMILY PLANNING</option>
                                    <option value="MAINTENANCE" {{(old('category', $d->category) == 'MAINTENANCE') ? 'selected' : ''}}>MAINTENANCE</option>
                                    <option value="OINTMENT" {{(old('category', $d->category) == 'OINTMENT') ? 'selected' : ''}}>OINTMENT</option>
                                    <option value="OTHERS" {{(old('category', $d->category) == 'OTHERS') ? 'selected' : ''}}>OTHERS</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="sku_code"><b class="text-danger">*</b>SKU Code</label>
                                <input type="text" class="form-control" name="sku_code" id="sku_code" value="{{old('sku_code', $d->sku_code)}}" required>
                            </div>
                            <hr>
                            <div id="accordianId" role="tablist" aria-multiselectable="true">
                                <div class="card">
                                    <div class="card-header" role="tab" id="section1HeaderId">
                                        <a data-toggle="collapse" data-parent="#accordianId" href="#section1ContentId" aria-expanded="true" aria-controls="section1ContentId">Other Details</a>
                                    </div>
                                    <div id="section1ContentId" class="collapse in" role="tabpanel" aria-labelledby="section1HeaderId">
                                        <div class="card-body">
                                            <div class="form-group">
                                                <label for="po_contract_number">PO Contract Number</label>
                                                <input type="text" class="form-control" name="po_contract_number" id="po_contract_number" value="{{old('po_contract_number', $d->po_contract_number)}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="supplier">Supplier</label>
                                                <input type="text" class="form-control" name="supplier" id="supplier" value="{{old('supplier', $d->supplier)}}">
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="dosage_form">Dosage Form</label>
                                                        <input type="text" class="form-control" name="dosage_form" id="dosage_form" value="{{old('dosage_form', $d->dosage_form)}}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="dosage_strength">Dosage Strength</label>
                                                        <input type="text" class="form-control" name="dosage_strength" id="dosage_strength" value="{{old('dosage_strength', $d->dosage_strength)}}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="unit_measure">Unit Measure</label>
                                                <input type="text" class="form-control" name="unit_measure" id="unit_measure" value="{{old('unit_measure', $d->unit_measure)}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="entity_name">Entity Name</label>
                                                <input type="text" class="form-control" name="entity_name" id="entity_name" value="{{old('entity_name', $d->entity_name)}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="source_of_funds">Source of Funds</label>
                                                <input type="text" class="form-control" name="source_of_funds" id="source_of_funds" value="{{old('source_of_funds', $d->source_of_funds)}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="unit_cost">Unit Cost</label>
                                                <input type="text" class="form-control" name="unit_cost" id="unit_cost" value="{{old('unit_cost', $d->unit_cost)}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="mode_of_procurement">Mode of Procurement</label>
                                                <input type="text" class="form-control" name="mode_of_procurement" id="mode_of_procurement" value="{{old('mode_of_procurement', $d->mode_of_procurement)}}">
                                            </div>
                                            <div class="form-group">
                                                <label for="end_user">End User</label>
                                                <input type="text" class="form-control" name="end_user" id="end_user" value="{{old('end_user', $d->end_user)}}">
                                            </div>
                                            <hr>
                                            <div class="form-group">
                                                <label for="config_piecePerBox">Piece per Box</label>
                                                <input type="number" class="form-control" name="config_piecePerBox" id="config_piecePerBox" value="{{old('config_piecePerBox', $d->config_piecePerBox)}}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block" id="submitbtn">Update (CTRL + S)</button>
                        </div>
                    </div>
                </form>

                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <div><b>Batch Details</b></div>
                            <div><a href="{{route('pharmacy_modify_view')}}?code={{$d->sku_code}}" class="btn btn-success">Modify Stock</a></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped text-center">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Expiration Date</th>
                                    <th>Quantity (in Boxes)</th>
                                    <th>Date Added / By</th>
                                    <th>Date Modified / By</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sub_list as $ind => $sl)
                                <tr>
                                    <td>{{$ind+1}}</td>
                                    <td>{{date('Y-m-d', strtotime($sl->expiration_date))}}</td>
                                    <td>{{$sl->current_box_stock}}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between">
                            <div><b>Stock Card / Transactions</b></div>
                            <div><button type="button" class="btn btn-success">Download</button></div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-striped">
                            <thead class="thead-light text-center">
                                <tr>
                                    <th>Date</th>
                                    <th>Received</th>
                                    <th>Issued</th>
                                    <th>Balance</th>
                                    <th>Total Cost</th>
                                    <th>DR/SI/RIS/PTR/BL No.</th>
                                    <th>Recipient/Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($scard as $s)
                                <tr class="text-center">
                                    <td>{{date('m/d/Y h:i A', strtotime($s->created_at))}}</td>
                                    <td>{{($s->type == 'RECEIVED') ? $s->qty_to_process : ''}}</td>
                                    <td>{{($s->type == 'ISSUED') ? $s->qty_to_process : ''}}</td>
                                    <td>{{$s->after_qty}}</td>
                                    <td>{{$s->total_cost}}</td>
                                    <td>{{$s->drsi_number}}</td>
                                    <td>{{$s->recipient}}  {{(!is_null($s->remarks)) ? '/ '.$s->remarks : ''}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $(document).bind('keydown', function(e) {
            if(e.ctrlKey && (e.which == 83)) {
                e.preventDefault();
                $('#submitbtn').trigger('click');
                $('#submitbtn').prop('disabled', true);
                setTimeout(function() {
                    $('#submitbtn').prop('disabled', false);
                }, 2000);
                return false;
            }
        });
    </script>
@endsection