@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Composite Measure</div>
            <div class="card-body">
                <table class="table table-bordered text-center">
                    <thead class="thead-light">
                        <tr>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Home Quarantine</th>
                            <th>TTMF</th>
                            <th>Hospital</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Close Contacts</td>
                            <td>{{$cc_count_total}}</td>
                            <td>{{$cc_count_hq}}</td>
                            <td>{{$cc_count_ttmf}}</td>
                            <td>{{$cc_count_hospital}}</td>
                        </tr>
                        <tr>
                            <td>Probable Cases</td>
                            <td>{{$probable_count_total}}</td>
                            <td>{{$probable_count_hq}}</td>
                            <td>{{$probable_count_ttmf}}</td>
                            <td>{{$probable_count_hospital}}</td>
                        </tr>
                        <tr>
                            <td>Suspect Cases</td>
                            <td>{{$suspected_count_total}}</td>
                            <td>{{$suspected_count_hq}}</td>
                            <td>{{$suspected_count_ttmf}}</td>
                            <td>{{$suspected_count_hospital}}</td>
                        </tr>
                        <tr>
                            <td>Active, Confirmed Cases</td>
                            <td>{{$activecases_count_total}}</td>
                            <td>{{$activecases_count_hq}}</td>
                            <td>{{$activecases_count_ttmf}}</td>
                            <td>{{$activecases_count_hospital}}</td>
                        </tr>
                        <tr>
                            <td>Asymptomatic</td>
                            <td>{{$activecases_count_asymptomatic_total}}</td>
                            <td>{{$activecases_count_asymptomatic_hq}}</td>
                            <td>{{$activecases_count_asymptomatic_ttmf}}</td>
                            <td>{{$activecases_count_asymptomatic_hospital}}</td>
                        </tr>
                        <tr>
                            <td>Mild w/ No Comorbidities</td>
                            <td>{{$activecases_count_mild_nocomorbid_total}}</td>
                            <td>{{$activecases_count_mild_nocomorbid_hq}}</td>
                            <td>{{$activecases_count_mild_nocomorbid_ttmf}}</td>
                            <td>{{$activecases_count_mild_nocomorbid_hospital}}</td>
                        </tr>
                        <tr>
                            <td>Mild w/ Comorbidities</td>
                            <td>{{$activecases_count_mild_withcomorbid_total}}</td>
                            <td>{{$activecases_count_mild_withcomorbid_hq}}</td>
                            <td>{{$activecases_count_mild_withcomorbid_ttmf}}</td>
                            <td>{{$activecases_count_mild_withcomorbid_hospital}}</td>
                        </tr>
                        <tr>
                            <td>Moderate</td>
                            <td>{{$activecases_count_moderate_total}}</td>
                            <td>{{$activecases_count_moderate_hq}}</td>
                            <td>{{$activecases_count_moderate_ttmf}}</td>
                            <td>{{$activecases_count_moderate_hospital}}</td>
                        </tr>
                        <tr>
                            <td>Severe</td>
                            <td>{{$activecases_count_severe_total}}</td>
                            <td>{{$activecases_count_severe_hq}}</td>
                            <td>{{$activecases_count_severe_ttmf}}</td>
                            <td>{{$activecases_count_severe_hospital}}</td>
                        </tr>
                        <tr>
                            <td>Critical</td>
                            <td>{{$activecases_count_critical_total}}</td>
                            <td>{{$activecases_count_critical_hq}}</td>
                            <td>{{$activecases_count_critical_ttmf}}</td>
                            <td>{{$activecases_count_critical_hospital}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection