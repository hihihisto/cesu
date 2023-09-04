@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div><b>List of Items</b></div>
                <div><button type="button" class="btn btn-success" data-toggle="modal" data-target="#addMasterItem">Add Master Item</button></div>
                <!-- <div><button type="button" class="btn btn-success" data-toggle="modal" data-target="#addProduct">Add Product</button></div> -->
            </div>
        </div>
        <div class="card-body">
            @if(session('msg'))
            <div class="alert alert-{{session('msgtype')}} text-center" role="alert">
                {{session('msg')}}
            </div>
            @endif
            <form action="" method="GET">
                <div class="row">
                    <div class="col-md-8"></div>
                    <div class="col-md-4">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" name="q" value="{{request()->input('q')}}" placeholder="Search By Item Name / SKU" style="text-transform: uppercase;" autocomplete="off" required>
                            <div class="input-group-append">
                              <button class="btn btn-secondary" type="submit"><i class="fa fa-search" aria-hidden="true"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <table class="table table-bordered table-striped">
                <thead class="thead-light text-center">
                    <tr>
                        <th>ID</th>
                        <th>Item Name</th>
                        <th>SKU Code</th>
                        <th>Current Stock</th>
                        <th>Date Added / By</th>
                        <th>Date Updated / By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $i)
                    <tr>
                        <td class="text-center">{{$i->id}}</td>
                        <td><a href="{{route('pharmacy_itemlist_viewitem', $i->id)}}">{{$i->pharmacysupplymaster->name}}</a></td>
                        <td class="text-center">{{$i->pharmacysupplymaster->sku_code}}</td>
                        <td class="text-center">{{$i->master_box_stock}}</td>
                        <td class="text-center"><small>{{date('m/d/Y h:i A', strtotime($i->created_at))}}</small></td>
                        <td class="text-center"><small>{{(!is_null($i->updated_by)) ? date('m/d/Y h:i A', strtotime($i->updated_at)).' / '.$i->getUpdatedBy->name : NULL}}</small></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<form action="{{route('pharmacy_add_master_item')}}" method="POST">
    @csrf
    <div class="modal fade" id="addMasterItem" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Master Item</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name"><b class="text-danger">*</b>Item Name</label>
                        <input type="text" class="form-control" name="name" id="name" style="text-transform: uppercase;" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" class="form-control" name="description" id="description">
                    </div>
                    <div class="form-group">
                        <label for="category"><b class="text-danger">*</b>Category</label>
                        <select class="form-control" name="category" id="category" required>
                            <option value="" disabled {{is_null(old('category')) ? 'selected' : ''}}>Select...</option>
                            <option value="ANTIBIOTICS">ANTIBIOTICS</option>
                            <option value="FAMILY PLANNING">FAMILY PLANNING</option>
                            <option value="MAINTENANCE">MAINTENANCE</option>
                            <option value="OINTMENT">OINTMENT</option>
                            <option value="OTHERS">OTHERS</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sku_code"><b class="text-danger">*</b>SKU Code</label>
                        <input type="text" class="form-control" name="sku_code" id="sku_code" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity_type"><b class="text-danger">*</b>Quantity Type</label>
                                <select class="form-control" name="quantity_type" id="quantity_type" required>
                                    <option value="BOX">PER BOX</option>
                                    <option value="BOTTLE">PER BOTTLE</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="config_piecePerBox"><b class="text-danger">*</b>Max pieces inside per Box</label>
                                <input type="number" class="form-control" name="config_piecePerBox" id="config_piecePerBox" min="1">
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div id="accordianId" role="tablist" aria-multiselectable="true">
                        <div class="card mb-3">
                            <div class="card-header btn-dropdown" role="tab" id="section1HeaderId">
                                <a data-toggle="collapse" data-parent="#accordianId" href="#section1ContentId" aria-expanded="true" aria-controls="section1ContentId">Stock Card Details</a>
                            </div>
                            <div id="section1ContentId" class="collapse" role="tabpanel" aria-labelledby="section1HeaderId">
                                <div class="card-body">
                                    <div class="form-group">
                                        <label for="po_contract_number">PO Contract Number</label>
                                        <input type="text" class="form-control" name="po_contract_number" id="po_contract_number" style="text-transform: uppercase;">
                                    </div>
                                    <div class="form-group">
                                        <label for="supplier">Supplier</label>
                                        <input type="text" class="form-control" name="supplier" id="supplier" style="text-transform: uppercase;">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="dosage_form">Dosage Form</label>
                                                <input type="text" class="form-control" name="dosage_form" id="dosage_form" style="text-transform: uppercase;">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="dosage_strength">Dosage Strength</label>
                                                <input type="text" class="form-control" name="dosage_strength" id="dosage_strength" style="text-transform: uppercase;">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="unit_measure">Unit Measure</label>
                                        <input type="text" class="form-control" name="unit_measure" id="unit_measure" style="text-transform: uppercase;">
                                    </div>
                                    <div class="form-group">
                                        <label for="entity_name">Entity Name</label>
                                        <input type="text" class="form-control" name="entity_name" id="entity_name" style="text-transform: uppercase;">
                                    </div>
                                    <div class="form-group">
                                        <label for="source_of_funds">Source of Funds</label>
                                        <input type="text" class="form-control" name="source_of_funds" id="source_of_funds" style="text-transform: uppercase;">
                                    </div>
                                    <div class="form-group">
                                        <label for="unit_cost">Unit Cost</label>
                                        <input type="text" class="form-control" name="unit_cost" id="unit_cost" style="text-transform: uppercase;">
                                    </div>
                                    <div class="form-group">
                                        <label for="mode_of_procurement">Mode of Procurement</label>
                                        <input type="text" class="form-control" name="mode_of_procurement" id="mode_of_procurement" style="text-transform: uppercase;">
                                    </div>
                                    <div class="form-group">
                                        <label for="end_user">End User</label>
                                        <input type="text" class="form-control" name="end_user" id="end_user" style="text-transform: uppercase;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="master_box_stock"><b class="text-danger">*</b>Current Stock (in <span id="inWhatQtyType"></span>)</label>
                                <input type="number" class="form-control" name="master_box_stock" id="master_box_stock" min="0" value="{{old('master_box_stock')}}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="expiration_date"><b class="text-danger">*</b>Expiration Date</label>
                                <input type="date" class="form-control" name="expiration_date" id="expiration_date" min="{{date('Y-m-d')}}" required>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    $('#quantity_type').change(function (e) { 
        e.preventDefault();
        if($(this).val() == 'BOX') {
            $('#config_piecePerBox').prop('disabled', false);
            $('#config_piecePerBox').prop('required', true);
            $("#inWhatQtyType").text("Boxes");
        }
        else {
            $('#config_piecePerBox').prop('disabled', true);
            $('#config_piecePerBox').prop('required', false);
            $("#inWhatQtyType").text("Bottles");
        }
    }).trigger('change');
</script>
@endsection