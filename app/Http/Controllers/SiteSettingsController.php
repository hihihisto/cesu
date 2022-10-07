<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    public function index() {
        return view('site_settings');
    }

    public function update(Request $request) {
        $d = SiteSettings::updateOrCreate(['id' => 1], [
            'paswab_enabled' => $request->paswab_enabled,
            'paswab_antigen_enabled' => $request->paswab_antigen_enabled,
            'paswab_message_en' => $request->paswab_message_en,
            'paswab_message_fil' => $request->paswab_message_fil,
            'oniStartTime_pm' => $request->oniStartTime_pm,
            'oniStartTime_am' => $request->oniStartTime_am,
            'lockencode_enabled' => $request->lockencode_enabled,
            'lockencode_start_time' => $request->lockencode_start_time,
            'lockencode_end_time' => $request->lockencode_end_time,
            'lockencode_positive_enabled' => $request->lockencode_positive_enabled,
            'lockencode_positive_start_time' => $request->lockencode_positive_start_time,
            'lockencode_positive_end_time' => $request->lockencode_positive_end_time,
            'encodeActiveCasesCutoff' => $request->encodeActiveCasesCutoff,
            'listMobiles' => $request->listMobiles,
            'listTelephone' => $request->listTelephone,
            'listEmail' => $request->listEmail,
            'listLinkNames' => $request->listLinkNames,
            'listLinkURL' => $request->listLinkURL,
            'dilgCustomRespondentName' => $request->dilgCustomRespondentName,
            'dilgCustomOfficeName' => $request->dilgCustomOfficeName,
            'unvaccinated_days_of_recovery' => $request->unvaccinated_days_of_recovery,
            'partialvaccinated_days_of_recovery' => $request->partialvaccinated_days_of_recovery,
            'fullyvaccinated_days_of_recovery' => $request->fullyvaccinated_days_of_recovery,
            'booster_days_of_recovery' => $request->booster_days_of_recovery,
            'in_hospital_days_of_recovery' => $request->in_hospital_days_of_recovery,
            'severe_days_of_recovery' => $request->severe_days_of_recovery,
            'paswab_auto_schedule_if_symptomatic' => $request->paswab_auto_schedule_if_symptomatic,
            'cifpage_auto_schedule_if_symptomatic' => $request->cifpage_auto_schedule_if_symptomatic,
            'system_type' => $request->system_type,
            'default_dru_name' => $request->default_dru_name,
            'default_dru_region' => $request->default_dru_region,
            'default_dru_region_json' => $request->default_dru_region_json,
            'default_dru_province' => $request->default_dru_province_json,
            'default_dru_citymun' => $request->default_dru_citymun,
            'default_dru_citymun_json' => $request->default_dru_citymun_json,

        ]);

        return back()
        ->withInput()
        ->with('msg', 'Encoding Error: The Address Street of the Patient is Invalid. Please check and edit the Patient Address first and try again.')
        ->with('msgType', 'warning');
    }
}
