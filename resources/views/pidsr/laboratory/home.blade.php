@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <div><b>Laboratory Logbook</b></div>
                    <div>
                        <a href="{{route('pidsr_laboratory_new')}}" class="btn btn-success">Add Sample</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @if(session('msg'))
                <div class="alert alert-{{session('msgtype')}} text-center" role="alert">
                    {{session('msg')}}
                </div>
                @endif
                @if($list->count() != 0)
                <table class="table table-bordered">
                    <thead class="thead-light text-center">
                        <tr>
                            <th>ID</th>
                            <th>Action</th>
                            <th>Linked EDCS-IS Case ID</th>
                            <th>Disease</th>
                            <th>Name</th>
                            <th>Age/Sex</th>
                            <th>Date Swab Collected</th>
                            <th>Specimen / Type</th>
                            <th>Result</th>
                            <th>Encoded by/at</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($list as $ind => $l)
                        <tr>
                            <td class="text-center"><a href="{{route('pidsr_laboratory_view', $l->id)}}">#{{$l->id}}</a></td>
                            <td class="text-center"><a href="{{route('pidsr_laboratory_print', $l->id)}}" class="btn btn-primary">Print</a></td>
                            <td class="text-center">{{(!is_null($l->for_case_id)) ? $l->for_case_id : 'N/A'}}</td>
                            <td class="text-center">{{$l->disease_tag}}</td>
                            <td>{{$l->getName()}}</td>
                            <td class="text-center">{{$l->age}}/{{$l->gender}}</td>
                            <td class="text-center">{{date('m/d/Y', strtotime($l->date_collected))}}</td>
                            <td class="text-center">
                                <div>{{$l->specimen_type}}</div>
                                <div>{{$l->test_type}}</div>
                            </td>
                            <td class="text-center">{{$l->result}}</td>
                            <td class="text-center">
                                <div>{{$l->user->name}}</div>
                                <div>{{date('m/d/Y h:i A', strtotime($l->created_at))}}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="pagination justify-content-center mt-3">
                    {{$list->appends(request()->input())->links()}}
                </div>
                @else
                <p class="text-center">Results is currently empty.</p>
                @endif
            </div>
        </div>
    </div>
@endsection