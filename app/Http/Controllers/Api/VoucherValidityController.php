<?php

namespace App\Http\Controllers\Api;

use App\Response\Message;
use Illuminate\Http\Request;
use App\Models\VoucherValidity;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use App\Http\Requests\VoucherValidityRequest;
use App\Http\Resources\VoucherValidityResource;

class VoucherValidityController extends Controller
{
    
    public function index(Request $request)
    {   
        $status = $request->query('status');
        
        $VoucherValidity = VoucherValidity::
        get();
        
        $is_empty = $VoucherValidity->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND, $VoucherValidity);
        }
            VoucherValidityResource::collection($VoucherValidity);
            return GlobalFunction::response_function(Message::VOUCHER_VALIDITY_DISPLAY, $VoucherValidity);

    }

    public function store(VoucherValidityRequest $request)
    {   
        if (VoucherValidity::count()){
            return GlobalFunction::response_function(Message::ALREADY_EXIST);
        }

        $CreateVoucherValidity = VoucherValidity::create([
            "name" => $request->name,
            "duration" => $request->duration,
        ]);

        return GlobalFunction::response_function(Message::VOUCHER_VALIDITY_SAVE, $CreateVoucherValidity);
        
    }
    
    public function update(VoucherValidityRequest $request, $id)
    {   
        $VoucherValidityId = VoucherValidity::find($id);

        if (!$VoucherValidityId) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        $VoucherValidityId->update([
            "name" => $request->name,
            "duration" => $request->duration,
        ]);
        
        return GlobalFunction::response_function(Message::VOUCHER_VALIDITY_UPDATE, $VoucherValidityId);
    }

    public function archived(Request $request, $id)
    {
        $VoucherValidityId = VoucherValidity::withTrashed()->find($id);

        if (!$VoucherValidityId) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        if ($VoucherValidityId->deleted_at) {
            $VoucherValidityId->update([
                'is_active' => 1
            ]);
            $VoucherValidityId->restore();
            return GlobalFunction::response_function(Message::RESTORE_STATUS, $VoucherValidityId);
        }

        if (!$VoucherValidityId->deleted_at) {
            $VoucherValidityId->update([
                'is_active' => 0
            ]);
            $VoucherValidityId->delete();
            return GlobalFunction::response_function(Message::ARCHIVE_STATUS, $VoucherValidityId);

        } 
    }
}
