@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between">
                <div>
                CIF List
                </div>
                <div>
                    @if($records > 0)
                        <a href="{{route('forms.create')}}" class="btn btn-success">New CIF</a>
                    @else
                    <span class="d-inline-block" tabindex="0" data-toggle="tooltip" title="Create patient record first to process CIF.">
                        <button class="btn btn-success" style="pointer-events: none;" type="button" disabled>New CIF</button>
                    </span>
                    @endif
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('status'))
                <div class="alert alert-{{session('statustype')}}" role="alert">
                    {{session('status')}}
                </div>
            @endif
            
            <form action="{{route('forms.index')}}" method="GET">
                <div class="input-group mb-3">
                    <select class="form-control" name="view" id="">
                        <option value="1" {{(request()->get('view') == '1') ? 'selected' : ''}}>Show All Records</option>
                        <option value="2" {{(request()->get('view') == '2') ? 'selected' : ''}}>Show All Except Records that has less than 5 Days Exposure History from this day</option>
                        <option value="3" {{(request()->get('view') == '3') ? 'selected' : ''}}>Show All Except Records that has not been exported to Excel yet</option>
                    </select>
                    <div class="input-group-append">
                      <button class="btn btn-outline-info" type="submit"><i class="fas fa-filter mr-2"></i>Filter</button>
                    </div>
                </div>
            </form>

            @if(request()->get('view') == null)
            <div class="alert alert-info" role="alert">
                <span>Displaying CIF results that were only scheduled for swab collection today ({{date('m/d/Y')}}). Total count is: {{count($forms)}}</span>
            </div>
            @endif

            <form action="{{route('forms.export')}}" method="POST">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered table-sm" id="table_id">
                        <thead>
                            <tr>
                                <th colspan="18" class="text-right"><button type="submit" class="btn btn-primary my-2" id="submit">Export to Excel</button></th>
                            </tr>
                            <tr class="text-center">
                                <th style="vertical-align: middle;"><input type="checkbox" class="checks mx-2" name="" id="select_all"></th>
                                <th style="vertical-align: middle;">Name</th>
                                <th style="vertical-align: middle;">Philhealth</th>
                                <th style="vertical-align: middle;">Mobile</th>
                                <th style="vertical-align: middle;">Birthdate</th>
                                <th style="vertical-align: middle;">Age/Gender</th>
                                <th style="vertical-align: middle;">Street</th>
                                <th style="vertical-align: middle;">Brgy</th>
                                <th style="vertical-align: middle;">City</th>
                                <th style="vertical-align: middle;">Type of Client</th>
                                <th style="vertical-align: middle;">Date of Collection</th>
                                <th style="vertical-align: middle;">Test Type</th>
                                <th style="vertical-align: middle;">Status</th>
                                <th style="vertical-align: middle;">Encoded By</th>
                                <th style="vertical-align: middle;">Encoded At</th>
                                <th style="vertical-align: middle;">Printed?</th>
                                <th style="vertical-align: middle;">Date Printed</th>
                                <th style="vertical-align: middle;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($forms as $form)
                                @if($form->user->brgy_id == auth()->user()->brgy_id || is_null(auth()->user()->brgy_id))
                                    <?php
            
                                        if($form->expoitem1 == 1) {
                                            $emsg = "YES";
                                        }
                                        else if($form->expoitem1 == 2) {
                                            $emsg = "NO";
                                        }
                                        else {
                                            $emsg = "UNKNOWN";
                                        }
            
                                        if(is_null($form->expoDateLastCont)) {
                                            $edate = "N/A";
                                        } 
                                        else {
                                            $edate = date('m/d/Y', strtotime($form->expoDateLastCont));
                                        }
            
                                        if($form->isExported == 1) {
                                            $textcolor = 'success';
                                        }
                                        else {
                                            $textcolor = 'warning';
                                        }
                                    ?>
                                    <tr class="bg-{{$textcolor}}">
                                        <th class="text-center" style="vertical-align: middle;">
                                            <input type="checkbox" class="checks mx-2" name="listToPrint[]" id="" value="{{$form->id}}">
                                        </th>
                                        <td style="vertical-align: middle;">{{$form->records->lname}}, {{$form->records->fname}} {{$form->records->mname}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{(!is_null($form->records->philhealth)) ? $form->records->philhealth : 'N/A'}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{$form->records->mobile}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{date('m/d/Y', strtotime($form->records->bdate))}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{$form->records->getAge()}} / {{$form->records->gender}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{$form->records->address_street}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{$form->records->address_brgy}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{$form->records->address_city}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{$form->pType}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{$form->testDateCollected1}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{$form->testType1}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{$form->testResult1}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{$form->user->name}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{date("m/d/Y h:i A", strtotime($form->created_at))}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{($form->isExported == 1) ? 'YES' : 'NO'}}</td>
                                        <td style="vertical-align: middle;" class="text-center">{{(!is_null($form->exportedDate)) ? date('m/d/Y h:i A', strtotime($form->exportedDate)) : ''}}</td>
                                        <td style="vertical-align: middle;" class="text-center">
                                            <a href="forms/{{$form->id}}/edit" class="btn btn-primary btn-sm">Edit</a>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                            
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    $(document).ready(function () {
        $('#table_id').DataTable();

        $('#select_all').change(function() {
        var checkboxes = $(this).closest('form').find(':checkbox');
        checkboxes.prop('checked', $(this).is(':checked'));
        });
    });

    $('#submit').prop('disabled', true);

    $('input:checkbox').click(function() {
        if ($(this).is(':checked')) {
            $('#submit').prop("disabled", false);
        } else {
        if ($('.checks').filter(':checked').length < 1){
            $('#submit').attr('disabled',true);}
        }
    });
</script>
@endsection