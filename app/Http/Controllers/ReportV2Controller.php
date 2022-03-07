<?php

namespace App\Http\Controllers;

use App\Models\Brgy;
use App\Models\Forms;
use Carbon\CarbonPeriod;
use App\Models\DailyCases;
use App\Models\ExposureHistory;
use App\Models\SecondaryTertiaryRecords;

class ReportV2Controller extends Controller
{
    public function viewDashboard() {
        if(request()->input('getOption')) {
            $opt = request()->input('getOption');

            if(auth()->user()->isCesuAccount()) {
                $initial_query = Forms::whereHas('records', function ($q) {
                    $q->where('records.address_province', 'CAVITE')
                    ->where('records.address_city', 'GENERAL TRIAS');
                });
            }
            else {
                $initial_query = Forms::whereHas('records', function ($q) {
                    $q->where('records.address_province', auth()->user()->brgy->city->province->provinceName)
                    ->where('records.address_city', auth()->user()->brgy->city->cityName)
                    ->where('records.address_brgy', auth()->user()->brgy->brgyName);
                });
            }
            
            if($opt == 1) {
                $opt_final_query = $initial_query
                ->where('status', 'approved')
                ->whereDate('morbidityMonth', date('Y-m-d'))
                ->whereBetween('dateReported', [date('Y-m-d', strtotime('-2 Days')), date('Y-m-d')])
                ->where('outcomeCondition', 'Active')
                ->where('caseClassification', 'Confirmed')
                ->where('reinfected', 0);

                $getListName = 'List of Newly Reported Active Cases';
            }
            else if($opt == 2) {
                $opt_final_query = $initial_query
                ->where('status', 'approved')
                ->whereDate('morbidityMonth', date('Y-m-d'))
                ->whereDate('dateReported', '<=', date('Y-m-d', strtotime('-3 Days')))
                ->where('outcomeCondition', 'Active')
                ->where('caseClassification', 'Confirmed')
                ->where('reinfected', 0);

                $getListName = 'List of Late Reported Active Cases';
            }
            else if($opt == 3) {
                $opt_final_query = $initial_query
                ->where('status', 'approved')
                ->where('outcomeCondition', 'Recovered')
                ->where('reinfected', 0)
                ->where(function ($q) {
                    $q->where(function ($r) {
                        $r->whereBetween('morbidityMonth', [date('Y-m-d', strtotime('-10 Days')), date('Y-m-d')])
                        ->whereDate('outcomeRecovDate', date('Y-m-d'))
                        ->where('dispoType', '!=', 6);
                    })
                    ->orWhere(function ($s) {
                        $s->whereDate('outcomeRecovDate', date('Y-m-d'))
                        ->where('outcomeCondition', 'Recovered')
                        ->where('dispoType', 6);
                    });
                });

                $getListName = 'List of Newly Reported Recovered Cases';
            }
            else if($opt == 4) {
                $opt_final_query = $initial_query
                ->where('status', 'approved')
                ->whereDate('morbidityMonth', '<', date('Y-m-d', strtotime('-10 Days')))
                ->whereDate('outcomeRecovDate', date('Y-m-d'))
                ->where('outcomeCondition', 'Recovered')
                ->where('reinfected', 0)
                ->where('dispoType', '!=', 6);

                $getListName = 'List of Late Reported Recovered Cases';
            }
            else if($opt == 5) {
                $opt_final_query = $initial_query
                ->where(function ($q) {
                    $q->where('status', 'approved')
                    ->whereDate('outcomeDeathDate', date('Y-m-d'))
                    ->where('outcomeCondition', 'Died');
                })->orWhere(function ($q) {
                    $q->where('status', 'approved')
                    ->whereDate('morbidityMonth', date('Y-m-d'))
                    ->where('outcomeCondition', 'Died');
                });

                $getListName = 'List of Newly Reported Death Cases';
            }
            else if($opt == 6) {
                $opt_final_query = $initial_query
                ->where('status', 'approved')
                ->where('caseClassification', 'Confirmed')
                ->where('outcomeCondition', 'Active')
                ->where('reinfected', 0)
                ->whereDate('morbidityMonth', '<=', date('Y-m-d'));

                $getListName = 'List of Total Active Cases';
            }
            else if ($opt == 7) {
                $opt_final_query = $initial_query
                ->where('status', 'approved')
                ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
                ->where(function ($q) {
                    $q->where(function ($r) {
                        $r->where('outcomeCondition', 'Recovered')
                        ->where('reinfected', 0);
                    })
                    ->orWhere(function ($s) {
                        $s->where('reinfected', 1);
                    });
                });

                $getListName = 'List of Total Recoveries';
            }
            else if($opt == 8) {
                $opt_final_query = $initial_query
                ->where('status', 'approved')
                ->where('outcomeCondition', 'Died')
                ->whereDate('morbidityMonth', '<=', date('Y-m-d'));

                $getListName = 'List of Total Deaths';
            }
            else if($opt == 9) {
                $opt_final_query = $initial_query
                ->where('dispoType', 6)
                ->where('status', 'approved')
                ->where('caseClassification', 'Confirmed')
                ->where('outcomeCondition', 'Active')
                ->whereDate('morbidityMonth', '<=', date('Y-m-d'));

                $getListName = 'List of Patients Admitted in General Trias Ligtas COVID-19 Facility #2 (Gen. Trias Sports Park, Brgy. Santiago)';
            }
            else if($opt == 10) {
                $opt_final_query = $initial_query
                ->where('dispoType', 3)
                ->where('status', 'approved')
                ->where('caseClassification', 'Confirmed')
                ->where('outcomeCondition', 'Active')
                ->where('reinfected', 0)
                ->whereDate('morbidityMonth', '<=', date('Y-m-d'));

                $getListName = 'List of Patients On Strict Home Quarantine';
            }
            else if($opt == 11) {
                $opt_final_query = $initial_query
                ->whereIn('dispoType', [1,2,5])
                ->where('status', 'approved')
                ->where('caseClassification', 'Confirmed')
                ->where('outcomeCondition', 'Active')
                ->where('reinfected', 0)
                ->whereDate('morbidityMonth', '<=', date('Y-m-d'));

                $getListName = 'List of Patients Admitted in the Hospital/Other Isolation Facility';
            }
            else if($opt == 12) {
                $opt_final_query = $initial_query
                ->where('dispoType', 7)
                ->where('status', 'approved')
                ->where('caseClassification', 'Confirmed')
                ->where('outcomeCondition', 'Active')
                ->whereDate('morbidityMonth', '<=', date('Y-m-d'));

                $getListName = 'List of Patients Admitted in General Trias Ligtas COVID-19 Facility #2 (Eagle Ridge, Brgy. Javalera)';
            }
            else {
                return abort(401);
            }

            function yielder($q) {
                foreach($q->cursor() as $data) {
                    yield $data;
                }
            }

            $getList = yielder($opt_final_query);
            $getList_count = $opt_final_query->count();

            return view('reportv2_dashboard', [
                'list' => $getList,
                'list_count' => $getList_count,
                'list_name' => $getListName,
            ]);
        }
        else {
            return abort(401);
        }
    }

    public function viewCtReport() {
        $arr = [];

        $brgy = Brgy::where('city_id', 1)
        ->where('displayInList', 1)
        ->orderBy('brgyName', 'ASC')
        ->get();

        /*
        $currentCTCount = Forms::whereHas('records', function ($q) {
                $q->where('records.address_province', 'CAVITE')
                ->where('records.address_city', 'GENERAL TRIAS');
            })
            ->where('status', 'approved')
            ->whereDate('morbidityMonth', $date->format('Y-m-d'))
            ->where('outcomeCondition', 'Active')
            ->whereIn('caseClassification', ['Suspect', 'Probable'])
            ->where('reinfected', 0)
            ->count();
        */

        $primaryCount = Forms::where('status', 'approved')
        ->where('outcomeCondition', 'Active')
        ->where('reinfected', 0)
        ->where('pType', 'CLOSE CONTACT')
        ->where('ccType', 1);

        /*
        $ct_primary_count = ExposureHistory::where('is_primarycc', 1)
        ->where('is_primarycc_date_set', [date('Y-m-01 00:00:00'), date('Y-m-d 13:00:00')])
        ->count();
        */

        $secondaryCount = Forms::where('status', 'approved')
        ->where('outcomeCondition', 'Active')
        ->where('reinfected', 0)
        ->where('pType', 'CLOSE CONTACT')
        ->where('ccType', 2);

        /*
        $ct_secondary_count = ExposureHistory::where('is_secondarycc', 1)
        ->where('is_secondarycc_date_set', [date('Y-m-01 00:00:00'), date('Y-m-d 13:00:00')])
        ->count();
        */

        $secondarycc_count = SecondaryTertiaryRecords::where('is_secondarycc', 1)
        ->whereBetween('is_secondarycc_date_set', [date('Y-m-01 00:00:00'), date('Y-m-d 13:00:00')])
        ->count();

        $tertiaryCount = Forms::where('status', 'approved')
        ->where('outcomeCondition', 'Active')
        ->where('reinfected', 0)
        ->where('pType', 'CLOSE CONTACT')
        ->where('ccType', 3);

        /*
        $ct_tertiary_count = ExposureHistory::where('is_tertiarycc', 1)
        ->whereBetween('is_tertiarycc_date_set', [date('Y-m-01 00:00:00'), date('Y-m-d 13:00:00')])
        ->count();
        */

        $tertiarycc_count = SecondaryTertiaryRecords::where('is_tertiarycc', 1)
        ->whereBetween('is_tertiarycc_date', [date('Y-m-01 00:00:00'), date('Y-m-d 13:00:00')])
        ->count();

        $suspectedCount = Forms::where('status', 'approved')
        ->where('ptype', '!=', 'CLOSE CONTACT')
        ->where('outcomeCondition', 'Active')
        ->where('reinfected', 0)
        ->where('caseClassification', 'Suspect');

        $probableCount = Forms::where('status', 'approved')
        ->where('ptype', '!=', 'CLOSE CONTACT')
        ->where('outcomeCondition', 'Active')
        ->where('reinfected', 0)
        ->where('caseClassification', 'Probable');

        $grandTotalContactTraced =
        ((clone $primaryCount)->whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })->whereBetween('morbidityMonth', [date('Y-m-01'), date('Y-m-d')])->count() +
        (clone $secondaryCount)->whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })->whereBetween('morbidityMonth', [date('Y-m-01'), date('Y-m-d')])->count() +
        (clone $tertiaryCount)->whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })->whereBetween('morbidityMonth', [date('Y-m-01'), date('Y-m-d')])->count() +
        (clone $suspectedCount)->whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })->whereBetween('morbidityMonth', [date('Y-m-01'), date('Y-m-d')])->count() +
        (clone $probableCount)->whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })->whereBetween('morbidityMonth', [date('Y-m-01'), date('Y-m-d')])->count()) +
        $secondarycc_count + 
        $tertiarycc_count;

        if(date('H') >= 13) {
            //idagdag ang bilang ng confirmed cases yesterday
            $total_active_yesterday_1pm = DailyCases::whereDate('set_date', date('Y-m-d', strtotime('-1 Day')))
            ->where('type', '1PM')
            ->pluck('total_active')
            ->first();

            $total_active_yesterday_4pm = DailyCases::whereDate('set_date', date('Y-m-d', strtotime('-1 Day')))
            ->where('type', '4PM')
            ->pluck('total_active')
            ->first();

            $today_count_query = DailyCases::whereDate('set_date', date('Y-m-d'))
            ->where('type', '1PM')
            ->pluck('total_active')
            ->first();

            $add_yesterday = $total_active_yesterday_4pm - $total_active_yesterday_1pm;
            $activeCasesCount = $today_count_query + $add_yesterday;
        }
        else {
            $activeCasesCount = Forms::whereHas('records', function ($q) {
                $q->where('records.address_province', 'CAVITE')
                ->where('records.address_city', 'GENERAL TRIAS');
            })
            ->where('status', 'approved')
            ->where('caseClassification', 'Confirmed')
            ->where('outcomeCondition', 'Active')
            ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
            ->count();
        }

        foreach($brgy as $b) {
            $primaryCount = Forms::whereHas('records', function ($q) use ($b) {
                $q->where('address_province', $b->city->province->provinceName)
                ->where('address_city', $b->city->cityName)
                ->where('address_brgy', $b->brgyName);
            })
            ->where('status', 'approved')
            ->where('pType', 'CLOSE CONTACT')
            ->where('ccType', 1);

            $secondaryCount = Forms::whereHas('records', function ($q) use ($b) {
                $q->where('address_province', $b->city->province->provinceName)
                ->where('address_city', $b->city->cityName)
                ->where('address_brgy', $b->brgyName);
            })
            ->where('status', 'approved')
            ->where('pType', 'CLOSE CONTACT')
            ->where('ccType', 2);

            $st_secondary_count = SecondaryTertiaryRecords::where('address_province', $b->city->province->provinceName)
            ->where('address_city', $b->city->cityName)
            ->where('address_brgy', $b->brgyName)
            ->where('is_secondarycc', 1);

            $st_secondary_count_yesterday = SecondaryTertiaryRecords::where('address_province', $b->city->province->provinceName)
            ->where('address_city', $b->city->cityName)
            ->where('address_brgy', $b->brgyName)
            ->where('is_secondarycc', 1);

            $tertiaryCount = Forms::whereHas('records', function ($q) use ($b) {
                $q->where('address_province', $b->city->province->provinceName)
                ->where('address_city', $b->city->cityName)
                ->where('address_brgy', $b->brgyName);
            })
            ->where('status', 'approved')
            ->where('pType', 'CLOSE CONTACT')
            ->where('ccType', 3);

            $st_tertiary_count = SecondaryTertiaryRecords::where('address_province', $b->city->province->provinceName)
            ->where('address_city', $b->city->cityName)
            ->where('address_brgy', $b->brgyName)
            ->where('is_tertiarycc', 1);

            $st_tertiary_count_yesterday = SecondaryTertiaryRecords::where('address_province', $b->city->province->provinceName)
            ->where('address_city', $b->city->cityName)
            ->where('address_brgy', $b->brgyName)
            ->where('is_tertiarycc', 1);

            $suspectedCount = Forms::whereHas('records', function ($q) use ($b) {
                $q->where('address_province', $b->city->province->provinceName)
                ->where('address_city', $b->city->cityName)
                ->where('address_brgy', $b->brgyName);
            })
            ->where('status', 'approved')
            ->where('ptype', '!=', 'CLOSE CONTACT')
            ->where('caseClassification', 'Suspect');

            $probableCount = Forms::whereHas('records', function ($q) use ($b) {
                $q->where('address_province', $b->city->province->provinceName)
                ->where('address_city', $b->city->cityName)
                ->where('address_brgy', $b->brgyName);
            })
            ->where('status', 'approved')
            ->where('ptype', '!=', 'CLOSE CONTACT')
            ->where('caseClassification', 'Probable');

            /*
            $ct_primary_count = ExposureHistory::whereHas('form', function ($q) use ($b) {
                $q->whereHas('records', function ($r) use ($b) {
                    $r->where('address_province', $b->city->province->provinceName)
                    ->where('address_city', $b->city->cityName)
                    ->where('address_brgy', $b->brgyName);
                });
            })
            ->where('is_primarycc', 1);

            $ct_secondary_count = ExposureHistory::whereHas('form', function ($q) use ($b) {
                $q->whereHas('records', function ($r) use ($b) {
                    $r->where('address_province', $b->city->province->provinceName)
                    ->where('address_city', $b->city->cityName)
                    ->where('address_brgy', $b->brgyName);
                });
            })
            ->where('is_secondarycc', 1);

            $ct_tertiary_count = ExposureHistory::whereHas('form', function ($q) use ($b) {
                $q->whereHas('records', function ($r) use ($b) {
                    $r->where('address_province', $b->city->province->provinceName)
                    ->where('address_city', $b->city->cityName)
                    ->where('address_brgy', $b->brgyName);
                });
            })
            ->where('is_tertiarycc', 1);
            */

            if(request()->input('getDate')) {
                $primaryCount = $primaryCount->whereDate('morbidityMonth', request()->input('getDate'))
                ->count();

                $secondaryCount = $secondaryCount->whereDate('morbidityMonth', request()->input('getDate'))
                ->count();

                $st_secondary_count = $st_secondary_count->whereBetween('is_secondarycc_date_set', [date('Y-m-d 00:00:00', strtotime(request()->input('getDate'))), date('Y-m-d 13:00:00', strtotime(request()->input('getDate')))]) //13:00 is cutoff as per CT
                ->count();

                $st_secondary_count_yesterday = $st_secondary_count_yesterday->whereBetween('is_secondarycc_date_set', [date('Y-m-d 13:00:00', strtotime(request()->input('getDate').' -1 Day')), date('Y-m-d 23:59:59', strtotime(request()->input('getDate').' -1 Day'))])
                ->count();

                $tertiaryCount = $tertiaryCount->whereDate('morbidityMonth', request()->input('getDate'))
                ->count();

                $st_tertiary_count = $st_tertiary_count->whereBetween('is_tertiarycc_date_set', [date('Y-m-d 00:00:00', strtotime(request()->input('getDate'))), date('Y-m-d 13:00:00', strtotime(request()->input('getDate')))]) //13:00 is cutoff as per CT
                ->count();

                $st_tertiary_count_yesterday = $st_tertiary_count_yesterday->whereBetween('is_tertiarycc_date_set', [date('Y-m-d 13:00:00', strtotime(request()->input('getDate').' -1 Day')), date('Y-m-d 23:59:59', strtotime(request()->input('getDate').' -1 Day'))])
                ->count();

                $suspectedCount = $suspectedCount->whereDate('morbidityMonth', request()->input('getDate'))
                ->count();

                $probableCount = $probableCount->whereDate('morbidityMonth', request()->input('getDate'))
                ->count();

                /*
                $ct_primary_count = $ct_primary_count->whereDate('is_primarycc_date_set', request()->input('getDate'))
                ->count();

                $ct_secondary_count = $ct_primary_count->whereDate('is_secondarycc_date_set', request()->input('getDate'))
                ->count();

                $ct_tertiary_count = $ct_primary_count->whereDate('is_tertiarycc_date_set', request()->input('getDate'))
                ->count();
                */
            }
            else {
                $primaryCount = $primaryCount->whereDate('morbidityMonth', date('Y-m-d'))
                ->count();

                $secondaryCount = $secondaryCount->whereDate('morbidityMonth', date('Y-m-d'))
                ->count();
                
                $st_secondary_count = $st_secondary_count->whereBetween('is_secondarycc_date_set', [date('Y-m-d 00:00:00'), date('Y-m-d 13:00:00')]) //13:00 is cutoff as per CT
                ->count();

                $st_secondary_count_yesterday = $st_secondary_count_yesterday->whereBetween('is_secondarycc_date_set', [date('Y-m-d 13:00:00', strtotime('yesterday')), date('Y-m-d 23:59:59')])
                ->count();

                $tertiaryCount = $tertiaryCount->whereDate('morbidityMonth', date('Y-m-d'))
                ->count();

                $st_tertiary_count = $st_tertiary_count->whereBetween('is_tertiarycc_date_set', [date('Y-m-d 00:00:00'), date('Y-m-d 13:00:00')]) //13:00 is cutoff as per CT
                ->count();

                $st_tertiary_count_yesterday = $st_tertiary_count_yesterday->whereBetween('is_tertiarycc_date_set', [date('Y-m-d 13:00:00', strtotime('yesterday')), date('Y-m-d 23:59:59')])
                ->count();

                $suspectedCount = $suspectedCount->whereDate('morbidityMonth', date('Y-m-d'))
                ->count();

                $probableCount = $probableCount->whereDate('morbidityMonth', date('Y-m-d'))
                ->count();

                /*
                $ct_primary_count = $ct_primary_count->whereDate('is_primarycc_date_set', date('Y-m-d'))
                ->count();

                $ct_secondary_count = $ct_secondary_count->whereDate('is_secondarycc_date_set', date('Y-m-d'))
                ->count();

                $ct_tertiary_count = $ct_tertiary_count->whereDate('is_tertiarycc_date_set', date('Y-m-d'))
                ->count();
                */
            }

            array_push ($arr, [
                'brgyName' => $b->brgyName,
                'primaryCount' => $primaryCount,
                'secondaryCount' => $secondaryCount + $st_secondary_count + $st_secondary_count_yesterday,
                'tertiaryCount' => $tertiaryCount + $st_tertiary_count + $st_tertiary_count_yesterday,
                'suspectedCount' => $suspectedCount,
                'probableCount' => $probableCount,
            ]);
        }

        //Array Push Other Cities
        $primaryCount = Forms::where(function ($q) {
            $q->whereHas('records', function ($r) {
                $r->where('address_province', '!=', 'CAVITE');
            })
            ->orWhereHas('records', function ($r) {
                $r->where('address_province', 'CAVITE')
                ->where('address_city', '!=', 'GENERAL TRIAS');
            });
        })
        ->where('status', 'approved')
        ->where('pType', 'CLOSE CONTACT')
        ->where('ccType', 1);

        $secondaryCount = Forms::where(function ($q) {
            $q->whereHas('records', function ($r) {
                $r->where('address_province', '!=', 'CAVITE');
            })
            ->orWhereHas('records', function ($r) {
                $r->where('address_province', 'CAVITE')
                ->where('address_city', '!=', 'GENERAL TRIAS');
            });
        })
        ->where('status', 'approved')
        ->where('pType', 'CLOSE CONTACT')
        ->where('ccType', 2);

        $st_secondary_count = SecondaryTertiaryRecords::where(function ($q) {
            $q->where('address_province', '!=', 'CAVITE')
            ->orWhere(function ($r) {
                $r->where('address_province', 'CAVITE')
                ->where('address_city', '!=', 'GENERAL TRIAS');
            });
        })
        ->where('is_secondarycc', 1);

        $st_secondary_count_yesterday = SecondaryTertiaryRecords::where(function ($q) {
            $q->where('address_province', '!=', 'CAVITE')
            ->orWhere(function ($r) {
                $r->where('address_province', 'CAVITE')
                ->where('address_city', '!=', 'GENERAL TRIAS');
            });
        })
        ->where('is_secondarycc', 1);

        $tertiaryCount = Forms::where(function ($q) {
            $q->whereHas('records', function ($r) {
                $r->where('address_province', '!=', 'CAVITE');
            })
            ->orWhereHas('records', function ($r) {
                $r->where('address_province', 'CAVITE')
                ->where('address_city', '!=', 'GENERAL TRIAS');
            });
        })
        ->where('status', 'approved')
        ->where('pType', 'CLOSE CONTACT')
        ->where('ccType', 3);

        $st_tertiary_count = SecondaryTertiaryRecords::where(function ($q) {
            $q->where('address_province', '!=', 'CAVITE')
            ->orWhere(function ($r) {
                $r->where('address_province', 'CAVITE')
                ->where('address_city', '!=', 'GENERAL TRIAS');
            });
        })
        ->where('is_tertiarycc', 1);

        $st_tertiary_count_yesterday = SecondaryTertiaryRecords::where(function ($q) {
            $q->where('address_province', '!=', 'CAVITE')
            ->orWhere(function ($r) {
                $r->where('address_province', 'CAVITE')
                ->where('address_city', '!=', 'GENERAL TRIAS');
            });
        })
        ->where('is_tertiarycc', 1);

        $suspectedCount = Forms::where(function ($q) {
            $q->whereHas('records', function ($r) {
                $r->where('address_province', '!=', 'CAVITE');
            })
            ->orWhereHas('records', function ($r) {
                $r->where('address_province', 'CAVITE')
                ->where('address_city', '!=', 'GENERAL TRIAS');
            });
        })
        ->where('status', 'approved')
        ->where('ptype', '!=', 'CLOSE CONTACT')
        ->where('caseClassification', 'Suspect');

        $probableCount = Forms::where(function ($q) {
            $q->whereHas('records', function ($r) {
                $r->where('address_province', '!=', 'CAVITE');
            })
            ->orWhereHas('records', function ($r) {
                $r->where('address_province', 'CAVITE')
                ->where('address_city', '!=', 'GENERAL TRIAS');
            });
        })
        ->where('status', 'approved')
        ->where('ptype', '!=', 'CLOSE CONTACT')
        ->where('caseClassification', 'Probable');

        if(request()->input('getDate')) {
            $primaryCount = $primaryCount->whereDate('morbidityMonth', request()->input('getDate'))
            ->count();

            $secondaryCount = $secondaryCount->whereDate('morbidityMonth', request()->input('getDate'))
            ->count();

            $st_secondary_count = $st_secondary_count->whereBetween('is_secondarycc_date_set', [date('Y-m-d 00:00:00', strtotime(request()->input('getDate'))), date('Y-m-d 13:00:00', strtotime(request()->input('getDate')))]) //13:00 is cutoff as per CT
            ->count();

            $st_secondary_count_yesterday = $st_secondary_count_yesterday->whereBetween('is_secondarycc_date_set', [date('Y-m-d 13:00:00', strtotime(request()->input('getDate').' -1 Day')), date('Y-m-d 23:59:59', strtotime(request()->input('getDate').' -1 Day'))])
            ->count();

            $tertiaryCount = $tertiaryCount->whereDate('morbidityMonth', request()->input('getDate'))
            ->count();

            $st_tertiary_count = $st_tertiary_count->whereBetween('is_tertiarycc_date_set', [date('Y-m-d 00:00:00', strtotime(request()->input('getDate'))), date('Y-m-d 13:00:00', strtotime(request()->input('getDate')))]) //13:00 is cutoff as per CT
            ->count();

            $st_tertiary_count_yesterday = $st_tertiary_count_yesterday->whereBetween('is_tertiarycc_date_set', [date('Y-m-d 13:00:00', strtotime(request()->input('getDate').' -1 Day')), date('Y-m-d 23:59:59', strtotime(request()->input('getDate').' -1 Day'))])
            ->count();

            $suspectedCount = $suspectedCount->whereDate('morbidityMonth', request()->input('getDate'))
            ->count();

            $probableCount = $probableCount->whereDate('morbidityMonth', request()->input('getDate'))
            ->count();
        }
        else {
            $primaryCount = $primaryCount->whereDate('morbidityMonth', date('Y-m-d'))
            ->count();

            $secondaryCount = $secondaryCount->whereDate('morbidityMonth', date('Y-m-d'))
            ->count();
            
            $st_secondary_count = $st_secondary_count->whereBetween('is_secondarycc_date_set', [date('Y-m-d 00:00:00'), date('Y-m-d 13:00:00')]) //13:00 is cutoff as per CT
            ->count();

            $st_secondary_count_yesterday = $st_secondary_count_yesterday->whereBetween('is_secondarycc_date_set', [date('Y-m-d 13:00:00', strtotime('yesterday')), date('Y-m-d 23:59:59')])
            ->count();

            $tertiaryCount = $tertiaryCount->whereDate('morbidityMonth', date('Y-m-d'))
            ->count();

            $st_tertiary_count = $st_tertiary_count->whereBetween('is_tertiarycc_date_set', [date('Y-m-d 00:00:00'), date('Y-m-d 13:00:00')]) //13:00 is cutoff as per CT
            ->count();

            $st_tertiary_count_yesterday = $st_tertiary_count_yesterday->whereBetween('is_tertiarycc_date_set', [date('Y-m-d 13:00:00', strtotime('yesterday')), date('Y-m-d 23:59:59')])
            ->count();

            $suspectedCount = $suspectedCount->whereDate('morbidityMonth', date('Y-m-d'))
            ->count();

            $probableCount = $probableCount->whereDate('morbidityMonth', date('Y-m-d'))
            ->count();
        }

        array_push ($arr, [
            'brgyName' => 'OTHER CITIES',
            'primaryCount' => $primaryCount,
            'secondaryCount' => $secondaryCount + $st_secondary_count + $st_secondary_count_yesterday,
            'tertiaryCount' => $tertiaryCount + $st_tertiary_count + $st_tertiary_count_yesterday,
            'suspectedCount' => $suspectedCount,
            'probableCount' => $probableCount,
        ]);

        $period = CarbonPeriod::create(date('Y-m-01'), date('Y-m-d'));
        $arr_summary = [];

        foreach($period as $date) {
            $currentActiveCount = Forms::whereHas('records', function ($q) {
                $q->where('records.address_province', 'CAVITE')
                ->where('records.address_city', 'GENERAL TRIAS');
            })
            ->where('status', 'approved')
            ->whereDate('morbidityMonth', $date->format('Y-m-d'))
            ->where('outcomeCondition', 'Active')
            ->where('caseClassification', 'Confirmed')
            ->where('reinfected', 0)
            ->count();

            $currentCT_query = Forms::whereHas('records', function ($q) {
                $q->where('records.address_province', 'CAVITE')
                ->where('records.address_city', 'GENERAL TRIAS');
            })
            ->where('status', 'approved')
            ->whereDate('morbidityMonth', $date->format('Y-m-d'))
            ->where('outcomeCondition', 'Active')
            ->whereIn('caseClassification', ['Suspect', 'Probable', 'Non-COVID-19 Case'])
            ->where('reinfected', 0);

            $additional_counter = 0;

            if((clone $currentCT_query)->whereNotNull('ccid_list')->count() != 0) {
                foreach((clone $currentCT_query)->whereNotNull('ccid_list')->pluck('ccid_list') as $data) {
                    $additional_counter += count(explode(",", $data));
                }
            }

            $st_count = SecondaryTertiaryRecords::where('morbidityMonth', $date->format('Y-m-d'))
            ->count();

            $additional_counter += $st_count;

            array_push($arr_summary, [
                'date' => $date->format('Y-m-d'),
                'numActive' => $currentActiveCount,
                'numCT' => $currentCT_query->count() + $additional_counter,
            ]);
        }

        return view('report_ct', [
            'list' => $arr,
            'totalPrimary' => 0,
            'totalSecondary' => 0,
            'totalTertiary' => 0,
            'totalSuspected' => 0,
            'totalProbable' => 0,
            'activeCasesCount' => $activeCasesCount,
            'grandTotalContactTraced' => $grandTotalContactTraced,
            'arr_summary' => $arr_summary,
        ]);
    }

    public function cmIndex() {
        if(date('d') != 15 && date('d') != date('t')) {
            return redirect()->route('home')
            ->with('status', 'Warning: You can ONLY open Composite Measure Report on '.date('F').' 15 and '.date('F t').'.')
            ->with('statustype', 'warning');
        }

        $sdate = date('Y-m-01');

        if(date('d') <= 15) { 
            $edate = date('Y-m-15');
        }
        else if(date('d') >= 16) {
            $edate = date('Y-m-t');
        }

        $cc_count_total = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', 'CLOSE CONTACT')
        ->count();

        $cc_count_hq = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', 'CLOSE CONTACT')
        ->where('dispoType', 3)
        ->count();

        $cc_count_ttmf = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', 'CLOSE CONTACT')
        ->whereIn('dispoType', [6,7,2,5])
        ->count();

        $cc_count_hospital = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', 'CLOSE CONTACT')
        ->where('dispoType', 1)
        ->count();

        $probable_count_total = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', '!=','CLOSE CONTACT')
        ->where('caseClassification', 'Probable')
        ->count();

        $probable_count_hq = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', '!=','CLOSE CONTACT')
        ->where('caseClassification', 'Probable')
        ->where('dispoType', 3)
        ->count();

        $probable_count_ttmf = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', '!=','CLOSE CONTACT')
        ->where('caseClassification', 'Probable')
        ->whereIn('dispoType', [6,7,2,5])
        ->count();

        $probable_count_hospital = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', '!=','CLOSE CONTACT')
        ->where('caseClassification', 'Probable')
        ->where('dispoType', 1)
        ->count();

        $suspected_count_total = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', '!=','CLOSE CONTACT')
        ->where('caseClassification', 'Suspect')
        ->count();

        $suspected_count_hq = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', '!=','CLOSE CONTACT')
        ->where('caseClassification', 'Suspect')
        ->where('dispoType', 3)
        ->count();

        $suspected_count_ttmf = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', '!=','CLOSE CONTACT')
        ->where('caseClassification', 'Suspect')
        ->whereIn('dispoType', [6,7,2,5])
        ->count();

        $suspected_count_hospital = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereBetween('morbidityMonth', [$sdate, $edate])
        ->where('pType', '!=','CLOSE CONTACT')
        ->where('caseClassification', 'Suspect')
        ->where('dispoType', 1)
        ->count();

        $activecases_count_total = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->count();

        $activecases_count_hq = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('dispoType', 3)
        ->count();

        $activecases_count_ttmf = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->whereIn('dispoType', [6,7,2,5])
        ->count();

        $activecases_count_hospital = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('dispoType', 1)
        ->count();

        $activecases_count_asymptomatic_total = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Asymptomatic')
        ->count();
        
        $activecases_count_asymptomatic_hq = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Asymptomatic')
        ->where('dispoType', 3)
        ->count();

        $activecases_count_asymptomatic_ttmf = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Asymptomatic')
        ->whereIn('dispoType', [6,7,2,5])
        ->count();

        $activecases_count_asymptomatic_hospital = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Asymptomatic')
        ->where('dispoType', 1)
        ->count();

        $activecases_count_mild_nocomorbid_total = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Mild')
        ->where('COMO', 'None')
        ->count();

        $activecases_count_mild_nocomorbid_hq = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Mild')
        ->where('COMO', 'None')
        ->where('dispoType', 3)
        ->count();

        $activecases_count_mild_nocomorbid_ttmf = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Mild')
        ->where('COMO', 'None')
        ->whereIn('dispoType', [6,7,2,5])
        ->count();

        $activecases_count_mild_nocomorbid_hospital = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Mild')
        ->where('COMO', 'None')
        ->where('dispoType', 1)
        ->count();

        $activecases_count_mild_withcomorbid_total = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Mild')
        ->where('COMO', '!=', 'None')
        ->count();

        $activecases_count_mild_withcomorbid_hq = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Mild')
        ->where('COMO', '!=', 'None')
        ->where('dispoType', 3)
        ->count();

        $activecases_count_mild_withcomorbid_ttmf = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Mild')
        ->where('COMO', '!=', 'None')
        ->whereIn('dispoType', [6,7,2,5])
        ->count();

        $activecases_count_mild_withcomorbid_hospital = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Mild')
        ->where('COMO', '!=', 'None')
        ->where('dispoType', 1)
        ->count();

        $activecases_count_moderate_total = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Moderate')
        ->count();

        $activecases_count_moderate_hq = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Moderate')
        ->where('dispoType', 3)
        ->count();

        $activecases_count_moderate_ttmf = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Moderate')
        ->whereIn('dispoType', [6,7,2,5])
        ->count();

        $activecases_count_moderate_hospital = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Moderate')
        ->where('dispoType', 1)
        ->count();

        $activecases_count_severe_total = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Severe')
        ->count();

        $activecases_count_severe_hq = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Severe')
        ->where('dispoType', 3)
        ->count();

        $activecases_count_severe_ttmf = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Severe')
        ->whereIn('dispoType', [6,7,2,5])
        ->count();

        $activecases_count_severe_hospital = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Severe')
        ->where('dispoType', 1)
        ->count();

        $activecases_count_critical_total = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Critical')
        ->count();

        $activecases_count_critical_hq = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Critical')
        ->where('dispoType', 3)
        ->count();

        $activecases_count_critical_ttmf = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Critical')
        ->whereIn('dispoType', [6,7,2,5])
        ->count();

        $activecases_count_critical_hospital = Forms::whereHas('records', function ($q) {
            $q->where('records.address_province', 'CAVITE')
            ->where('records.address_city', 'GENERAL TRIAS');
        })
        ->where('status', 'approved')
        ->whereDate('morbidityMonth', '<=', date('Y-m-d'))
        ->where('caseClassification', 'Confirmed')
        ->where('outcomeCondition', 'Active')
        ->where('healthStatus', 'Critical')
        ->where('dispoType', 1)
        ->count();

        return view('cm_index', [
            'cc_count_total' => $cc_count_total,
            'cc_count_hq' => $cc_count_hq,
            'cc_count_ttmf' => $cc_count_ttmf,
            'cc_count_hospital' => $cc_count_hospital,
            'probable_count_total' => $probable_count_total,
            'probable_count_hq' => $probable_count_hq,
            'probable_count_ttmf' => $probable_count_ttmf,
            'probable_count_hospital' => $probable_count_hospital,
            'suspected_count_total' => $suspected_count_total,
            'suspected_count_hq' => $suspected_count_hq,
            'suspected_count_ttmf' => $suspected_count_ttmf,
            'suspected_count_hospital' => $suspected_count_hospital,
            'activecases_count_total' => $activecases_count_total,
            'activecases_count_hq' => $activecases_count_hq,
            'activecases_count_ttmf' => $activecases_count_ttmf,
            'activecases_count_hospital' => $activecases_count_hospital,
            'activecases_count_asymptomatic_total' => $activecases_count_asymptomatic_total,
            'activecases_count_asymptomatic_hq' => $activecases_count_asymptomatic_hq,
            'activecases_count_asymptomatic_ttmf' => $activecases_count_asymptomatic_ttmf,
            'activecases_count_asymptomatic_hospital' => $activecases_count_asymptomatic_hospital,
            'activecases_count_mild_nocomorbid_total' => $activecases_count_mild_nocomorbid_total,
            'activecases_count_mild_nocomorbid_hq' => $activecases_count_mild_nocomorbid_hq,
            'activecases_count_mild_nocomorbid_ttmf' => $activecases_count_mild_nocomorbid_ttmf,
            'activecases_count_mild_nocomorbid_hospital' => $activecases_count_mild_nocomorbid_hospital,
            'activecases_count_mild_withcomorbid_total' => $activecases_count_mild_withcomorbid_total,
            'activecases_count_mild_withcomorbid_hq' => $activecases_count_mild_withcomorbid_hq,
            'activecases_count_mild_withcomorbid_ttmf' => $activecases_count_mild_withcomorbid_ttmf,
            'activecases_count_mild_withcomorbid_hospital' => $activecases_count_mild_withcomorbid_hospital,
            'activecases_count_moderate_total' => $activecases_count_moderate_total,
            'activecases_count_moderate_hq' => $activecases_count_moderate_hq,
            'activecases_count_moderate_ttmf' => $activecases_count_moderate_ttmf,
            'activecases_count_moderate_hospital' => $activecases_count_moderate_hospital,
            'activecases_count_severe_total' => $activecases_count_severe_total,
            'activecases_count_severe_hq' => $activecases_count_severe_hq,
            'activecases_count_severe_ttmf' => $activecases_count_severe_ttmf,
            'activecases_count_severe_hospital' => $activecases_count_severe_hospital,
            'activecases_count_critical_total' => $activecases_count_critical_total,
            'activecases_count_critical_hq' => $activecases_count_critical_hq,
            'activecases_count_critical_ttmf' => $activecases_count_critical_ttmf,
            'activecases_count_critical_hospital' => $activecases_count_critical_hospital,
        ]);
    }

    public function clustering_index() {
        //get Brgy List
        $brgy = Brgy::where('city_id', 1)
        ->where('displayInList', 1)
        ->orderBy('brgyName', 'ASC')
        ->get();

        return view('report_clustering_index', [
            'list' => $brgy,
        ]);
    }

    public function encodingCalendar() {
        ini_set('max_execution_time', 600);
        $brgy = Brgy::where('city_id', 1)
        ->where('displayInList', 1)
        ->orderBy('brgyName', 'ASC')
        ->get();

        $period = CarbonPeriod::create(date('Y-m-01'), date('Y-m-d'));

        /*
        $arr = [];
        foreach($brgy as $b) {
            foreach($period as $d) {
                $sus_count = Forms::with('records')
                ->whereHas('records', function ($q) use ($d) {
                    $q->where('records.address_province', 'CAVITE')
                    ->where('records.address_city', 'GENERAL TRIAS')
                    ->where('records.address_brgy', $d->brgyName);
                })
                ->whereDate('morbidityMonth', $d->format('Y-m-d'))
                ->where('status', 'approved')
                ->where('caseClassification', 'Suspect')
                ->where('outcomeCondition', 'Active')
                ->count();

                $pro_count = Forms::with('records')
                ->whereHas('records', function ($q) use ($d) {
                    $q->where('records.address_province', 'CAVITE')
                    ->where('records.address_city', 'GENERAL TRIAS')
                    ->where('records.address_brgy', $d->brgyName);
                })
                ->whereDate('morbidityMonth', $d->format('Y-m-d'))
                ->where('status', 'approved')
                ->where('caseClassification', 'Probable')
                ->where('outcomeCondition', 'Active')
                ->count();

                array_push($arr, [
                    'brgy' => $b->brgyName,
                    'forDate' => $d->format('Y-m-d'),
                    'sus_count' => $sus_count,
                    'pro_count' => $pro_count,
                ]);
            }
        }
        */

        return view('encodingcalendar', [
            'brgy' => $brgy,
            'period' => $period,
        ]);
    }
}
