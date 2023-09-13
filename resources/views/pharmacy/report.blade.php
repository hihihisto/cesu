@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><b>Report</b> (Branch: {{auth()->user()->pharmacybranch->name}})</div>
        <div class="card-body">
            @if(auth()->user()->isAdminPharmacy())
            <form action="" method="GET">
                <div class="input-group">
                    <select class="custom-select" id="select_branch" name="select_branch" required>
                        <option value="" disabled selected>Select Branch to Filter Report</option>
                        @foreach($list_branch as $ind => $br)
                            <option value="{{$br->id}}" {{(old('select_branch', request()->input('select_branch')) == $br->id) ? 'selected': ''}}>{{$br->name}}</option>
                        @endforeach
                    </select>
                    <div class="input-group-append">
                      <button class="btn btn-outline-success" type="submit">Search</button>
                    </div>
                </div>
            </form>
            <hr>
            @endif
            <div class="row">
                <div class="col-md-8">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light text-center">
                            <tr>
                                <th colspan="3">Top Fast Moving Meds</th>
                            </tr>
                            <tr>
                                <th>Item Name</th>
                                <th>Current Stock</th>
                                <th>% of Issuance vs. Previous Month</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td scope="row"></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>

                    <table class="table table-striped table-bordered">
                        <thead class="thead-light text-center">
                            <tr>
                                <th colspan="4">Top Barangays Issued</th>
                            </tr>
                            <tr>
                                <th>Top</th>
                                <th>Barangay</th>
                                <th>Issued QTY (Boxes)</th>
                                <th>Issued QTY (Pieces)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td scope="row"></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="thead-light">
                            <tr>
                                <th colspan="3">List of Expiring Meds (after 3 Months)</th>
                            </tr>
                            <tr>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Expiration Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($expired_list as $expired_item)
                            <tr>
                                <td><b>{{$expired_item->pharmacysub->pharmacysupplymaster->name}}</b></td>
                                <td>{{$expired_item->displayQty()}}</td>
                                <td><b class="text-danger">{{date('m/d/Y (D)', strtotime($expired_item->expiration_date))}}</b></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Current Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($si_array as $key => $si)
                        <tr>
                            <td>{{$si['name']}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    
</script>
@endsection