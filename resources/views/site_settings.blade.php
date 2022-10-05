@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header"><b>Site Settings</b></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><b>Pa-swab Settings</b></div>
                        <div class="card-body">
                            <div class="mb-3">
                              <label for="paswab_enabled" class="form-label">Pa-swab Status</label>
                              <select class="form-control" name="paswab_enabled" id="paswab_enabled">
                                <option value="1">Enabled</option>
                                <option value="0">Disabled</option>
                              </select>
                            </div>
                            <div class="mb-3">
                                <label for="paswab_antigen_enabled" class="form-label">Pa-swab Antigen Mode</label>
                                <select class="form-control" name="paswab_antigen_enabled" id="paswab_antigen_enabled">
                                  <option value="1">Enabled</option>
                                  <option value="0">Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><b>Swab Test Settings</b></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="oniStartTime_am" class="form-label">Autotime Start (AM)</label>
                                <input type="time" class="form-control" name="oniStartTime_am" id="oniStartTime_am">
                            </div>
                            <div class="mb-3">
                              <label for="oniStartTime_pm" class="form-label">Autotime Start (PM)</label>
                              <input type="time" class="form-control" name="oniStartTime_pm" id="oniStartTime_pm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header"><b>Server Settings</b></div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="system_type" class="form-label">System Type</label>
                                <select class="form-control" name="system_type" id="system_type" required>
                                  <option value="regional">Regional</option>
                                  <option value="provincial">Provincial</option>
                                  <option value="municipal">Municipal</option>
                                </select>
                            </div>
                            <div class="mb-3">
                              <label for="default_dru_name" class="form-label">Default DRU Name</label>
                              <input type="text" class="form-control" name="default_dru_name" id="default_dru_name" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>    
@endsection