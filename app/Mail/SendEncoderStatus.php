<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Forms;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\AbtcBakunaRecords;
use App\Models\SyndromicRecords;
use App\Models\VaxcertConcern;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendEncoderStatus extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $list = User::where('encoder_stats_visible', 1)
        ->where('enabled', 1)
        ->get();
        
        $arr = [];
        foreach($list as $item) {
            /*
            $suspected_count = Forms::where(function ($q) use ($item) {
                $q->where('user_id', $item->id)
                ->orWhere('updated_by', $item->id);
            })
            ->where(function ($q) {
                $q->whereDate('created_at', date('Y-m-d'))
                ->orWhereDate('updated_at', date('Y-m-d'));
            })
            ->whereIn('caseClassification', ['Suspect', 'Probable'])
            ->count();
            */

            /*
            $confirmed_count = Forms::where(function ($q) use ($item) {
                $q->where(function ($r) use ($item) {
                    $r->where('user_id', $item->id)
                    ->where('updated_by', '!=', $item->id);
                })
                ->orWhere(function ($s) use ($item) {
                    $s->where('user_id', '!=', $item->id)
                    ->where('updated_by', $item->id);
                })
                ->orWhere(function ($t) use ($item) {
                    $t->where('user_id', $item->id)
                    ->where('updated_by', $item->id);
                });
            })
            ->whereDate('morbidityMonth', date('Y-m-d'))
            ->where('caseClassification', 'Confirmed')
            ->where('outcomeCondition', 'Active')
            ->count();
            */

            /*
            $confirmed_count = Forms::where(function ($q) use ($item) {
                $q->where('user_id', $item->id)
                ->orWhere('updated_by', $item->id);
            })
            ->where(function ($q) {
                $q->whereDate('created_at', date('Y-m-d'))
                ->orWhereDate('updated_at', date('Y-m-d'));
            })
            ->whereDate('morbidityMonth', date('Y-m-d'))
            ->where('caseClassification', 'Confirmed')
            ->count();
            */

            $suspected_count = Forms::where('user_id', $item->id)
            ->where(function($q) {
                $q->whereDate('created_at', date('Y-m-d'));
            })
            ->whereIn('caseClassification', ['Suspect', 'Probable'])
            ->count();

            $confirmed_count = Forms::where(function ($q) use ($item) {
                $q->where('user_id', $item->id)
                ->orWhere('updated_by', $item->id);
            })
            ->whereDate('morbidityMonth', date('Y-m-d'))
            ->where('caseClassification', 'Confirmed')
            ->count();

            $recovered_count = Forms::where(function ($q) use ($item) {
                $q->where(function ($r) use ($item) {
                    $r->where('user_id', $item->id)
                    ->where('updated_by', '!=', $item->id);
                })
                ->orWhere(function ($s) use ($item) {
                    $s->where('user_id', '!=', $item->id)
                    ->where('updated_by', $item->id);
                })
                ->orWhere(function ($t) use ($item) {
                    $t->where('user_id', $item->id)
                    ->where('updated_by', $item->id);
                });
            })
            ->whereDate('morbidityMonth', date('Y-m-d'))
            ->where('caseClassification', 'Confirmed')
            ->where('outcomeCondition', 'Recovered')
            ->count();
            
            $negative_count = Forms::where(function ($q) use ($item) {
                $q->where('user_id', $item->id)
                ->orWhere('updated_by', $item->id);
            })
            ->where(function ($q) {
                $q->whereDate('created_at', date('Y-m-d'))
                ->orWhereDate('updated_at', date('Y-m-d'));
            })
            ->where('caseClassification', 'Non-COVID-19 Case')
            ->count();

            $abtc_count = AbtcBakunaRecords::where('d0_done_by', $item->id)
            ->whereDate('d0_done_date', date('Y-m-d'))
            ->count();

            $abtc_count_ff1 = AbtcBakunaRecords::where('d3_done_by', $item->id)
            ->whereDate('d3_done_date', date('Y-m-d'))
            ->count();

            $abtc_count_ff2 = AbtcBakunaRecords::where('d7_done_by', $item->id)
            ->whereDate('d7_done_date', date('Y-m-d'))
            ->count();

            $abtc_count_ff3 = AbtcBakunaRecords::where('d14_done_by', $item->id)
            ->whereDate('d14_done_date', date('Y-m-d'))
            ->count();

            $abtc_count_ff4 = AbtcBakunaRecords::where('d28_done_by', $item->id)
            ->whereDate('d28_done_date', date('Y-m-d'))
            ->count();

            $abtc_ffup_gtotal = $abtc_count_ff1 + $abtc_count_ff2 + $abtc_count_ff3 + $abtc_count_ff4;

            $vaxcert_count = VaxcertConcern::where('processed_by', $item->id)
            ->whereDate('updated_at', date('Y-m-d'))
            ->count();

            $opd_count = SyndromicRecords::where('created_by', $item->id)
            ->whereDate('created_at', date('Y-m-d'))
            ->count();

            array_push($arr, [
                'name' => $item->name,
                'suspected_count' => $suspected_count,
                'confirmed_count' => $confirmed_count,
                'recovered_count' => $recovered_count,
                'negative_count' => $negative_count,
                'abtc_count' => $abtc_count,
                'abtc_ffup_gtotal' => $abtc_ffup_gtotal,
                'vaxcert_count' => $vaxcert_count,
                'opd_count' => $opd_count,
            ]);
        }
        
        return $this->markdown('email.encoder_stats', [
            'arr' => $arr,
        ])
        ->from('admin@cesugentri.com', 'Christian James Historillo')
        ->subject('CESU Gen. Trias - Encoder Status for '.date('F d, Y'));
    }
}
