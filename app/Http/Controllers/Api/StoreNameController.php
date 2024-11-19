<?php

namespace App\Http\Controllers\Api;

use App\Models\StoreName;
use App\Models\Province;
use App\Response\Message;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\StoreNameRequest;
use App\Http\Resources\StoreNameResource;

class StoreNameController extends Controller
{
    use ApiResponse;
    
    public function getAllStoreNames(Request $request)
    {   
        $status = $request->query('status');
        
        $StoreName = StoreName::
        when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
        ->when($status === "survey", function ($query) {
            $query->withTrashed();
        })
        ->orderBy('created_at', 'desc')
        ->useFilters()
        ->dynamicPaginate();
        
        $is_empty = $StoreName->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND);
        }
            StoreNameResource::collection($StoreName);
           $ResultStoreName = $request->query('pagination') == 'none' 
            ? ['data' => StoreNameResource::collection($StoreName)] 
            : $StoreName;

            return GlobalFunction::response_function(Message::STORE_NAME_DISPLAY,$ResultStoreName);

    }

    public function store(StoreNameRequest $request)
    {

        $create_store_name = StoreName::create([
            "province_id" => $request->province_id,
            "name" => $request->name,
            "address" => $request->address
        ]);

        return GlobalFunction::response_function(Message::STORE_NAME_SAVE);
        
    }

    public function update(StoreNameRequest $request, $id)
    {   
        $store_name_id = StoreName::find($id);

        if (!$store_name_id) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        $store_name_id->province_id = $request['province_id'];
        $store_name_id->name = $request['name'];
        $store_name_id->address = $request['address'];
        

        if (!$store_name_id->isDirty()) {
            return GlobalFunction::response_function(Message::NO_CHANGES);
        }

        $store_name_id->save();
        
        return GlobalFunction::response_function(Message::STORE_NAME_UPDATE);
    }

    public function archived(Request $request, $id)
    {
        $store_name = StoreName::withTrashed()->find($id);
        // return $store_name
        if (!$store_name) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        if ($store_name->deleted_at) {

            if (Province::withTrashed()->where('id', $store_name->province_id)->whereNotNull('deleted_at')->exists()) {
                return GlobalFunction::invalid(Message::PROVINCE_ID_INVALID );
            }

            $store_name->update([
                'is_active' => 1
            ]);
            $store_name->restore();
            return GlobalFunction::response_function(Message::RESTORE_STATUS);
        }

        if (!$store_name->deleted_at) {
            $store_name->update([
                'is_active' => 0
            ]);
            $store_name->delete();
            return GlobalFunction::response_function(Message::ARCHIVE_STATUS);

        } 
    }
}
