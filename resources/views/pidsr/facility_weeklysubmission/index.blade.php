@extends('layouts.app')

@section('content')
    <form action="{{route('edcs_facility_weeklysubmission_process', [$f->sys_code1, $year, $mw])}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between">
                        <div><b>Weekly Submission</b></div>
                        <div><button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modelId">Change Reporting Period</button></div>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('msg'))
                    <div class="alert alert-{{session('msgtype')}}" role="alert">
                        {{session('msg')}}
                    </div>
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for=""><b class="text-danger">*</b>Facility Name</label>
                                <input type="text" class="form-control" value="{{$f->facility_name}}" disabled>
                              </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status"><b class="text-danger">*</b>Submission Status for MW:{{$mw}} - Year: {{$year}}</label>
                                <select class="form-control" name="status" id="status" required>
                                  <option value="" disabled {{is_null(old('status', $d->status)) ? 'selected' : ''}}>Choose...</option>
                                  <option value="ZERO CASE" {{(old('status', $d->status) == 'ZERO CASE') ? 'selected' : ''}}>ZERO CASE</option>
                                  <option value="SUBMITTED" {{(old('status', $d->status) == 'SUBMITTED') ? 'selected' : ''}}>SUBMITTED</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    
                    <div id="ifSubmitted" class="d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header"><b>Vaccine-Preventable Disease (VPDs)</b></div>
                                    <div class="card-body">
                                        <div class="form-group">
                                          <label for="afp_count"><b class="text-danger">*</b>Acute Flaccid Paralysis</label>
                                          <input type="number" class="form-control" name="afp_count" id="afp_count" value="{{old('afp_count', $d->afp_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="diph_count"><b class="text-danger">*</b>Diphtheria</label>
                                            <input type="number" class="form-control" name="diph_count" id="diph_count" value="{{old('diph_count', $d->diph_count)}}" min="0" max="999">
                                          </div>
                                          <div class="form-group">
                                            <label for="measles_count"><b class="text-danger">*</b>Measles-Rubella</label>
                                            <input type="number" class="form-control" name="measles_count" id="measles_count" value="{{old('measles_count', $d->measles_count)}}" min="0" max="999">
                                          </div>
                                          <div class="form-group">
                                            <label for="nt_count"><b class="text-danger">*</b>Neonatal Tetanus</label>
                                            <input type="number" class="form-control" name="nt_count" id="nt_count" value="{{old('nt_count', $d->nt_count)}}" min="0" max="999">
                                          </div>
                                          <div class="form-group">
                                            <label for="nnt_count"><b class="text-danger">*</b>Non-Neonatal Tetanus</label>
                                            <input type="number" class="form-control" name="nnt_count" id="nnt_count" value="{{old('nnt_count', $d->nnt_count)}}" min="0" max="999">
                                          </div>
                                          <div class="form-group">
                                            <label for="pert_count"><b class="text-danger">*</b>Pertussis</label>
                                            <input type="number" class="form-control" name="pert_count" id="pert_count" value="{{old('pert_count', $d->pert_count)}}" min="0" max="999">
                                          </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header"><b>Zoonotic/Vector-Borne Diseases</b></div>
                                    <div class="card-body">
                                        <div class="form-group">
                                          <label for="chikv_count"><b class="text-danger">*</b>Chikungunya</label>
                                          <input type="number" class="form-control" name="chikv_count" id="chikv_count" value="{{old('chikv_count', $d->chikv_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="dengue_count"><b class="text-danger">*</b>Dengue</label>
                                            <input type="number" class="form-control" name="dengue_count" id="dengue_count" value="{{old('dengue_count', $d->dengue_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="lepto_count"><b class="text-danger">*</b>Leptospirosis</label>
                                            <input type="number" class="form-control" name="lepto_count" id="lepto_count" value="{{old('lepto_count', $d->lepto_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="rabies_count"><b class="text-danger">*</b>Rabies</label>
                                            <input type="number" class="form-control" name="rabies_count" id="rabies_count" value="{{old('rabies_count', $d->rabies_count)}}" min="0" max="999">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header"><b>Food and Waterborne Diseases (FWBDs)</b></div>
                                    <div class="card-body">
                                        <div class="form-group">
                                          <label for="abd_count"><b class="text-danger">*</b>Acute Bloody Diarrhea</label>
                                          <input type="number" class="form-control" name="abd_count" id="abd_count" value="{{old('abd_count', $d->abd_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="hepa_count"><b class="text-danger">*</b>Acute Viral Hepatitis</label>
                                            <input type="number" class="form-control" name="hepa_count" id="hepa_count" value="{{old('hepa_count', $d->hepa_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="cholera_count"><b class="text-danger">*</b>Cholera</label>
                                            <input type="number" class="form-control" name="cholera_count" id="cholera_count" value="{{old('cholera_count', $d->cholera_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="rota_count"><b class="text-danger">*</b>RotaVirus</label>
                                            <input type="number" class="form-control" name="rota_count" id="rota_count" value="{{old('rota_count', $d->rota_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="typhoid_count"><b class="text-danger">*</b>Typhoid Fever</label>
                                            <input type="number" class="form-control" name="typhoid_count" id="typhoid_count" value="{{old('typhoid_count', $d->typhoid_count)}}" min="0" max="999">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header"><b>Other Diseases</b></div>
                                    <div class="card-body">
                                        <div class="form-group">
                                          <label for="ili_count"><b class="text-danger">*</b>Influenza-Like Illness</label>
                                          <input type="number" class="form-control" name="ili_count" id="ili_count" value="{{old('ili_count', $d->ili_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="ames_count"><b class="text-danger">*</b>Acute Meningitis Encephalitis Syndrome (AMES)</label>
                                            <input type="number" class="form-control" name="ames_count" id="ames_count" value="{{old('ames_count', $d->ames_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="hfmd_count"><b class="text-danger">*</b>HFMD</label>
                                            <input type="number" class="form-control" name="hfmd_count" id="hfmd_count" value="{{old('hfmd_count', $d->hfmd_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="meningo_count"><b class="text-danger">*</b>Meningococcemia</label>
                                            <input type="number" class="form-control" name="meningo_count" id="meningo_count" value="{{old('meningo_count', $d->meningo_count)}}" min="0" max="999">
                                        </div>
                                        <div class="form-group">
                                            <label for="sari_count"><b class="text-danger">*</b>Severe Acute Respiratory Infection</label>
                                            <input type="number" class="form-control" name="sari_count" id="sari_count" value="{{old('sari_count', $d->sari_count)}}" min="0" max="999">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                          <label for="excel_file"><b class="text-danger">*</b>Upload Excel File</label>
                          <input type="file" class="form-control-file" name="excel_file" id="excel_file" accept=".xls,.xlsx">
                        </div>
                    </div>
                </div>
                
                <div class="card-footer">
                    <button type="submit" class="btn btn-success btn-block" {{($s_type == 'EARLY_CURRENT_WEEK') ? 'disabled' : ''}}>Submit</button>
                </div>
            </div>
        </div>
    </form>

    <form action="" method="GET">
        <div class="modal fade" id="modelId" tabindex="-1" role="dialog" aria-labelledby="modelTitleId" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Change Reporting Period</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="year"><b class="text-danger">*</b>Year</label>
                            <input type="number" class="form-control" name="year" id="year" min="2024" max="{{date('Y')}}" value="{{(request()->input('year')) ? request()->input('year') : date('Y')}}" required>
                        </div>
                        <div class="form-group">
                            <label for="mw"><b class="text-danger">*</b>Morbidity Week</label>
                            <input type="number" class="form-control" name="mw" id="mw" min="1" max="52" value="{{(request()->input('mw')) ? request()->input('mw') : Carbon\Carbon::now()->subWeek(1)->format('W')}}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success btn-block">Change</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    
    <script>
        $('#status').change(function (e) { 
            e.preventDefault();
            if($(this).val() == 'ZERO CASE') {
                $('#ifSubmitted').addClass('d-none');

                $('#abd_count').prop('required', false);
                $('#afp_count').prop('required', false);
                $('#ames_count').prop('required', false);
                $('#hepa_count').prop('required', false);
                $('#chikv_count').prop('required', false);
                $('#cholera_count').prop('required', false);
                $('#dengue_count').prop('required', false);
                $('#diph_count').prop('required', false);
                $('#hfmd_count').prop('required', false);
                $('#ili_count').prop('required', false);
                $('#lepto_count').prop('required', false);
                $('#measles_count').prop('required', false);
                $('#meningo_count').prop('required', false);
                $('#nt_count').prop('required', false);
                $('#nnt_count').prop('required', false);
                $('#pert_count').prop('required', false);
                $('#rabies_count').prop('required', false);
                $('#rota_count').prop('required', false);
                $('#sari_count').prop('required', false);
                $('#typhoid_count').prop('required', false);
                $('#excel_file').prop('required', false);
            }
            else {
                $('#ifSubmitted').removeClass('d-none');

                $('#abd_count').prop('required', true);
                $('#afp_count').prop('required', true);
                $('#ames_count').prop('required', true);
                $('#hepa_count').prop('required', true);
                $('#chikv_count').prop('required', true);
                $('#cholera_count').prop('required', true);
                $('#dengue_count').prop('required', true);
                $('#diph_count').prop('required', true);
                $('#hfmd_count').prop('required', true);
                $('#ili_count').prop('required', true);
                $('#lepto_count').prop('required', true);
                $('#measles_count').prop('required', true);
                $('#meningo_count').prop('required', true);
                $('#nt_count').prop('required', true);
                $('#nnt_count').prop('required', true);
                $('#pert_count').prop('required', true);
                $('#rabies_count').prop('required', true);
                $('#rota_count').prop('required', true);
                $('#sari_count').prop('required', true);
                $('#typhoid_count').prop('required', true);
                $('#excel_file').prop('required', true);
            }
        });
    </script>
@endsection