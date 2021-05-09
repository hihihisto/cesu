<?php

namespace App\Http\Controllers;

use App\Models\Forms;
use App\Models\Records;
use PDF;
use App\Models\LinelistSubs;
use Illuminate\Http\Request;
use App\Models\LinelistMasters;

class LineListController extends Controller
{
    public function index() {
        $list = LinelistMasters::all();

        return view('linelist_index', ['list' => $list]);
    }

    public function createoni() {

        $list = Forms::find(1);

        return view('linelist_createoni', ['list' => $list]);
    }

    public function printoni($id) {
        /*
        $pdf = PDF::loadView('oni_pdf', ['details' => $details, 'list' => $list])->setPaper('a4', 'landscape');
        return $pdf->download('invoice.pdf');
        */

        $details = LineListMasters::find($id);
        $list = LineListSubs::where('linelist_master_id', $id)->orderBy('specNo', 'asc')->get();

        return view('oni_pdf', ['details' => $details, 'list' => $list]);
    }

    public function oniStore(Request $request) {

        $master = $request->user()->linelistmaster()->create([
            'type' => 1, //ONI = 1, LaSalle = 2
            'dru' => $request->dru,
            'contactPerson' => $request->contactPerson,
            'contactMobile' => $request->contactMobile,
        ]);

        for($i=0;$i<count($request->user);$i++) {
            $query = LinelistSubs::create([
                'linelist_master_id' => $master->id,
                'specNo' => $i+1,
                'dateAndTimeCollected' => $request->dateCollected[$i]." ".$request->timeCollected[$i],
                'accessionNo' => $request->accessionNo[$i],
                'records_id' => $request->user[$i],
                'remarks' => $request->remarks[$i],
                'oniSpecType' => $request->oniSpecType[$i],
                'oniReferringHospital' => $request->oniReferringHospital[$i]
            ]);
        }

        dd('done');
    }

    public function ajaxGetLineList () {
        $query = Forms::where('testDateCollected1', date('2021-05-07'))->pluck('id')->toArray();

        $query = Records::whereIn('id', $query)->orderBy('lname', 'asc')->get();

        $sdata['data'] = $query;
        echo json_encode($sdata);
        exit;
    }
}
