@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-8">
                <form action="{{route('he_check', [$event_code, $facility_code])}}" method="GET">
                    <div class="card">
                        <div class="card-header"><b>{{$he->event_name}}</b></div>
                        <div class="card-body">
                            @if(session('msg'))
                            <div class="alert alert-{{session('msgtype')}} text-center" role="alert">
                                {{session('msg')}}
                            </div>
                            @endif
                            <div class="form-group">
                                <label for="lname"><b class="text-danger">*</b>Last Name</label>
                                <input type="text" class="form-control" name="lname" id="lname" value="{{old('lname')}}" minlength="2" maxlength="50" placeholder="ex: DELA CRUZ" style="text-transform: uppercase;" pattern="[A-Za-z\- 'Ññ]+" required>
                            </div>
                            <div class="form-group">
                                <label for="fname"><b class="text-danger">*</b>First Name</label>
                                <input type="text" class="form-control" name="fname" id="fname" value="{{old('fname')}}" minlength="2" maxlength="50" placeholder="ex: JUAN" style="text-transform: uppercase;" pattern="[A-Za-z\- 'Ññ]+" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="mname">Middle Name <i>(If Applicable)</i></label>
                                        <input type="text" class="form-control" name="mname" id="mname" value="{{old('mname')}}" minlength="2" maxlength="50" placeholder="ex: SANCHEZ" style="text-transform: uppercase;" pattern="[A-Za-z\- 'Ññ]+">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="suffix">Suffix <i>(If Applicable)</i></label>
                                        <input type="text" class="form-control" name="suffix" id="suffix" value="{{old('suffix')}}" minlength="2" maxlength="3" placeholder="ex: JR, SR, III, IV" style="text-transform: uppercase;" pattern="[A-Za-z\- 'Ññ]+">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="bdate"><b class="text-danger">*</b>Birthdate</label>
                                <input type="date" class="form-control" name="bdate" id="bdate" value="{{old('bdate')}}" min="1900-01-01" max="{{date('Y-m-d', strtotime('yesterday'))}}" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success btn-block">Next</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
    </div>

    <div class="modal fade" id="facicheck" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center">
                        <div><b>General Trias City Epidemiology and Surveillance Unit (CESU)</b></div>
                        <div>{{$he->event_name}}</div>
                    </h5>
                </div>
                <div class="modal-body text-center">
                    <h5>You are encoding data linked with this facility:</h5>
                    <h4 class="my-3"><b class="text-success"><u>{{mb_strtoupper($f->facility_name)}}</u></b></h4>
                </div>
                <div class="modal-footer text-center">
                    <h5>If this is your encoding facility, you may now proceed.</h5>
                    <button type="button" class="btn btn-success btn-block" data-dismiss="modal">Proceed</button>
                    <hr>
                    <h5><b class="text-danger">If NOT</b>, do not proceed, close this form and use the right URL Code that was given by CESU Gen. Trias.</h5>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).bind('keydown', function(e) {
            if(e.ctrlKey && (e.which == 83)) {
                e.preventDefault();
                $('#submitBtn').trigger('click');
                $('#submitBtn').prop('disabled', true);
                setTimeout(function() {
                    $('#submitBtn').prop('disabled', false);
                }, 2000);
                return false;
            }
        });

        $('#facicheck').modal({backdrop: 'static', keyboard: false});
        $('#facicheck').modal('show');
    </script>
@endsection