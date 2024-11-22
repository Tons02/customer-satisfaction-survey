<?php

namespace App\Http\Controllers\Api;

use App\Models\Role;
use App\Models\User;
use App\Response\Message;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Requests\RoleRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\RoleResource;
use Essa\APIToolKit\Api\ApiResponse;

class RoleController extends Controller
{
    use ApiResponse;
    
    public function index(Request $request)
    {   

        $status = $request->query('status');
        
        $Role = Role::
        when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
        ->orderBy('created_at', 'desc')
        ->useFilters()
        ->dynamicPaginate();
        
        $is_empty = $Role->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND);
        }
            RoleResource::collection($Role);
            return GlobalFunction::response_function(Message::ROLE_DISPLAY,$Role);

    }

    public function store(RoleRequest $request)
    {

        $create_role = Role::create([
            "name" => $request->name,
            "access_permission" => $request->access_permission,
        ]);

        return GlobalFunction::response_function(Message::ROLE_SAVE);
        
    }

    public function update(RoleRequest $request, $id)
    {   
        $role_id = Role::find($id);

        if (!$role_id) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }


        $role_id->name = $request['name'];
        $role_id->access_permission = $request['access_permission'];

        if (!$role_id->isDirty()) {
            return GlobalFunction::response_function(Message::NO_CHANGES);
        }

        $role_id->save();
        
        return GlobalFunction::response_function(Message::ROLE_UPDATE);
    }

    public function archived(Request $request, $id)
    {
        $role = Role::withTrashed()->find($id);
        // return $role
        if (!$role) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        
        if ($role->deleted_at) {
            $this->authorize('restore', $role);

            $role->update([
                'is_active' => 1
            ]);
            $role->restore();
            return GlobalFunction::response_function(Message::RESTORE_STATUS);
        }

        if (User::where('role_id', $id)->exists()) {
            return GlobalFunction::invalid(Message::ROLE_ALREADY_USE);
        }

        if (!$role->deleted_at) {
            $this->authorize('delete', $role);

            $role->update([
                'is_active' => 0
            ]);
            $role->delete();
            return GlobalFunction::response_function(Message::ARCHIVE_STATUS);

        } 
    }
}
