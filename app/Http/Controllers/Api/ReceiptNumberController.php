<?php

namespace App\Http\Controllers\Api;

use App\Response\Message;
use App\Models\TriggerSetUp;
use Illuminate\Http\Request;
use App\Models\ReceiptNumber;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\ReceiptNumberRequest;
use App\Http\Resources\ReceiptNumberResource;

class ReceiptNumberController extends Controller
{
    use ApiResponse;
    
    public function index(Request $request)
    {   
        $status = $request->query('status');
        
        $ReceiptNumber = ReceiptNumber::
        when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
        ->orderBy('created_at', 'desc')
        ->useFilters()
        ->dynamicPaginate();
        
        $is_empty = $ReceiptNumber->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND);
        }
            ReceiptNumberResource::collection($ReceiptNumber);
            return GlobalFunction::response_function(Message::RECEIPT_NUMBER_DISPLAY_DISPLAY,$ReceiptNumber);

    }

    public function store(ReceiptNumberRequest $request)
    {      
        $storeId = $request->store_id;

        // Count the total receipt numbers for the given store_id
        $count = ReceiptNumber::where('store_id', $storeId)->count();

        // Get the limit and trigger point from the TriggerSetup model
        $triggerSetup = TriggerSetUp::first();
        $limit = $triggerSetup->limit; 
        $trigger_point = $triggerSetup->trigger_point; 

        // Calculate valid trigger points up to the limit
         $validTriggerPoints = range($trigger_point, $limit, $trigger_point);

        // Check if the count exceeds the limit
        if ($count > $limit) {
            return GlobalFunction::response_function(Message::RECEIPT_NUMBER_LIMIT);
        } else {
            // Check if the current count is a valid trigger point
             $isValid = in_array($count+1, $validTriggerPoints);
        
        }

        $create_role = ReceiptNumber::create([
            "receipt_number" => $request->receipt_number,
            "contact_details" => $request->contact_details,
            "store_id" => $request->store_id,
            "is_valid" => $isValid
        ]);

        return GlobalFunction::response_function(Message::RECEIPT_NUMBER_SAVE);
        
    }

    public function update(ReceiptNumberRequest $request, $id)
    {   
        $receipt = ReceiptNumber::find($id);

        if (!$receipt) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        $receipt->receipt_number = $request['receipt_number'];
        $receipt->contact_details = $request['contact_details'];
        $receipt->store_id = $request['store_id'];

        if (!$receipt->isDirty()) {
            return GlobalFunction::response_function(Message::NO_CHANGES);
        }

        $receipt->save();
        
        return GlobalFunction::response_function(Message::RECEIPT_NUMBER_UPDATE);
    }

}
