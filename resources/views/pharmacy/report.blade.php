@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header"><b>Report</b> (Branch: {{auth()->user()->pharmacybranch->name}})</div>
        <div class="card-body">
            <form action="" method="GET">
                <div class="card">
                    <div class="card-header"><b>Select Type of Report</b></div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="year"><b class="text-danger">*</b>Select Year</label>
                                    <select class="form-control" name="year" id="year" required>
                                      @foreach(range(date('Y'), 2020) as $y)
                                          <option value="{{$y}}">{{$y}}</option>
                                      @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-8" id="div2">
                                <div class="form-group">
                                    <label for="type"><b class="text-danger">*</b>Select Type</label>
                                    <select class="form-control" name="type" id="type" required>
                                      <option value="" disabled selected>Choose...</option>
                                      <option value="YEARLY">YEARLY (CURRENT)</option>
                                      <option value="QUARTERLY">QUARTERLY</option>
                                      <option value="MONTHLY">MONTHLY</option>
                                      <option value="WEEKLY">WEEKLY</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="div3">
                                <div class="form-group d-none" id="squarter">
                                    <label for="quarter"><b class="text-danger">*</b>Select Quarter</label>
                                    <select class="form-control" name="quarter" id="quarter">
                                      <option value="1">1ST QUARTER</option>
                                      <option value="2">2ND QUARTER</option>
                                      <option value="3">3RD QUARTER</option>
                                      <option value="4">4TH QUARTER</option>
                                    </select>
                                </div>
                                <div class="form-group d-none" id="smonth">
                                    <label for="month"><b class="text-danger">*</b>Select Month</label>
                                    <select class="form-control" name="month" id="month">
                                      <option value="1">JANUARY</option>
                                      <option value="2">FEBRUARY</option>
                                      <option value="3">MARCH</option>
                                      <option value="4">APRIL</option>
                                      <option value="5">MAY</option>
                                      <option value="6">JUNE</option>
                                      <option value="7">JULY</option>
                                      <option value="8">AUGUST</option>
                                      <option value="9">SEPTEMBER</option>
                                      <option value="10">OCTOBER</option>
                                      <option value="11">NOVEMBER</option>
                                      <option value="12">DECEMBER</option>
                                    </select>
                                </div>
                                <div class="form-group d-none" id="sweek">
                                    <label for="week"><b class="text-danger">*</b>Select Week</label>
                                    <input type="number" min="1" max="53" class="form-control" name="week" id="week" value="{{date('W')}}">
                                </div>
                            </div>
                        </div>
                        @if(auth()->user()->isAdminPharmacy())

                        @endif
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </div>
                </div>
            </form>

            @if(request()->input('type'))
            <hr>
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><b>Gender and Age Group of Requestors</b></div>
                        <div class="card-body">
                            <canvas id="age_group_chart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header"><b>Types of Requestors</b></div>
                        <div class="card-body">
                            <canvas id="concerns_chart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered table-striped text-center">
                        <thead class="thead-light">
                            <tr>
                                <th colspan="3">Top Fast Moving Meds</th>
                            </tr>
                            <tr>
                                <th>#</th>
                                <th>Item Name</th>
                                <th>Total Stocks Issued</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fm_array as $fm)
                            <tr>
                                <td>#{{$loop->iteration}}</td>
                                <td><a href="">{{$fm['name']}}</a></td>
                                <td>{{$fm['qty_total']}} {{Str::plural('PC', $fm['qty_total'])}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="table table-striped table-bordered text-center">
                        <thead class="thead-light">
                            <tr>
                                <th colspan="4">Top 10 Requestors in Entities (BHS/Hospitals/Others)</th>
                            </tr>
                            <tr>
                                <th>Top</th>
                                <th>Name</th>
                                <th>Issued QTY (Boxes)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($entities_arr as $ind => $r)
                            <tr>
                                <td>#{{$ind+1}}</td>
                                <td><a href="{{route('pharmacy_view_branch', $r['id'])}}">{{$r['name']}}</a></td>
                                <td>{{$r['issued_box_count']}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="col-md-6">
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
            <hr>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="thead-light text-center">
                        <tr>
                            <th rowspan="2">Name</th>
                            <th rowspan="2">Current Stock</th>
                            @for($i=1;$i<=12;$i++)
                            <th colspan="2">{{mb_strtoupper(Carbon\Carbon::create()->month($i)->format('M'))}}</th>
                            @endfor
                        </tr>
                        <tr>
                            @for($i=1;$i<=12;$i++)
                            <th class="text-success">+</th>
                            <th class="text-danger">-</th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($si_array as $key => $si)
                        <tr>
                            <td><b><a href="{{route('pharmacy_itemlist_viewitem', $si['id'])}}">{{$si['name']}}</a></b></td>
                            <td class="text-center"><small>{{$si['current_stock']}}</small></td>
                            @foreach($si['monthly_stocks'] as $ms)
                            <td class="text-center {{($ms['received'] != 0) ? 'text-success font-weight-bold' : ''}}">{{$ms['received']}}</td>
                            <td class="text-center {{($ms['issued'] != 0) ? 'text-danger font-weight-bold' : ''}}">{{$ms['issued']}}</td>
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
$('#type').change(function (e) { 
    e.preventDefault();
    if($(this).val() == 'YEARLY') {
        $('#squarter').addClass('d-none');
        $('#smonth').addClass('d-none');
        $('#sweek').addClass('d-none');

        $('#quarter').prop('required', false);
        $('#month').prop('required', false);
        $('#week').prop('required', false);

        $('#div2').addClass('col-md-8');
        $('#div2').removeClass('col-md-4');
        $('#div3').addClass('d-none');
    }
    else if($(this).val() == 'QUARTERLY') {
        $('#squarter').removeClass('d-none');
        $('#smonth').addClass('d-none');
        $('#sweek').addClass('d-none');

        $('#quarter').prop('required', true);
        $('#month').prop('required', false);
        $('#week').prop('required', false);

        $('#div2').removeClass('col-md-8');
        $('#div2').addClass('col-md-4');
        $('#div3').removeClass('d-none');
    }
    else if($(this).val() == 'MONTHLY') {
        $('#squarter').addClass('d-none');
        $('#smonth').removeClass('d-none');
        $('#sweek').addClass('d-none');

        $('#div2').removeClass('col-md-8');
        $('#div2').addClass('col-md-4');
        $('#div3').removeClass('d-none');
    }
    else if($(this).val() == 'WEEKLY') {
        $('#squarter').addClass('d-none');
        $('#smonth').addClass('d-none');
        $('#sweek').removeClass('d-none');

        $('#div2').removeClass('col-md-8');
        $('#div2').addClass('col-md-4');
        $('#div3').removeClass('d-none');
    }
}).trigger('change');

@if(request()->input('type'))
var male_set = {{json_encode($age_group_set_male)}};
var female_set = {{json_encode($age_group_set_female)}};

var ctx = document.getElementById('age_group_chart').getContext('2d');
var data = {
    labels: ['< 10', '11-20', '21-30', '31-40', '41-50', '51-60', '> 61'],
    datasets: [
        {
            label: 'Male',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            borderColor: 'rgba(75, 192, 192, 1)',
            borderWidth: 1,
            data: male_set, // Your data for Dataset 1
        },
        {
            label: 'Female',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            borderColor: 'rgba(255, 99, 132, 1)',
            borderWidth: 1,
            data: female_set, // Your data for Dataset 2
        },
    ],
};

var options = {
    scales: {
        x: {
            stacked: true,
        },
        y: {
            stacked: true,
        },
    },
};

var stackedBarChart = new Chart(ctx, {
    type: 'bar',
    data: data,
    options: options,
});

var labels = [
    @foreach ($reason_array as $reason)
        '{{ $reason['name'] }}',
    @endforeach
];

var reasonCount = {{json_encode(array_column($reason_array, 'count'))}};

var dynamicColors = [];
for (var i = 0; i < reasonCount.length; i++) {
    dynamicColors.push('rgba(' +
        Math.floor(Math.random() * 256) + ',' +
        Math.floor(Math.random() * 256) + ',' +
        Math.floor(Math.random() * 256) + ',' +
        '0.6)');
}

var data = {
    labels: labels,
    datasets: [{
        data: reasonCount, // Values for each slice of the pie
        backgroundColor: dynamicColors,
    }],
};

// Get the canvas element
var ctx = document.getElementById('concerns_chart').getContext('2d');

// Create the pie chart
var myPieChart = new Chart(ctx, {
    type: 'pie',
    data: data,
});
@endif
</script>
@endsection