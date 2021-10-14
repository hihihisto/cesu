@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header font-weight-bold">List of Currently Admitted in Facility <span class="badge badge-danger">{{number_format($list->count())}}</span></div>
            <div class="card-body">
                <table class="table table-bordered" id="facilityTable">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Age/Gender</th>
                            <th>Address</th>
                            <th>Occupation</th>
                            <th>Medical Status</th>
                            <th>Lab Status</th>
                            <th>Date Swabbed</th>
                            <th>Comorbidities</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $item)
                        @php
                        if(!is_null($item->testDateCollected2)) {
                            $testResult = $item->testResult2;
                            $testDate = date('m/d/Y', strtotime($item->testDateCollected2));
                        }
                        else {
                            $testResult = $item->testResult1;
                            $testDate = date('m/d/Y', strtotime($item->testDateCollected1));
                        }
                        @endphp
                        <tr>
                            <td class="text-center">{{$loop->iteration}}</td>
                            <td>{{$item->records->getName()}}</td>
                            <td class="text-center">{{$item->records->getAge().' / '.substr($item->records->gender,0,1)}}</td>
                            <td class="text-center">BRGY. {{$item->records->address_brgy}}</td>
                            <td class="text-center">{{(!is_null($item->records->occupation)) ? $item->records->occupation : 'N/A'}}</td>
                            <td class="text-center">{{$item->healthStatus}}</td>
                            <td class="text-center">{{$testResult}}</td>
                            <td class="text-center">{{$testDate}}</td>
                            <td class="text-center">{{(!is_null($item->COMO) && !in_array("None", explode(',', $item->COMO))) ? $item->COMO : 'N/A'}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $('#facilityTable').dataTable();
    </script>
@endsection