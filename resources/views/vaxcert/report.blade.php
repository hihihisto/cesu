@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header"><b>Report</b></div>
        <div class="card-body">
            <div class="text-center">
                <img src="{{asset('assets/images/CHO_LETTERHEAD_WITH_CESU.png')}}" class="mb-3 img-fluid" style="width: 50rem;">
            </div>
            <h3 class="text-center"><b>VaxCert Concerns Report</b></h3>
            <hr>
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card bg-success text-white text-center mb-3">
                        <div class="card-header"><h4>Grand Total Resolved VaxCert Concerns</h4></div>
                        <div class="card-body">
                            <h1><b>{{$get_total}}</b></h1>
                        </div>
                      </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="card bg-light text-center mb-3">
                        <div class="card-header"><h4>Resolved VaxCert Concerns for this Month ({{date('F')}})</h4></div>
                        <div class="card-body">
                            <h1><b>{{$get_total_current_month}}</b></h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-primary text-white text-center mb-3">
                        <div class="card-header"><h4>Resolved VaxCert Concerns for this Year ({{date('Y')}})</h4></div>
                        <div class="card-body">
                            <h1><b>{{$get_total_current_year}}</b></h1>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white text-center mb-3">
                        <div class="card-header"><h4>Resolved VaxCert Concerns for Previous Years</h4></div>
                        <div class="card-body">
                            <h1><b>{{$get_total_previous_year}}</b></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection