@extends('layouts.app')

@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div>
                    Line List
                </div>
                <div>
                    <form action="{{route('linelist.create')}}" method="POST">
                        @csrf
                        <div class="form-check">
                          <label class="form-check-label">
                            <input type="checkbox" class="form-check-input" name="isOverride" id="isOverride" value="1">
                            Override Mode
                          </label>
                        </div>
                        <button class="btn btn-success" name="submit" value="1">Create LaSalle</button>
                        <button class="btn btn-success" name="submit" value="2">Create ONI</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('status'))
                <div class="alert alert-{{session('statustype')}}" role="alert">
                    {{session('status')}}
                </div>
                <hr>
            @endif
            <table class="table text-center">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Date Created</th>
                        <th></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($list as $key => $item)
                    @php
                    if($item->type == 1) {
                        $link = 'oni';
                    }
                    else {
                        $link = 'lasalle';
                    }
                    @endphp
                    <tr>
                        <td scope="row">{{$key+1}}</td>
                        <td>{{($item->type == 1) ? 'ONI' : 'LASALLE'}}</td>
                        <td>{{$item->created_at}}</td>
                        <td class="text-center"><a class="btn btn-primary" href="linelist/{{$link}}/print/{{$item->id}}?s=legal">Print (Legal)</a></td>
                        <td class="text-center"><a class="btn btn-primary" href="linelist/{{$link}}/print/{{$item->id}}?s=a4">Print (A4)</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
