<?php

namespace App\Http\Controllers;

use App\Models\Brgy;
use App\Models\Forms;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class JsonReportController extends Controller
{
    public function totalCases() {
        $arr = [];

        $totalActiveCases = Forms::with(['records' => function ($q) {
            $q->where('address_province', 'CAVITE')
            ->where('address_city', 'GENERAL TRIAS');
        }])
        ->where('status', 'approved')
        ->where('outcomeCondition', 'Active')
        ->where('caseClassification', 'Confirmed')
        ->count();

        $totalRecovered = Forms::with(['records' => function ($q) {
            $q->where('address_province', 'CAVITE')
            ->where('address_city', 'GENERAL TRIAS');
        }])
        ->where('status', 'approved')
        ->where('outcomeCondition', 'Recovered')
        ->count();
        
        $totalDeaths = Forms::with(['records' => function ($q) {
            $q->where('address_province', 'CAVITE')
            ->where('address_city', 'GENERAL TRIAS');
        }])
        ->where('status', 'approved')
        ->where('outcomeCondition', 'Died')
        ->count();

        $newActive = Forms::with(['records' => function ($q) {
            $q->where('address_province', 'CAVITE')
            ->where('address_city', 'GENERAL TRIAS');
        }])
        ->where('status', 'approved')
        ->where(function ($q) {
            $q->whereDate('morbidityMonth', date('Y-m-d'))
            ->whereBetween('dateReported', [date('Y-m-d', strtotime('-2 Days')), date('Y-m-d')]);
        })->where('outcomeCondition', 'Active')
        ->where('caseClassification', 'Confirmed')
        ->count();

        $lateActive = Forms::with(['records' => function ($q) {
            $q->where('address_province', 'CAVITE')
            ->where('address_city', 'GENERAL TRIAS');
        }])
        ->where('status', 'approved')
        ->where(function ($q) {
            $q->whereDate('morbidityMonth', date('Y-m-d'))
            ->whereDate('dateReported', '<=', date('Y-m-d', strtotime('-3 Days')));
        })->where('outcomeCondition', 'Active')
        ->where('caseClassification', 'Confirmed')
        ->count();

        $newRecovered = Forms::with(['records' => function ($q) {
            $q->where('address_province', 'CAVITE')
            ->where('address_city', 'GENERAL TRIAS');
        }])
        ->where('status', 'approved')
        ->whereDate('outcomeRecovDate', date('Y-m-d'))
        ->where('outcomeCondition', 'Recovered')
        ->count();

        $lateRecovered = Forms::with(['records' => function ($q) {
            $q->where('address_province', 'CAVITE')
            ->where('address_city', 'GENERAL TRIAS');
        }])
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', date('Y-m-d'))
        ->whereDate('dateReported', '<=', date('Y-m-d', strtotime('-10 Days')))
        ->where('outcomeCondition', 'Recovered')
        ->count();

        $newDeaths = Forms::with(['records' => function ($q) {
            $q->where('address_province', 'CAVITE')
            ->where('address_city', 'GENERAL TRIAS');
        }])
        ->where(function ($q) {
            $q->where('status', 'approved')
            ->whereDate('outcomeDeathDate', date('Y-m-d'))
            ->where('outcomeCondition', 'Died');
        })->orWhere(function ($q) {
            $q->where('status', 'approved')
            ->whereDate('morbidityMonth', '<', date('Y-m-d'))
            ->where('outcomeDeathDate', date('Y-m-d'));
        })->count();

        array_push($arr, [
            'totalActiveCases' => $totalActiveCases,
            'totalRecovered' => $totalRecovered,
            'totalDeaths' => $totalDeaths,
            'totalCases' => $totalActiveCases + $totalRecovered + $totalDeaths,
            'newActive' => $newActive,
            'lateActive' => $lateActive,
            'newRecovered' => $newRecovered,
            'lateRecovered' => $lateRecovered,
            'newDeaths' => $newDeaths,
        ]);

        return response()->json($arr);
    }

    public function dailyNewCases() {
        
    }

    public function casesDistribution() {
        ini_set('max_execution_time', 600);
        
        $arr = [];

        $period = CarbonPeriod::create('2021-01-01', date('Y-m-d'));

        $activeCount = 0;

        foreach ($period as $date) {
            $currentActiveCount = Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->where('status', 'approved')
            ->whereDate('morbidityMonth', $date->toDateString())
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')
            ->count();
            
            $currentRecoveredCount = Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->where('status', 'approved')
            ->whereDate('morbidityMonth', $date->toDateString())
            ->where('outcomeCondition', 'Recovered')
            ->count();

            $currentDiedCount = Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->where('status', 'approved')
            ->whereDate('morbidityMonth', $date->toDateString())
            ->where('outcomeCondition', 'Died')
            ->count();

            array_push($arr, [
                'date' => $date->toDateString(),
                'activeConfirmedCases' => ($currentActiveCount + $activeCount) - ($currentRecoveredCount + $currentDiedCount),
                'recoveredCases' => $currentRecoveredCount,
                'deathCases' => $currentDiedCount,
            ]);

            $activeCount += $currentActiveCount - $currentRecoveredCount - $currentDiedCount;
        }

        return response()->json($arr);
    }

    public function facilityCount() {
        $arr = [];

        array_push($arr, [
            'facilityCount' => Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->where('dispoType', 2)
            ->where('status', 'approved')
            ->where('caseClassification', 'Confirmed')
            ->where('outcomeCondition', 'Active')
            ->count(),
            'hqCount' => Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->where('dispoType', 3)
            ->where('status', 'approved')
            ->where('caseClassification', 'Confirmed')
            ->where('outcomeCondition', 'Active')
            ->count(),
            'hospitalCount' => Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->whereIn('dispoType', [1,5])
            ->where('status', 'approved')
            ->where('caseClassification', 'Confirmed')
            ->where('outcomeCondition', 'Active')
            ->count(),
        ]);

        return response()->json($arr);
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

            $activeCases = Forms::whereHas('records', function($q) use ($item) {
                $q->where('address_brgy', $item->brgyName);
            })->where('status', 'approved')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count();

            $deaths = Forms::whereHas('records', function($q) use ($item) {
                $q->where('address_brgy', $item->brgyName);
            })->where('status', 'approved')
            ->where('outcomeCondition', 'Died')->count();

            $recovered = Forms::whereHas('records', function($q) use ($item) {
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

        $male = Forms::with(['records' => function ($q) {
            $q->where('address_province', 'CAVITE')
            ->where('address_city', 'GENERAL TRIAS')
            ->where('gender', 'MALE');
        }])
        ->where('status', 'approved')
        ->where('outcomeCondition', 'Active')
        ->where('caseClassification', 'Confirmed')
        ->count();

        $female = Forms::with(['records' => function ($q) {
            $q->where('address_province', 'CAVITE')
            ->where('address_city', 'GENERAL TRIAS')
            ->where('gender', 'FEMALE');
        }])
        ->where('status', 'approved')
        ->where('outcomeCondition', 'Active')
        ->where('caseClassification', 'Confirmed')
        ->count();

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
            'count' => Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->where('status', 'approved')
            ->where('healthStatus', 'Asymptomatic')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count(),
        ]);
        
        array_push($arr, [
            'status' => 'MILD',
            'count' => Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->where('status', 'approved')
            ->where('healthStatus', 'Mild')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count(),
        ]);

        array_push($arr, [
            'status' => 'MODERATE',
            'count' => Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->where('status', 'approved')
            ->where('healthStatus', 'Moderate')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count(),
        ]);
        
        array_push($arr, [
            'status' => 'SEVERE',
            'count' => Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->where('status', 'approved')
            ->where('healthStatus', 'Severe')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count(),
        ]);

        array_push($arr, [
            'status' => 'CRITICAL',
            'count' => Forms::with(['records' => function ($q) {
                $q->where('address_province', 'CAVITE')
                ->where('address_city', 'GENERAL TRIAS');
            }])
            ->where('status', 'approved')
            ->where('healthStatus', 'Critical')
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')->count(),
        ]);

        return response()->json($arr);
    }
}
