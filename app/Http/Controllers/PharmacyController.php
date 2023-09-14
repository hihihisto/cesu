<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Brgy;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PharmacyBranch;
use App\Models\PharmacySupply;
use App\Models\PharmacyPatient;
use App\Models\PharmacyStockLog;
use App\Models\PharmacyStockCard;
use App\Models\PharmacySupplySub;
use Illuminate\Support\Facades\DB;
use App\Models\PharmacySupplyStock;
use App\Models\PharmacySupplyMaster;
use App\Models\PharmacySupplySubStock;
use PhpOffice\PhpWord\TemplateProcessor;

/*
PERMISSION LIST
PHARMACY_ADMIN
PHARMACY_ENCODER
*/

class PharmacyController extends Controller
{
    public function home() {
        return view('pharmacy.home');
    }

    public function addMasterItem(Request $r) {
        $check = PharmacySupplyMaster::where('sku_code', mb_strtoupper($r->sku_code))
        ->orWhere('name', mb_strtoupper($r->name))
        ->first();

        if(!($check)) {
            $c = $r->user()->pharmacysupplymaster()->create([
                'name' => mb_strtoupper($r->name),
                'sku_code' => mb_strtoupper($r->sku_code),
                'category' => $r->category,
                'description' => $r->description,
                'quantity_type' => $r->quantity_type,
                'config_piecePerBox' => ($r->quantity_type == 'BOX') ? $r->config_piecePerBox : NULL, 
            ]);

            if($r->master_box_stock != 0) {
                $d = $r->user()->pharmacysupplysub()->create([
                    'supply_master_id' => $c->id,
                    'pharmacy_branch_id' => auth()->user()->pharmacy_branch_id,
                    'self_sku_code' => NULL,
                    'self_description' => NULL,
    
                    'po_contract_number' => $r->po_contract_number,
                    'supplier' => $r->supplier,
                    'dosage_form' => $r->dosage_form,
                    'dosage_strength' => $r->dosage_strength,
                    'unit_measure' => $r->unit_measure,
                    'entity_name' => $r->entity_name,
                    'source_of_funds' => $r->source_of_funds,
                    'unit_cost' => $r->unit_cost,
                    'mode_of_procurement' => $r->mode_of_procurement,
                    'end_user' => $r->end_user,
                    'default_issuance_per_box' => NULL,
                    'default_issuance_per_piece' => NULL,
    
                    'master_box_stock' => ($r->quantity_type == 'BOX') ? $r->master_box_stock : NULL,
                    'master_piece_stock' => ($r->quantity_type == 'BOX') ? ($r->config_piecePerBox * $r->master_box_stock) : $r->master_box_stock, 
                ]);
    
                $e = $r->user()->pharmacysupplysubstock()->create([
                    'subsupply_id' => $d->id,
                    'expiration_date' => $r->expiration_date,
                    'current_box_stock' => ($r->quantity_type == 'BOX') ? $r->master_box_stock : NULL,
                    'current_piece_stock' => ($r->quantity_type == 'BOX') ? ($r->config_piecePerBox * $r->master_box_stock) : $r->master_box_stock,
                ]);
    
                $f = $r->user()->pharmacystockcard()->create([
                    'subsupply_id' => $d->id,
    
                    'type' => 'RECEIVED',
                    'before_qty_box' => ($r->quantity_type == 'BOX') ? 0 : NULL,
                    'before_qty_piece' => 0,
                    'qty_to_process' => $r->master_box_stock,
                    'qty_type' => $r->quantity_type,
                    'after_qty_box' => ($r->quantity_type == 'BOX') ? $r->master_box_stock : NULL,
                    'after_qty_piece' => ($r->quantity_type == 'BOX') ? ($r->config_piecePerBox * $r->master_box_stock) : $r->master_box_stock,
                    'total_cost' => NULL,
                    'drsi_number' => NULL,
    
                    'recipient' => NULL,
                    'remarks' => 'INITIAL ENCODING',
                ]);
            }

            return redirect()->back()
            ->with('msg', 'Master Item was added successfully.')
            ->with('msgtype', 'success');
        }
        else {
            return redirect()->back()
            ->with('msg', 'Error: Master Item already exists in the database. Please double check the Item Name and SKU Code.')
            ->with('msgtype', 'warning');
        }   
    }

    public function addSubItem($master_id, Request $r) {

        /*
        $check = PharmacySupply::where('sku_code', mb_strtoupper($r->sku_code))
        ->where('pharmacy_branch_id', auth()->user()->pharmacy_branch_id)
        ->first();

        if(!($check)) {
            $add = $r->user()->pharmacysupply()->create([
                'pharmacy_branch_id' => auth()->user()->pharmacy_branch_id,
                'name' => mb_strtoupper($r->name),
                'category' => $r->category,
                'quantity_type' => $r->quantity_type,
    
                'sku_code' => mb_strtoupper($r->sku_code),
                'po_contract_number' => $r->po_contract_number,
                'supplier' => $r->supplier,
                'description' => $r->description,
                'dosage_form' => $r->dosage_form,
                'dosage_strength' => $r->dosage_strength,
                'unit_measure' => $r->unit_measure,
                'entity_name' => $r->entity_name,
                'source_of_funds' => $r->source_of_funds,
                'unit_cost' => $r->unit_cost,
                'mode_of_procurement' => $r->mode_of_procurement,
                'end_user' => $r->end_user,
    
                'config_piecePerBox' => $r->config_piecePerBox,
                'master_box_stock' => $r->supply_base_stock,
                'master_piece_stock' => ($r->filled('config_piecePerBox')) ? ($r->config_piecePerBox * $r->supply_base_stock) : NULL,
            ]);
    
            $add_stock = $r->user()->pharmacysupplystock()->create([
                'supply_id' => $add->id,
                'expiration_date' => $r->expiration_date,
    
                'current_box_stock' => $r->supply_base_stock,
                'current_piece_stock' => ($r->filled('config_piecePerBox')) ? ($r->config_piecePerBox * $r->supply_base_stock) : NULL,
            ]);

            $add_stock_card = $r->user()->pharmacystockcard()->create([
                'supply_id' => $add->id,
                'type' => 'RECEIVED',
                'before_qty' => 0,
                'qty_to_process' => $r->supply_base_stock,
                'after_qty' => $r->supply_base_stock,
                'total_cost' => NULL,
                'drsi_number'=> NULL,

                'recipient' => NULL,
                'remarks'=> NULL,
            ]);

            return redirect()->route('pharmacy_itemlist')
            ->with('msg', 'Item ['.$r->name.'] was successfully added.')
            ->with('msgtype', 'success');
        }
        else {
            return redirect()->back()
            ->with('msg', 'Error: Item already exists in the database.')
            ->with('msgtype', 'warning');
        }
        */
    }

    public function modifyStockQr() {
        if(request()->input('code')) {
            $code = request()->input('code');

            if(Str::startsWith($code, 'PATIENT_')) {
                $newString = Str::replaceFirst("PATIENT_", "", $code);

                $d = PharmacyPatient::where('qr', $newString)->first();

                if($d) {
                    return redirect()->route('pharmacy_modify_patient_stock', [
                        'id' => $d->id,
                    ]);
                }
                else {
                    return redirect()->back()
                    ->with('msg', 'Error: Patient QR does not exist in the system.')
                    ->with('msgtype', 'warning');
                }
            }
            else if(Str::startsWith($code, 'SUBSTOCK_')) {
                $newString = Str::replaceFirst("SUBSTOCK_", "", $code);

                $d = PharmacySupplySubStock::where('id', $newString)->first();

                if($d) {
                    if($d->pharmacysub->pharmacy_branch_id == auth()->user()->pharmacy_branch_id) {
                        return redirect()->route('pharmacy_modify_view', [$d->pharmacysub->id, 'select_substock' => 1]);
                    }
                    else {
                        return redirect()->back()
                        ->with('msg', 'Error: You are not authorized to do that.')
                        ->with('msgtype', 'warning');
                    }
                }
                else {
                    return redirect()->back()
                    ->with('msg', 'Error: Item SubStock ID does not exist in the system.')
                    ->with('msgtype', 'warning');
                }
            }
            else {
                $d = PharmacySupplyMaster::where('sku_code', $code)->first();

                if($d) {
                    $e = PharmacySupplySub::whereHas('pharmacysupplymaster', function ($q) use ($code) {
                        $q->where('sku_code', $code);
                    })
                    ->where('pharmacy_branch_id', auth()->user()->pharmacy_branch_id)
                    ->first();

                    if($e) {
                        $t = PharmacySupplySubStock::where('subsupply_id', $d->id)
                        ->orderBy('expiration_date', 'ASC')
                        ->first();
    
                        if($t) {
                            return redirect()->route('pharmacy_modify_view', $t->id);
                        }
                    }
                    else {
                        return redirect()->back()
                        ->with('msg', 'Error: Item ['.$d->name.'] was not yet initialized in the Branch '.auth()->user()->pharmacybranch->name)
                        ->with('msgtype', 'warning');
                    }
                }
                else {
                    return redirect()->back()
                    ->with('msg', 'Error: SKU Code does not exist in the system.')
                    ->with('msgtype', 'warning');
                }
            }
        }
        else {
            return abort(401);
        }
    }

    public function modifyStockView($subsupply_id) {
        $d = PharmacySupplySub::findOrFail($subsupply_id);

        $sub_list = PharmacySupplySubStock::where('subsupply_id', $d->id)
        ->get();

        $branch_list = PharmacyBranch::where('id', '!=', auth()->user()->pharmacy_branch_id)
        ->orderBy('name', 'ASC')
        ->get();

        if(request()->input('process_patient')) {
            $get_patient = PharmacyPatient::findOrFail(request()->input('process_patient'));
        }
        else {
            $get_patient = NULL;
        }

        return view('pharmacy.modify_stock', [
            'd' => $d,
            'sub_list' => $sub_list,
            'branch_list' => $branch_list,
            'get_patient' => $get_patient,
        ]);
    }

    public function modifyStockProcess($subsupply_id, Request $r) {
        $d = PharmacySupplySub::findOrFail($subsupply_id);

        if($r->type == 'ISSUED') {
            $substock = PharmacySupplySubStock::findOrFail($r->select_sub_stock_id);

            //Check if Authorized
            if($substock->subsupply_id != $d->id && auth()->user()->pharmacy_branch_id != $d->pharmacy_branch_id) {
                return redirect()->back()
                ->withInput()
                ->with('msg', 'Error: You are not allowed to do that.')
                ->with('msgtype', 'warning');
            }
            
            //IF PATIENT, GET PATIENT
            if($r->select_recipient == 'PATIENT') {
                if(Str::startsWith($r->receiving_patient_id, 'PATIENT_')) {
                    $newString = Str::replaceFirst("PATIENT_", "", $r->receiving_patient_id);

                    $search_patient = PharmacyPatient::where('qr', $newString)->first();
                }
                else {
                    $search_patient = PharmacyPatient::where('qr', $r->receiving_patient_id)
                    ->orWhere('id', $r->receiving_patient_id)
                    ->first();
                }

                if($search_patient) {
                    $get_patient_id = $search_patient->id;
                }
                else {
                    return redirect()->back()
                    ->withInput()
                    ->with('msg', 'Patient record does not exist on the server. Please double check and try again.')
                    ->with('msgtype', 'warning');
                }
            }

            if($d->pharmacysupplymaster->quantity_type == 'BOX') {
                if($r->qty_type == 'BOX') {
                    //BOX ISSUANCE IN BOX

                    if($r->qty_to_process <= $substock->current_box_stock) {
                        //UPDATE SUBSUPPLY
                        $d->master_box_stock -= $r->qty_to_process;
                        $d->master_piece_stock -= $r->qty_to_process * $d->pharmacysupplymaster->config_piecePerBox;
                        
                        //UPDATE SUBSTOCK
                        $substock->current_box_stock -= $r->qty_to_process;
                        $substock->current_piece_stock -= $r->qty_to_process * $d->pharmacysupplymaster->config_piecePerBox;
                    }
                    else {
                        return redirect()->back()
                        ->withInput()
                        ->with('msg', 'Error: Quantity to Process is greater than the Current Batch Quantity. Or Current Batch Quantity was updated.')
                        ->with('msgtype', 'warning');
                    }
                }
                else {
                    //BOX ISSUANCE IN PIECE

                    if($r->qty_to_process <= $substock->current_piece_stock) {
                        //UPDATE SUBSUPPLY
                        $d->master_piece_stock -= $r->qty_to_process;
                        while(($d->master_box_stock * $d->pharmacysupplymaster->config_piecePerBox) > $d->master_piece_stock) {
                            $d->master_box_stock--;
                        }

                        //UPDATE SUBSTOCK
                        $substock->current_piece_stock -= $r->qty_to_process;
                        while(($substock->current_box_stock * $d->pharmacysupplymaster->config_piecePerBox) > $substock->current_piece_stock) {
                            $substock->current_box_stock--;
                        }
                    }
                    else {
                        return redirect()->back()
                        ->withInput()
                        ->with('msg', 'Error: Quantity to Process is greater than the Current Batch Quantity. Or Current Batch Quantity was updated.')
                        ->with('msgtype', 'warning');
                    }
                }
            }
            else {
                //PIECE ISSUANCE
                if($r->qty_to_process <= $substock->current_piece_stock) {
                    //UPDATE SUBSUPPLY
                    $d->master_piece_stock -= $r->qty_to_process;

                    //UPDATE SUBSTOCK
                    $substock->current_piece_stock -= $r->qty_to_process;
                }
                else {
                    return redirect()->back()
                    ->withInput()
                    ->with('msg', 'Error: Quantity to Process is greater than the Current Batch Quantity. Or Current Batch Quantity was updated.')
                    ->with('msgtype', 'warning');
                }
            }

            //CREATE STOCK CARD
            $new_stockcard = $r->user()->pharmacystockcard()->create([
                'subsupply_id' => $d->id,
                'type' => 'ISSUED',
                'before_qty_box' => ($d->pharmacysupplymaster->quantity_type == 'BOX') ? $d->getOriginal('master_box_stock') : NULL,
                'before_qty_piece' => $d->getOriginal('master_piece_stock'),
                'qty_to_process' => $r->qty_to_process,
                'qty_type' => $r->qty_type,
                'after_qty_box' => $d->master_box_stock,
                'after_qty_piece' => $d->master_piece_stock,
                'total_cost' => $r->total_cost,
                'drsi_number' => $r->drsi_number,

                'receiving_branch_id' => ($r->select_recipient == 'BRANCH') ? $r->receiving_branch_id : NULL,
                'receiving_patient_id' => ($r->select_recipient == 'PATIENT') ? $get_patient_id : NULL,
                'recipient' => ($r->select_recipient == 'OTHERS') ? $r->recipient : NULL,
                
                'remarks' => $r->remarks,
            ]);

            if($d->isDirty()) {
                $d->save();
            }

            if($substock->isDirty()) {
                $substock->save();
            }

            //IF BRANCH, PROCESS STOCKCARD
            if($r->select_recipient == 'BRANCH') {
                //PROCESS BRANCH SUBSUPPLY

                $branch_subsupply = PharmacySupplySub::whereHas('pharmacysupplymaster', function ($q) use ($d) {
                    $q->where('sku_code', $d->pharmacysupplymaster->sku_code);
                })->where('pharmacy_branch_id', $r->receiving_branch_id)
                ->first();

                if($d->pharmacysupplymaster->quantity_type == 'BOX') {
                    if($r->qty_type == 'BOX') {
                        //BOX ISSUANCE IN BOX

                        if($branch_subsupply) {
                            //OK
                            $branch_subsupply->master_box_stock += $r->qty_to_process;
                            $branch_subsupply->master_piece_stock += ($r->qty_to_process * $d->pharmacysupplymaster->config_piecePerBox);
                        
                            $sspid = $branch_subsupply->id;

                            $before_branch_qty_box = $branch_subsupply->getOriginal('master_box_stock');
                            $before_branch_qty_piece = $branch_subsupply->getOriginal('master_piece_stock');

                            $after_branch_qty_box = $branch_subsupply->master_box_stock;
                            $after_branch_qty_piece = $branch_subsupply->master_piece_stock;

                            if($branch_subsupply->isDirty()) {
                                $branch_subsupply->save();
                            }
                        }
                        else {
                            //OK
                            $new_branch_subsupply = $r->user()->pharmacysupplysub()->create([
                                'supply_master_id' => $d->pharmacysupplymaster->id,
                                'pharmacy_branch_id' => $r->receiving_branch_id,

                                'master_box_stock' => $r->qty_to_process,
                                'master_piece_stock' => ($r->qty_to_process * $d->pharmacysupplymaster->config_piecePerBox),
                            ]);

                            $sspid = $new_branch_subsupply->id;

                            $before_branch_qty_box = 0;
                            $before_branch_qty_piece = 0;

                            $after_branch_qty_box = $new_branch_subsupply->master_box_stock;
                            $after_branch_qty_piece = $new_branch_subsupply->master_piece_stock;
                        }
                    }
                    else {
                        //BOX ISSUANCE IN PIECE
                        if($branch_subsupply) {
                            //OK
                            $branch_subsupply->master_piece_stock += $r->$r->qty_to_process;
                            while(($branch_subsupply->master_box_stock * $d->pharmacysupplymaster->config_piecePerBox) < $branch_subsupply->master_piece_stock) {
                                $branch_subsupply->master_box_stock++;
                            }

                            $sspid = $branch_subsupply->id;

                            $before_branch_qty_box = $branch_subsupply->getOriginal('master_box_stock');
                            $before_branch_qty_piece = $branch_subsupply->getOriginal('master_piece_stock');

                            $after_branch_qty_box = $branch_subsupply->master_box_stock;
                            $after_branch_qty_piece = $branch_subsupply->master_piece_stock;

                            if($branch_subsupply->isDirty()) {
                                $branch_subsupply->save();
                            }
                        }
                        else {
                            //OK
                            $new_branch_subsupply = $r->user()->pharmacysupplysub()->create([
                                'supply_master_id' => $d->pharmacysupplymaster->id,
                                'pharmacy_branch_id' => $r->receiving_branch_id,

                                'master_box_stock' => ($r->qty_to_process % $d->pharmacysupplymaster->config_piecePerBox === 0) ? ($r->qty_to_process / $d->pharmacysupplymaster->config_piecePerBox) : 0,
                                'master_piece_stock' => $r->qty_to_process,
                            ]);

                            $sspid = $new_branch_subsupply->id;

                            $before_branch_qty_box = 0;
                            $before_branch_qty_piece = 0;

                            $after_branch_qty_box = $new_branch_subsupply->master_box_stock;
                            $after_branch_qty_piece = $new_branch_subsupply->master_piece_stock;
                        }
                    }
                }
                else {
                    //PIECE ISSUANCE
                    if($branch_subsupply) {
                        //OK
                        $branch_subsupply->master_piece_stock += $r->qty_to_process;

                        $sspid = $branch_subsupply->id;
                        
                        $before_branch_qty_box = NULL;
                        $before_branch_qty_piece = $branch_subsupply->getOriginal('master_piece_stock');

                        $after_branch_qty_box = NULL;
                        $after_branch_qty_piece = $branch_subsupply->master_piece_stock;

                        if($branch_subsupply->isDirty()) {
                            $branch_subsupply->save();
                        }
                    
                    }
                    else {
                        //OK
                        $new_branch_subsupply = $r->user()->pharmacysupplysub()->create([
                            'supply_master_id' => $d->pharmacysupplymaster->id,
                            'pharmacy_branch_id' => $r->receiving_branch_id,

                            //'master_box_stock' => ($r->qty_to_process % $d->pharmacysupplymaster->config_piecePerBox === 0) ? ($r->qty_to_process / $d->pharmacysupplymaster->config_piecePerBox) : 0,
                            'master_piece_stock' => $r->qty_to_process,
                        ]);

                        $before_branch_qty_box = NULL;
                        $before_branch_qty_piece = 0;

                        $after_branch_qty_box = NULL;
                        $after_branch_qty_piece = $new_branch_subsupply->master_piece_stock;

                        $sspid = $new_branch_subsupply->id;
                    }
                }

                //PROCESS BRANCH SUBSTOCK
                $branch_substock = PharmacySupplySubStock::where('subsupply_id', $sspid)
                ->whereDate('expiration_date', $substock->expiration_date)
                ->first();

                if($d->pharmacysupplymaster->quantity_type == 'BOX') {
                    if($r->qty_type == 'BOX') {
                        if($branch_substock) {
                            //OK
                            $branch_substock->current_box_stock += $r->qty_to_process;
                            $branch_substock->current_piece_stock += ($r->qty_to_process * $d->pharmacysupplymaster->config_piecePerBox);
                        
                            if($branch_substock->isDirty()) {
                                $branch_substock->save();
                            }
                        }
                        else {
                            //OK
                            $new_branch_substock = $r->user()->pharmacysupplysubstock()->create([
                                'subsupply_id' => $sspid,
                                'expiration_date' => $substock->expiration_date,
                                'current_box_stock' => $r->qty_to_process,
                                'current_piece_stock' => ($r->qty_to_process * $d->pharmacysupplymaster->config_piecePerBox),
                            ]);
                        }
                    }
                    else {
                        if($branch_substock) {
                            //OK
                            $branch_substock->current_piece_stock += $r->qty_to_process;
                            while(($branch_substock->current_box_stock * $d->pharmacysupplymaster->config_piecePerBox) < $branch_substock->current_piece_stock) {
                                $branch_substock->current_box_stock++;
                            }

                            if($branch_substock->isDirty()) {
                                $branch_substock->save();
                            }
                        }
                        else {
                            //OK
                            $new_branch_substock = $r->user()->pharmacysupplysubstock()->create([
                                'subsupply_id' => $sspid,
                                'expiration_date' => $substock->expiration_date,
                                'current_box_stock' => ($r->qty_to_process % $d->pharmacysupplymaster->config_piecePerBox === 0) ? ($r->qty_to_process / $d->pharmacysupplymaster->config_piecePerBox) : 0,
                                'current_piece_stock' => $r->qty_to_process,
                            ]);
                        }
                    }
                }
                else {
                    if($branch_substock) {
                        $branch_substock->current_piece_stock += $r->qty_to_process;

                        if($branch_substock->isDirty()) {
                            $branch_substock->save();
                        }
                    }
                    else {
                        $new_branch_substock = $r->user()->pharmacysupplysubstock()->create([
                            'subsupply_id' => $sspid,
                            'expiration_date' => $substock->expiration_date,
                            //'current_box_stock' => ($r->qty_to_process % $d->pharmacysupplymaster->config_piecePerBox === 0) ? ($r->qty_to_process / $d->pharmacysupplymaster->config_piecePerBox) : 0,
                            'current_piece_stock' => $r->qty_to_process,
                        ]);
                    }
                }

                //CREATE STOCK CARD FOR BRANCH
                $new_stockcard_branch = $r->user()->pharmacystockcard()->create([
                    'subsupply_id' => $sspid,
                    'type' => 'RECEIVED',
                    'before_qty_box' => $before_branch_qty_box,
                    'before_qty_piece' => $before_branch_qty_piece,
                    'qty_to_process' => $r->qty_to_process,
                    'qty_type' => $r->qty_type,
                    'after_qty_box' => $after_branch_qty_box,
                    'after_qty_piece' => $after_branch_qty_piece,
                    //'total_cost' => $r->total_cost,
                    //'drsi_number' => $r->drsi_number,

                    //'receiving_branch_id' => ($r->select_recipient == 'BRANCH') ? $r->receiving_branch_id : NULL,
                    //'receiving_patient_id' => ($r->select_recipient == 'PATIENT') ? $get_patient_id : NULL,
                    //'recipient' => ($r->select_recipient == 'OTHERS') ? $r->recipient : NULL,
                    
                    'remarks' => 'RECEIVED FROM '.$d->pharmacybranch->name,
                ]);
            }

            $txt = 'Processing of ISSUED Item was successful';
        }
        else if ($r->type == 'RECEIVED') {
            //RECEIVE, BOX MODE ONLY LOCKED TO MAIN BRANCH

            if($d->pharmacysupplymaster->quantity_type == 'BOX') {
                $d->master_box_stock += $r->qty_to_process;
                $d->master_piece_stock += ($r->qty_to_process * $d->pharmacysupplymaster->config_piecePerBox);
            }
            else {
                $d->master_piece_stock += $r->qty_to_process;
                
            }

            //ADD TO SUBSTOCK
            $substock = PharmacySupplySubStock::where('subsupply_id', $d->id)
            ->where('expiration_date', $r->expiration_date)
            ->first();

            if($d->pharmacysupplymaster->quantity_type == 'BOX') {
                if($substock) {
                    $substock->current_box_stock =+ $r->qty_to_process;
                    $substock->current_piece_stock += ($r->qty_to_process * $d->pharmacysupplymaster->config_piecePerBox);

                    if($substock->isDirty()) {
                        $substock->save();
                    }
                }
                else {
                    $new_substock = $r->user()->pharmacysupplysubstock()->create([
                        'subsupply_id' => $d->id,
                        'expiration_date' => $r->expiration_date,
                        'current_box_stock' => $r->qty_to_process,
                        'current_piece_stock' => ($r->qty_to_process * $d->pharmacysupplymaster->config_piecePerBox),
                    ]);
                }
            }
            else {
                if($substock) {
                    $substock->current_piece_stock += $r->qty_to_process;

                    if($substock->isDirty()) {
                        $substock->save();
                    }
                }
                else {
                    $new_substock = $r->user()->pharmacysupplysubstock()->create([
                        'subsupply_id' => $d->id,
                        'expiration_date' => $r->expiration_date,
                        //'current_box_stock' => $r->qty_to_process,
                        'current_piece_stock' => $r->qty_to_process,
                    ]);
                }
            }
            
            //CREATE STOCK CARD
            $new_stockcard_branch = $r->user()->pharmacystockcard()->create([
                'subsupply_id' => $d->id,
                'type' => 'RECEIVED',
                'before_qty_box' => ($d->pharmacysupplymaster->quantity_type == 'BOX') ? $d->getOriginal('master_box_stock') : NULL,
                'before_qty_piece' => $d->getOriginal('master_piece_stock'),
                'qty_to_process' => $r->qty_to_process,
                'qty_type' => $r->qty_type,
                'after_qty_box' => ($d->pharmacysupplymaster->quantity_type == 'BOX') ? $d->master_box_stock : NULL,
                'after_qty_piece' => $d->master_piece_stock,

                //'total_cost' => $r->total_cost,
                //'drsi_number' => $r->drsi_number,

                //'receiving_branch_id' => ($r->select_recipient == 'BRANCH') ? $r->receiving_branch_id : NULL,
                //'receiving_patient_id' => ($r->select_recipient == 'PATIENT') ? $get_patient_id : NULL,
                //'recipient' => ($r->select_recipient == 'OTHERS') ? $r->recipient : NULL,
                
                'remarks' => mb_strtoupper($r->remarks),
            ]);

            if($d->isDirty()) {
                $d->save();
            }

            $txt = 'Processing of RECEIVED Item was successful';
        }

        return redirect()->route('pharmacy_home')
        ->with('msg', $txt)
        ->with('msgtype', 'success');
    }

    public function masterItemHome() {
        if(request()->input('q')) {
            $q = request()->input('q');
            
            $list = PharmacySupplyMaster::where('sku_code', $q)
            ->orWhere('name','LIKE', '%'.$q.'%')
            ->orWhere('sku_code_doh', $q)
            ->paginate(10);
        }
        else {
            $list = PharmacySupplyMaster::orderBy('name', 'ASC')
            ->paginate(10);
        }
        
        return view('pharmacy.itemlist_viewMasterList', [
            'list' => $list,
        ]);
    }

    public function viewMasterItem($id) {
        $d = PharmacySupplyMaster::findOrFail($id);
        
        return view('pharmacy.itemlist_viewMaster', [
            'd' => $d,
        ]);
    }

    public function updateMasterItem($id, Request $r) {
        //find existing first

        $d = PharmacySupplyMaster::where('id', '!=', $id)
        ->where(function ($q) use ($r) {
            $q->where('name', mb_strtoupper($r->name))
            ->orWhere('sku_code', mb_strtoupper($r->sku_code));
        })->first();

        if(!($d)) {
            $u = PharmacySupplyMaster::where('id', $id)
            ->update([
                'enabled' => $r->enabled,
                'name' => mb_strtoupper($r->name),
                'sku_code' => mb_strtoupper($r->sku_code),
                'sku_code_doh' => mb_strtoupper($r->sku_code_doh),
                'category' => $r->category,
                'description' => $r->description,
                'quantity_type' => $r->quantity_type,
                'config_piecePerBox' => $r->config_piecePerBox,

                'updated_by' => auth()->user()->id,
            ]);

            return redirect()->route('pharmacy_masteritem_list')
            ->with('msg', 'Master Item details were updated successfully.')
            ->with('msgtype', 'success');
        }
        else {
            return redirect()->back()
            ->with('msg', 'Error: Another Master Item Name or SKU already exists in the server.')
            ->with('msgtype', 'warning');
        }
    }
    
    public function viewItemList() {
        if(request()->input('q')) {
            $q = request()->input('q');

            $list = PharmacySupplySub::whereHas('pharmacysupplymaster', function ($r) use ($q) {
                $r->where('sku_code', $q)
                ->orWhere('name','LIKE', '%'.$q.'%');
            })
            ->where('pharmacy_branch_id', auth()->user()->pharmacy_branch_id)
            ->paginate(10);
        }
        else {
            $list = PharmacySupplySub::where('pharmacy_branch_id', auth()->user()->pharmacy_branch_id)
            ->whereHas('pharmacysupplymaster', function ($q) {
                $q->orderBy('name', 'ASC');
            })
            ->paginate(10);
        }

        return view('pharmacy.itemlist', [
            'list' => $list,
        ]);
    }

    public function viewItem($item_id) {
        $item = PharmacySupplySub::findOrFail($item_id);

        if($item->pharmacy_branch_id == auth()->user()->pharmacy_branch_id) {
            $sub_list = PharmacySupplySubStock::where('subsupply_id', $item->id)
            ->orderBy('expiration_date', 'ASC')
            ->get();

            $scard = PharmacyStockCard::where('subsupply_id', $item->id)
            ->orderBy('created_at', 'DESC')
            ->get();

            return view('pharmacy.itemlist_viewSub', [
                'd' => $item,
                'sub_list' => $sub_list,
                'scard' => $scard,
            ]);
        }
        else {
            return abort(401);
        }
    }

    public function viewItemMonthlyStock($item_id) {
        $item = PharmacySupplySub::findOrFail($item_id);

        $month_array = [];

        //month loop
        for($i=1;$i<=12;$i++) {
            $nomonth = Carbon::create()->month($i)->format('m');

            $issued_count = PharmacyStockCard::where('subsupply_id', $item->id)
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', $nomonth)
            ->where('status', 'approved')
            ->where('type', 'ISSUED')
            ->sum('qty_to_process');

            $received_count = PharmacyStockCard::where('subsupply_id', $item->id)
            ->whereYear('created_at', date('Y'))
            ->whereMonth('created_at', $nomonth)
            ->where('status', 'approved')
            ->where('type', 'RECEIVED')
            ->sum('qty_to_process');

            $month_array[] = [
                'month' => Carbon::create()->month($i)->format('F'),
                'issued_count' => $issued_count,
                'received_count' => $received_count,
            ];
        }

        return view('pharmacy.itemlist_viewSubMonthlyStock', [
            'd' => $item,
            'month_array' => $month_array,
        ]);
    }

    public function printQrItem ($item_id) {
        $d = PharmacySupplySub::findOrFail($item_id);

        header("Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header("Content-Disposition: attachment; filename=PHARMACY_SUBCARD_".$d->id.".docx");

        $templateProcessor  = new TemplateProcessor(storage_path('PHARMACY_SUBQR.docx'));
        $templateProcessor->setValue('sku_code', $d->pharmacysupplymaster->sku_code);
        $templateProcessor->setValue('name', $d->pharmacysupplymaster->name);
        $templateProcessor->setValue('branch', $d->pharmacybranch->name);

        $templateProcessor->saveAs('php://output');
    }

    public function updateItem($item_id, Request $r) {
        $d = PharmacySupplySub::findOrFail($item_id);

        if($d->ifAuthorizedToUpdate()) {
            $d->update([
                'self_sku_code' => $r->self_sku_code,
                'self_description' => $r->self_description,

                'po_contract_number' => $r->po_contract_number,
                'supplier' => $r->supplier,
                'dosage_form' => $r->dosage_form,
                'dosage_strength' => $r->dosage_strength,
                'unit_measure' => $r->unit_measure,
                'entity_name' => $r->entity_name,
                'source_of_funds' => $r->source_of_funds,
                'unit_cost' => $r->unit_cost,
                'mode_of_procurement' => $r->mode_of_procurement,
                'end_user' => $r->end_user,
            ]);

            return redirect()->route('pharmacy_itemlist_viewitem')
            ->with('msg', 'Details of the Sub-Item was updated successfully.')
            ->with('msgtype', 'success');
        }
        else {
            return abort(401);
        }
    }

    public function viewSubStock($id) {
        $d = PharmacySupplySubStock::findOrFail($id);

        if($d->ifUserAuthorized()) {
            return view('pharmacy.substock_view', [
                'd' => $d,
            ]);
        }
        else {
            return abort(401);
        }
    }

    public function printQrSubStock($id) {
        $d = PharmacySupplySubStock::findOrFail($id);

        header("Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header("Content-Disposition: attachment; filename=PHARMACY_SUBSTOCK_".$d->id.".docx");

        $templateProcessor  = new TemplateProcessor(storage_path('PHARMACY_SUBSTOCKQR.docx'));
        $templateProcessor->setValue('id', $d->id);
        $templateProcessor->setValue('name', $d->pharmacysub->pharmacysupplymaster->name);
        $templateProcessor->setValue('sku_code', $d->pharmacysub->pharmacysupplymaster->sku_code);
        $templateProcessor->setValue('branch', $d->pharmacysub->pharmacybranch->name);

        $templateProcessor->saveAs('php://output');
    }

    public function updateSubStock($id, Request $r) {
        $d = PharmacySupplySubStock::findOrFail($id);

        if($d->ifUserAuthorized()) {
            $d->expiration_date = $r->expiration_date;
            $d->batch_number = $r->batch_number;
            $d->lot_number = $r->lot_number;

            if($d->pharmacysub->pharmacysupplymaster->quantity_type == 'BOX') {
                $d->current_box_stock = $r->change_qty_box;
                $d->current_piece_stock = $r->change_qty_piece;
                
                if($d->isDirty('current_box_stock')) {
                    $sb = PharmacySupplySub::findOrFail($d->pharmacysub->id);

                    if($d->getOriginal('current_box_stock') > $r->change_qty_box) {
                        $sb->master_box_stock -= ($d->getOriginal('current_box_stock') - $r->change_qty_box);
                        $sb->master_piece_stock -= ($d->getOriginal('current_box_stock') - $r->change_qty_box) * $sb->pharmacysupplymaster->config_piecePerBox;
                    }
                    else {
                        $sb->master_box_stock += ($r->change_qty_box - $d->getOriginal('current_box_stock'));
                        $sb->master_piece_stock += ($d->getOriginal('current_box_stock') - $r->change_qty_box) * $sb->pharmacysupplymaster->config_piecePerBox;
                    }

                    if($sb->isDirty()) {
                        $sb->save();
                    }
                }

                /*
                if($d->isDirty('current_piece_stock')) {
                    $sb = PharmacySupplySub::findOrFail($d->pharmacysub->id);

                    if($d->getOriginal('current_piece_stock') > $r->change_qty_piece) {
                        $piece_multiplier = ($d->getOriginal('current_piece_stock') - $r->change_qty_piece) * $d->pharmacysupplymaster->config_piecePerBox;
                        
                        $sb->master_piece_stock -= ($d->getOriginal('current_piece_stock') - $r->change_qty_piece);
                    }
                    else {
                        $sb->master_piece_stock += ($r->change_qty_piece - $d->getOriginal('current_piece_stock'));
                    }

                    if($sb->isDirty()) {
                        $sb->save();
                    }
                }
                */
            }
            else {
                $d->current_piece_stock = $r->change_qty_piece;

                if($d->isDirty('current_piece_stock')) {
                    $sb = PharmacySupplySub::findOrFail($d->pharmacysub->id);

                    if($d->getOriginal('current_piece_stock') > $r->change_qty_piece) {
                        $sb->pharmacysub->master_piece_stock -= ($d->getOriginal('current_piece_stock') - $r->change_qty_piece);
                    }
                    else {
                        $sb->pharmacysub->master_piece_stock += ($r->change_qty_piece - $d->getOriginal('current_piece_stock'));
                    }

                    if($sb->isDirty()) {
                        $sb->save();
                    }
                }
            }

            if($d->isDirty()) {
                $d->save();
            }

            return redirect()->route('pharmacy_itemlist_viewitem', $d->pharmacysub->id)
            ->with('msg', 'Pharmacy Sub Stock (ID: #'.$d->id.') was updated successfully.')
            ->with('msgtype', 'success');
        }
        else {
            return abort(401);
        }        
    }

    public function exportStockCard($supply_id) {
        $item = PharmacyStockCard::where('supply_id', $supply_id)
        ->orderBy('created_at', 'ASC')
        ->get();
    }

    public function viewReport() {
        if(request()->input('select_branch')) {
            $selected_branch = request()->input('select_branch');
        }
        else {
            $selected_branch = auth()->user()->pharmacy_branch_id;
        }

        $list_branch = PharmacyBranch::get();

        if(request()->input('type') && request()->input('year')) {
            $input_type = request()->input('type');
            $input_year = request()->input('year');

            //TOP FAST MOVING MEDS

            //TOP BRGY ISSUANCE
            $list_entities = PharmacyBranch::where('id', '!=', auth()->user()->pharmacy_branch_id)->get();
            
            foreach($list_entities as $e) {
                //CHECK IF HAS ISSUANCE RECORD FIRST
                $check = PharmacyStockCard::where('receiving_branch_id', $e->id)
                ->where('status', 'approved')
                ->where('type', 'ISSUED');

                if($input_type == 'YEARLY') {
                    $check = $check->whereYear('created_at', $input_year)->first();
                }
                else if($input_type == 'QUARTERLY') {

                }
                else if($input_type == 'MONTHLY') {

                }
                else if($input_type == 'WEEKLY') {

                }

                if($check) {
                    $issued_box_qry = PharmacyStockCard::where('receiving_branch_id', $e->id)
                    ->where('status', 'approved')
                    ->where('type', 'ISSUED')
                    ->where('qty_type', 'BOX');

                    if($input_type == 'YEARLY') {
                        $issued_box_count = $issued_box_qry->whereYear('created_at', $input_year)
                        ->sum('qty_to_process');
                    }
                    else if($input_type == 'QUARTERLY') {
    
                    }
                    else if($input_type == 'MONTHLY') {
    
                    }
                    else if($input_type == 'WEEKLY') {
    
                    }

                    $entities_arr[] = [
                        'name' => $e->name,
                        'issued_box_count' => $issued_box_count,
                    ];
                }
            }

            //STOCKS MASTERLIST
            $list_subitem = PharmacySupplySub::where('pharmacy_branch_id', $selected_branch)
            ->get();

            $si_array = [];

            foreach($list_subitem as $key => $si) {
                $items_list[] = [
                    'name' => $si->pharmacysupplymaster->name,
                    'current_stock' => $si->displayQty(),
                    'id' => $si->id,
                ];
            }

            foreach($items_list as $item) {
                $monthlyStocks = [];

                for($i=1;$i<=12;$i++) {
                    $nomonth = Carbon::create()->month($i)->format('m');

                    $issued_count = PharmacyStockCard::where('subsupply_id', $item['id'])
                    ->whereYear('created_at', date('Y'))
                    ->whereMonth('created_at', $nomonth)
                    ->where('status', 'approved')
                    ->where('type', 'ISSUED')
                    ->sum('qty_to_process');

                    $received_count = PharmacyStockCard::where('subsupply_id', $item['id'])
                    ->whereYear('created_at', date('Y'))
                    ->whereMonth('created_at', $nomonth)
                    ->where('status', 'approved')
                    ->where('type', 'RECEIVED')
                    ->sum('qty_to_process');

                    $monthlyStocks[] = [
                        'month' => Carbon::create()->month($i)->format('F'),
                        'issued' => $issued_count,
                        'received' => $received_count,
                    ];
                }

                $result[] = [
                    'name' => $item['name'],
                    'id' => $item['id'],
                    'current_stock' => $item['current_stock'],
                    'monthly_stocks' => $monthlyStocks,
                ];
            }
            
            //NEAREST EXPIRATION DATES
            $expired_list = PharmacySupplySubStock::whereBetween('expiration_date', [date('Y-m-d'), date('Y-m-t', strtotime('+3 Months'))])
            ->where('current_box_stock', '>', 0)
            ->orderBy('expiration_date', 'ASC')
            ->get();

            //GET PATIENT AGE GROUPS / MALE,FEMALE

            //GET PATIENT TOP REASON FOR MEDS

            return view('pharmacy.report', [
                'expired_list' => $expired_list,
                'list_branch' => $list_branch,
                'si_array' => $result,
            ]);
        }
        else {
            return view('pharmacy.report');
        }
    }

    public function viewPatientList() {
        if(request()->input('q')) {
            $search = request()->input('q');

            $list = PharmacyPatient::where(DB::raw('CONCAT(lname," ",fname," ", mname)'), 'LIKE', "%".str_replace(',','',mb_strtoupper($search))."%")
            ->orWhere(DB::raw('CONCAT(lname," ",fname)'), 'LIKE', "%".str_replace(',','',mb_strtoupper($search))."%")
            ->orWhere('id', $search)
            ->paginate(10);
        }
        else {
            $list = PharmacyPatient::orderBy('created_at', 'DESC')
            ->paginate(10);
        }

        return view('pharmacy.patient_list', [
            'list' => $list,
        ]);
    }

    public function newPatient() {
        if(request()->input('lname') && request()->input('fname') && request()->input('bdate')) {
            if(PharmacyPatient::ifDuplicateFound(request()->input('lname'), request()->input('fname'), request()->input('mname'), request()->input('suffix'), request()->input('bdate'))) {
                dd('exit');
            }
            else {
                return view('pharmacy.patient_register');
            }
        }
        else {
            return abort(401);
        }
    }

    public function storePatient(Request $r) {
        $foundunique = false;

        while(!$foundunique) {
            $qr = mb_strtoupper(Str::random(6));

            $search = PharmacyPatient::where('qr', $qr)->first();
            if(!$search) {
                $foundunique = true;
            }
        }

        $c = $r->user()->pharmacypatient()->create([
            'lname' => mb_strtoupper($r->lname),
            'fname' => mb_strtoupper($r->fname),
            'mname' => ($r->filled('mname')) ? mb_strtoupper($r->mname) : NULL,
            'suffix' => ($r->filled('suffix')) ? mb_strtoupper($r->suffix) : NULL,
            'bdate' => $r->bdate,
            'gender' => $r->gender,
            'email' => $r->email,
            'contact_number' => $r->contact_number,
            'contact_number2' => $r->contact_number2,
            'philhealth' => $r->philhealth,
    
            'address_region_code' => $r->address_region_code,
            'address_region_text' => $r->address_region_text,
            'address_province_code' => $r->address_province_code,
            'address_province_text' => $r->address_province_text,
            'address_muncity_code' => $r->address_muncity_code,
            'address_muncity_text' => $r->address_muncity_text,
            'address_brgy_code' => $r->address_brgy_text,
            'address_brgy_text' => $r->address_brgy_text,
            'address_street' => ($r->filled('address_street')) ? mb_strtoupper($r->address_street) : NULL,
            'address_houseno' => ($r->filled('address_houseno')) ? mb_strtoupper($r->address_houseno) : NULL,
            
            'concerns_list' => implode(',', $r->concerns_list),
            'qr' => $qr,
    
            'id_file' => NULL,
            'selfie_file' => NULL,
    
            'status' => 'ENABLED',
    
            'pharmacy_branch_id' => auth()->user()->pharmacy_branch_id,
        ]);

        return redirect()->route('pharmacy_view_patient_list')
        ->with('msg', 'Patient record successfully created.')
        ->with('msgtype', 'success');
    }

    public function viewPatient($id) {
        $d = PharmacyPatient::findOrFail($id);

        $scard = PharmacyStockCard::where('receiving_patient_id', $d->id)
        ->orderBy('created_at', 'DESC')
        ->get();

        return view('pharmacy.patient_view', [
            'd' => $d,
            'scard' => $scard,
        ]);
    }

    public function printPatientCard($id) {
        /*
        TEMPLATE PROCESSOR

        $d = PharmacyPatient::findOrFail($id);

        header("Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header("Content-Disposition: attachment; filename=PHARMACY_CARD_".$d->lname.".docx");

        $templateProcessor  = new TemplateProcessor(storage_path('PHARMACY_PATIENT_CARD.docx'));
        $templateProcessor->setValue('patient_id', $d->id);

        $templateProcessor->saveAs('php://output');
        */

        $d = PharmacyPatient::findOrFail($id);

        header("Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        header("Content-Disposition: attachment; filename=PHARMACY_CARD_".$d->lname.".docx");

        $templateProcessor  = new TemplateProcessor(storage_path('PHARMACY_PATIENT_CARD.docx'));

        $templateProcessor->setValue('patient_id', $d->id);
        $templateProcessor->setValue('dreg', date('m/d/Y', strtotime($d->created_at)));
        $templateProcessor->setValue('name', $d->getName());
        $templateProcessor->setValue('bdate', date('m/d/Y', strtotime($d->bdate)));
        $templateProcessor->setValue('agesex', $d->getAge().' / '.$d->sg());
        $templateProcessor->setValue('address', $d->getCompleteAddress());
        $templateProcessor->setValue('patient_qr', 'PATIENT_'.$d->qr);
        $templateProcessor->setValue('qr', $d->qr);
        $templateProcessor->setValue('branch', $d->pharmacybranch->name);

        $templateProcessor->saveAs('php://output');
    }
    
    public function updatePatient($id, Request $r) {
        $d = PharmacyPatient::findOrFail($id);

        if(!(PharmacyPatient::ifDuplicateFoundOnUpdate($d->id, $r->lname, $r->fname, $r->mname, $r->suffix, $r->bdate))) {
            $d->update([
                'lname' => mb_strtoupper($r->lname),
                'fname' => mb_strtoupper($r->fname),
                'mname' => ($r->filled('mname')) ? mb_strtoupper($r->mname) : NULL,
                'suffix' => ($r->filled('suffix')) ? mb_strtoupper($r->suffix) : NULL,
                'bdate' => $r->bdate,
                'gender' => $r->gender,
                'email' => $r->email,
                'contact_number' => $r->contact_number,
                'contact_number2' => $r->contact_number2,
                'philhealth' => $r->philhealth,
        
                'address_region_code' => $r->address_region_code,
                'address_region_text' => $r->address_region_text,
                'address_province_code' => $r->address_province_code,
                'address_province_text' => $r->address_province_text,
                'address_muncity_code' => $r->address_muncity_code,
                'address_muncity_text' => $r->address_muncity_text,
                'address_brgy_code' => $r->address_brgy_text,
                'address_brgy_text' => $r->address_brgy_text,
                'address_street' => ($r->filled('address_street')) ? mb_strtoupper($r->address_street) : NULL,
                'address_houseno' => ($r->filled('address_houseno')) ? mb_strtoupper($r->address_houseno) : NULL,
                
                'concerns_list' => implode(',', $r->concerns_list),
    
                'status' => $r->status,
                'updated_by' => auth()->user()->id,
            ]);

            return redirect()->route('pharmacy_view_patient_list')
            ->with('msg', 'Patient data was updated successfully.')
            ->with('msgtype', 'success');
        }
        else {
            return redirect()->back()
            ->with('msg', 'Error: Patient data already exists in the server.')
            ->with('msgtype', 'warning');
        }
    }

    public function modifyStockPatientView($id) {
        $d = PharmacyPatient::findOrFail($id);

        if(request()->input('meds') || request()->input('alt_meds_id')) {
            if(request()->input('meds')) {
                $meds = request()->input('meds');
            }
            else {
                $meds = request()->input('alt_meds_id');
            }

            if(Str::startsWith($meds, 'SUBSTOCK_')) {
                $newString = Str::replaceFirst("SUBSTOCK_", "", $meds);
                
                $f = PharmacySupplySubStock::where('id', $newString)->first();

                if($f) {
                    if($f->pharmacysub->pharmacy_branch_id == auth()->user()->id) {
                        $get_substock = $f->id;
                    }
                    else {
                        return redirect()->back()
                        ->with('msg', 'Error: You are not authorized to do that.')
                        ->with('msgtype', 'warning');
                    }
                }
                else {

                }
                
                $sku_code = $f->pharmacysub->pharmacysupplymaster->sku_code;
            }
            else {
                $get_substock = NULL;

                $sku_code = $meds;
            }

            $e = PharmacySupplySub::whereHas('pharmacysupplymaster', function ($q) use ($sku_code) {
                $q->where('sku_code', $sku_code);
            })->where('pharmacy_branch_id', auth()->user()->pharmacy_branch_id)
            ->first();

            if($e) {
                //check qty if not equals to Zero
                if($e->pharmacysupplymaster->quantity_type == 'BOX') {
                    if($e->master_box_stock == 0 && $e->master_piece_stock == 0) {
                        return redirect()->back()
                        ->with('msg', 'Error: The Item '.$e->pharmacysupplymaster->name.' in '.$e->pharmacybranch->name.' has no available stock.')
                        ->with('msgtype', 'warning');
                    }
                }
                else {
                    if($e->master_box_stock == 0) {
                        if($e->master_box_stock == 0 && $e->master_piece_stock == 0) {
                            return redirect()->back()
                            ->with('msg', 'Error: The Item '.$e->pharmacysupplymaster->name.' in '.$e->pharmacybranch->name.' has no available stock.')
                            ->with('msgtype', 'warning');
                        }
                    }
                }

                return redirect()->route('pharmacy_modify_view', [
                    $e->id,
                    'process_patient' => $d->id,
                    'get_substock' => $get_substock,
                ]);
            }
            else {

            }
        }

        $meds_list = PharmacySupplySub::where('pharmacy_branch_id', auth()->user()->pharmacy_branch_id)
        ->get();

        return view('pharmacy.modify_stock_patientview', [
            'd' => $d,
            'meds_list' => $meds_list,
        ]);
    }

    public function listBranch() {
        if(request()->input('q')) {
            $search = request()->input('q');

            $list = PharmacyBranch::where('name', 'LIKE', '%'.$search.'%')
            ->paginate(10);
        }
        else {
            $list = PharmacyBranch::orderBy('name', 'ASC')
            ->paginate(10);
        }

        $list_brgy = Brgy::where('city_id', 1)
        ->where('displayInList', 1)
        ->get();
        
        return view('pharmacy.branches_list', [
            'list' => $list,
            'list_brgy' => $list_brgy,
        ]);
    }

    public function storeBranch(Request $r) {
        $s = PharmacyBranch::where('name', mb_strtoupper($r->name))->first();

        if(!($s)) {
            $c = $r->user()->createpharmacybranch()->create([
                'name' => mb_strtoupper($r->name),
                'focal_person' => $r->filled('focal_person') ? mb_strtoupper($r->focal_person) : NULL,
                'contact_number' => $r->filled('contact_number') ? mb_strtoupper($r->contact_number) : NULL,
                'description' => $r->filled('description') ? mb_strtoupper($r->description) : NULL,
                'level' => $r->level,
                'if_bhs_id' => ($r->if_bhs) ? $r->if_bhs_id : NULL,
            ]);

            return redirect()->back()
            ->with('msg', 'New Pharmacy Entity/Branch was added successfully.')
            ->with('msgtype', 'success');
        }
        else {
            return redirect()->back()
            ->withInput()
            ->with('msg', 'Error: Branch Name already exists. Please try again by using a different name.')
            ->with('msgtype', 'warning');
        }
    }

    public function viewBranch($id) {
        $d = PharmacyBranch::findOrFail($id);

        return view('pharmacy.branches_view', [
            'd' => $d,
        ]);
    }

    public function updateBranch($id, Request $r) {
        $d = PharmacyBranch::findOrFail($id);

        $search = PharmacyBranch::where('name', mb_strtoupper($r->name))
        ->first();

        if(!($search)) {
            $d->name = mb_strtoupper($r->name);
            $d->focal_person = ($r->filled('focal_person')) ? $r->focal_person : NULL;
            $d->focal_person = ($r->filled('contact_number')) ? $r->contact_number : NULL;

            return redirect()->route('pharmacy_list_branch')
            ->with('msg', 'Pharmacy Branch (ID: #'.$d->id.') was updated successfully.')
            ->with('msgtype', 'success');
        }
        else {
            return redirect()->back()
            ->with('msg', 'Error: Another Branch with the updated name already exists in the server.')
            ->with('msgtype', 'warning');
        }
    }
}
