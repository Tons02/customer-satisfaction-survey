<?php

namespace App\Http\Controllers\Api;

use App\Response\Message;
use App\Models\TriggerSetUp;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Controllers\Controller;
use Essa\APIToolKit\Api\ApiResponse;
use App\Http\Requests\TriggerSetUpRequest;

class TriggerSetUpController extends Controller
{
    use ApiResponse;
    
    public function index(Request $request){
        $status = $request->query('status');
        
        $TriggerSetUp = TriggerSetUp::
        when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
        ->orderBy('created_at', 'desc')
        ->useFilters()
        ->dynamicPaginate();
        
        $is_empty = $TriggerSetUp->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND);
        }
            // TriggerSetUpResource::collection($TriggerSetUp);
            return GlobalFunction::response_function(Message::TRIGGER_DISPLAY,$TriggerSetUp);
    }

    public function store(TriggerSetUpRequest $request) {

        if (TriggerSetUp::count()){
            return GlobalFunction::response_function(Message::INVALID_ACTION);
        }

        $create_trigger = TriggerSetUp::create([
            "trigger_point" => $request->trigger_point,
            "limit" => $request->limit,
            "total" => $request->total,
        ]);

        return GlobalFunction::response_function(Message::TRIGGER_SAVE);
    }

    public function update(TriggerSetUpRequest $request, $id) {
        $triggger_id = TriggerSetUp::find($id);

        if (!$triggger_id) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        $triggger_id->trigger_point = $request['trigger_point'];
        $triggger_id->limit = $request['limit'];
        $triggger_id->total = $request['total'];

        if (!$triggger_id->isDirty()) {
            return GlobalFunction::response_function(Message::NO_CHANGES);
        }

        $triggger_id->save();
        
        return GlobalFunction::response_function(Message::TRIGGER_UPDATE);
    }

    public function archived(Request $request, $id)
    {
        $triggerSetUp = TriggerSetUp::withTrashed()->find($id);
        if (!$triggerSetUp) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        if ($triggerSetUp->deleted_at) {
            $triggerSetUp->update([
                'is_active' => 1
            ]);
            $triggerSetUp->restore();
            return GlobalFunction::response_function(Message::RESTORE_STATUS);
        }

        if (!$triggerSetUp->deleted_at) {
            $triggerSetUp->update([
                'is_active' => 0
            ]);
            $triggerSetUp->delete();
            return GlobalFunction::response_function(Message::ARCHIVE_STATUS);

        } 
    }
}
