<?php

namespace App\Http\Controllers\Api;

use App\Models\Province;
use App\Response\Message;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\ProvinceRequest;
use App\Http\Resources\ProvinceResource;

class ProvinceController extends Controller
{
    use ApiResponse;
    
    public function index(Request $request)
    {   
        $status = $request->query('status');
        
        $Province = Province::
        when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
        ->orderBy('created_at', 'desc')
        ->useFilters()
        ->dynamicPaginate();
        
        $is_empty = $Province->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND);
        }
            ProvinceResource::collection($Province);
            return GlobalFunction::response_function(Message::PROVINCE_DISPLAY,$Province);

    }

    public function store(ProvinceRequest $request)
    {

        $create_province = Province::create([
            "name" => $request->name,
        ]);

        return GlobalFunction::response_function(Message::PROVINCE_SAVE);
        
    }

    public function update(ProvinceRequest $request, $id)
    {   
        $province_id = Province::find($id);

        if (!$province_id) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }

        $province_id->name = $request['name'];

        if (!$province_id->isDirty()) {
            return GlobalFunction::response_function(Message::NO_CHANGES);
        }

        $province_id->save();
        
        return GlobalFunction::response_function(Message::PROVINCE_UPDATE);
    }

    public function archived(Request $request, $id)
    {
        $province = Province::withTrashed()->find($id);
        // return $province
        if (!$province) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        if ($province->deleted_at) {
            $province->update([
                'is_active' => 1
            ]);
            $province->restore();
            return GlobalFunction::response_function(Message::RESTORE_STATUS);
        }

        // if (User::where('province_id', $id)->exists()) {
        //     return GlobalFunction::invalid(Message::PROVINCE_ALREADY_USE);
        // }

        if (!$province->deleted_at) {
            $province->update([
                'is_active' => 0
            ]);
            $province->delete();
            return GlobalFunction::response_function(Message::ARCHIVE_STATUS);

        } 
    }
}