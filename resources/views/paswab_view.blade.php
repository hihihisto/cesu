@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card">
            <div class="card-header">Pa-Swab List</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead class="text-center">
                        <tr>
                            <th>Date Submitted</th>
                            <th>Name</th>
                            <th>Client Type</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($list as $item)
                            <tr>
                                <td>{{date('m/d/Y h:i:s A', strtotime($item->created_at))}}</td>
                                <td>{{$item->getName()}}</td>
                                <td></td>
                            </tr>
                        @empty
                            empty
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination justify-content-center mt-3">
                    {{$list->appends(request()->input())->links()}}
                </div>
            </div>
        </div>
    </div>
@endsection