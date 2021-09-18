<?php

namespace App\Http\Controllers;

use App\Models\Brgy;
use App\Models\Forms;
use Illuminate\Http\Request;

class JsonReportController extends Controller
{
    public function totalCases() {

    }

    public function dailyNewCases() {
        
    }

    public function casesDistribution() {

    }

    public function brgyCases() {
        $arr = [];

        $list = Brgy::where('city_id', 1)
        ->where('displayInList', '1')
        ->orderBy('brgyName', 'ASC')->get();

        foreach($list as $item) {
            /*
            $confirmedCases = Forms::with('records')
            ->whereHas('records', function($q) use ($item) {
                $q->where('address_brgy', $item->brgyName);
            })->where('status', 'approved')
            ->where('caseClassification', 'Confirmed')->count();
            */

            $activeCases = Forms::with('records')
            ->whereHas('records', function($q) use ($item) {
                $q->where('address_brgy', $item->brgyName);
            })->where('status', 'approved')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count();

            $deaths = Forms::with('records')
            ->whereHas('records', function($q) use ($item) {
                $q->where('address_brgy', $item->brgyName);
            })->where('status', 'approved')
            ->where('outcomeCondition', 'Died')->count();

            $recovered = Forms::with('records')
            ->whereHas('records', function($q) use ($item) {
                $q->where('address_brgy', $item->brgyName);
            })->where('status', 'approved')
            ->where('outcomeCondition', 'Recovered')->count();

            $confirmedCases = ($activeCases + $deaths + $recovered);

            array_push($arr, [
                'brgyName' => $item->brgyName,
                'numOfConfirmedCases' => $confirmedCases,
                'numOfActiveCases' => $activeCases,
                'numOfDeaths' => $deaths,
                'numOfRecoveries' => $recovered,
            ]);
        }
        
        return response()->json($arr);
    }

    public function genderBreakdown() {

        $arr = [];

        $male = Forms::with('records')
        ->whereHas('records', function($q) {
            $q->where('gender', 'MALE');
        })->where('status', 'approved')
        ->where('outcomeCondition', 'Active')
        ->where('caseClassification', 'Confirmed')->count();

        $female = Forms::with('records')
        ->whereHas('records', function($q) {
            $q->where('gender', 'FEMALE');
        })->where('status', 'approved')
        ->where('outcomeCondition', 'Active')
        ->where('caseClassification', 'Confirmed')->count();

        array_push($arr, [
            'gender' => 'MALE',
            'count' => $male,
        ]);

        array_push($arr, [
            'gender' => 'FEMALE',
            'count' => $female,
        ]);

        return response()->json($arr);
    }

    public function conditionBreakdown() {
        $arr = [];

        array_push($arr, [
            'status' => 'ASYMPTOMATIC',
            'count' => Forms::where('status', 'approved')
            ->where('healthStatus', 'Asymptomatic')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count(),
        ]);
        
        array_push($arr, [
            'status' => 'MILD',
            'count' => Forms::where('status', 'approved')
            ->where('healthStatus', 'Mild')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count(),
        ]);

        array_push($arr, [
            'status' => 'MODERATE',
            'count' => Forms::where('status', 'approved')
            ->where('healthStatus', 'Moderate')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count(),
        ]);
        
        array_push($arr, [
            'status' => 'SEVERE',
            'count' => Forms::where('status', 'approved')
            ->where('healthStatus', 'Severe')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count(),
        ]);

        array_push($arr, [
            'status' => 'CRITICAL',
            'count' => Forms::where('status', 'approved')
            ->where('healthStatus', 'Critical')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count(),
        ]);

        return response()->json($arr);
    }
}
