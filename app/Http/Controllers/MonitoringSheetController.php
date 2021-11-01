<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Forms;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use App\Models\MonitoringSheetMaster;
use App\Models\MonitoringSheetSub;
use IlluminateAgnostic\Collection\Support\Str;

class MonitoringSheetController extends Controller
{
    public function create($id) {
        $data = Forms::findOrFail($id);

        $search = MonitoringSheetMaster::where('forms_id', $data->id)->first();

        if(!$search) {
            $foundunique = false;

            while(!$foundunique) {
                $majik = Str::random(30);
                
                $search = MonitoringSheetMaster::where('magicURL', $majik);
                if($search->count() == 0) {
                    $foundunique = true;
                }
            }

            $create = new MonitoringSheetMaster;

            $create->forms_id = $data->id;
            $create->region = '4A';
            $create->date_lastexposure = (!is_null($data->expoDateLastCont)) ? $data->expoDateLastCont : $data->interviewDate;
            $create->date_endquarantine = Carbon::parse($data->interviewDate)->addDays(13)->format('Y-m-d');
            $create->magicURL = $majik;

            $create->save();

            return redirect()->route('msheet.view', ['id' => $create->id])
            ->with('msg', 'Monitoring Sheet has been created successfully. You can now proceed.')
            ->with('msgtype', 'success');
        }
        else {
            return redirect()->route('forms.index')
            ->with('status', 'You are not allowed to do that.')
            ->with('statustype', 'warning');
        }
    }
    
    public function view($id) {
        $data = MonitoringSheetMaster::findOrFail($id);
        $subdata = MonitoringSheetSub::where('monitoring_sheet_masters_id', $data->id)->get();

        $date1 = Carbon::now();
        $date2 = Carbon::parse($data->date_endquarantine);

        if($date1->lt($date2)) {
            $end_date = date('Y-m-d');
        }
        else {
            $end_date = date('Y-m-d', strtotime($data->date_endquarantine));
        }

        if(date('A') == 'AM') {
            $currentmer = 'AM';
        }
        else {
            $currentmer = 'PM';
        }

        $period = CarbonPeriod::create(date('Y-m-d', strtotime($data->date_endquarantine.' -13 Days')), $end_date);

        return view('msheet_view', ['data' => $data, 'period' => $period, 'subdata' => $subdata, 'currentmer' => $currentmer]);
    }

    public function print($id) {
        $data = MonitoringSheetMaster::findOrFail($id);

        $subdata = MonitoringSheetSub::where('monitoring_sheet_masters_id', $data->id)->get();

        $date1 = Carbon::now();
        $date2 = Carbon::parse($data->date_endquarantine);

        $end_date = date('Y-m-d', strtotime($data->date_endquarantine));

        if(date('A') == 'AM') {
            $currentmer = 'AM';
        }
        else {
            $currentmer = 'PM';
        }

        $period = CarbonPeriod::create(date('Y-m-d', strtotime($data->date_endquarantine.' -13 Days')), $end_date);

        return view('msheet_print', ['data' => $data, 'period' => $period, 'subdata' => $subdata, 'currentmer' => $currentmer]);
    }

    public function viewdate($msheet_master_id, $date, $mer) {
        $master = MonitoringSheetMaster::findOrFail($msheet_master_id);

        $period = CarbonPeriod::create(date('Y-m-d', strtotime($master->date_endquarantine.' -13 Days')), $master->date_endquarantine)->toArray();
        $dateArr = [];
        foreach($period as $ind) {
            array_push($dateArr, $ind->format('Y-m-d'));
        }

        if(in_array($date, $dateArr)) {
            $subdata = MonitoringSheetSub::where('monitoring_sheet_masters_id', $msheet_master_id)
            ->whereDate('forDate', $date)
            ->where('forMeridian', $mer)
            ->first();

            return view('msheet_view_date', ['data' => $master, 'date' => $date, 'mer' => $mer, 'subdata' => $subdata]);
        }
        else {
            return redirect()->route('msheet.view', ['id' => $master->id])
            ->with('msg', 'You are not allowed to do that.')
            ->with('msgtype', 'warning');
        }
    }

    public function viewguest($magicurl) {
        
    }

    public function updatemonitoring(Request $request, $id, $date, $mer) {
        $master = MonitoringSheetMaster::findOrFail($id);

        $period = CarbonPeriod::create(date('Y-m-d', strtotime($master->date_endquarantine.' -13 Days')), $master->date_endquarantine)->toArray();
        $dateArr = [];
        foreach($period as $ind) {
            array_push($dateArr, $ind->format('Y-m-d'));
        }

        if(in_array($date, $dateArr)) {
            $sub = MonitoringSheetSub::updateOrCreate(['monitoring_sheet_masters_id' => $master->id, 'forDate' => $date, 'forMeridian' => $mer],
        [
            'fever' => ($request->fever) ? $request->fevertemp : NULL,
            'cough' => ($request->cough) ? 1 : 0,
            'sorethroat' => ($request->sorethroat) ? 1 : 0,
            'dob' => ($request->dob) ? 1 : 0,
            'colds' => ($request->colds) ? 1 : 0,
            'diarrhea' => ($request->diarrhea) ? 1 : 0,
            'os1' => !is_null($request->os1) ? mb_strtoupper($request->os1) : NULL,
            'os2' => !is_null($request->os2) ? mb_strtoupper($request->os2) : NULL,
            'os3' => !is_null($request->os3) ? mb_strtoupper($request->os3) : NULL,
        ]);

        return redirect()->route('msheet.view', ['id' => $master->id])
            ->with('msg', 'Status for ('.$date.' - '.$mer.') has been updated successfully.')
            ->with('msgtype', 'success');
        }
        else {
            return redirect()->route('msheet.view', ['id' => $master->id])
            ->with('msg', 'You are not allowed to do that.')
            ->with('msgtype', 'warning');
        }
    }
}
