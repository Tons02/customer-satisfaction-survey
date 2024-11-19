<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Response\Message;
use Illuminate\Http\Request;
use App\Functions\GlobalFunction;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use Essa\APIToolKit\Api\ApiResponse;

class UserController extends Controller
{
    use ApiResponse;

    public function index(Request $request){
        $this->authorize('viewany', User::class);

        $status = $request->query('status');
 
        $users = User::
        when($status === "inactive", function ($query) {
            $query->onlyTrashed();
        })
        ->orderBy('created_at', 'desc')
        ->UseFilters()
        ->dynamicPaginate();
        
        $is_empty = $users->isEmpty();

        if ($is_empty) {
            return GlobalFunction::response_function(Message::NOT_FOUND, $users);
        }
            UserResource::collection($users); 
            return GlobalFunction::response_function(Message::USER_DISPLAY, $users);
    }

    public function store(UserRequest $request){
        
        $this->authorize('create', User::class);
        $create_user = User::create([
            "id_prefix" => $request["personal_info"]["id_prefix"],
            "id_no" => $request["personal_info"]["id_no"],
            "first_name" => $request["personal_info"]["first_name"],
            "middle_name" => $request["personal_info"]["middle_name"],
            "last_name" => $request["personal_info"]["last_name"],
            "contact_details" => $request["personal_info"]["contact_details"],
            "sex" => $request["personal_info"]["sex"],
            
            "company_id" => $request["personal_info"]["company_id"],
            "company" => $request["personal_info"]["company"],

            "business_unit_id" => $request["personal_info"]["business_unit_id"],
            "business_unit" => $request["personal_info"]["business_unit"],

            "department_id" => $request["personal_info"]["department_id"],
            "department" => $request["personal_info"]["department"],

            "unit_id" => $request["personal_info"]["unit_id"],
            "unit" => $request["personal_info"]["unit"],

            "sub_unit_id" => $request["personal_info"]["sub_unit_id"],
            "sub_unit" => $request["personal_info"]["sub_unit"],
            
            "location_id" => $request["personal_info"]["location_id"],
            "location" => $request["personal_info"]["location"],

            "province_id" => $request["personal_info"]["province_id"],
            "store_id" => $request["personal_info"]["store_id"],

            "username" => $request["username"],
            "password" => $request["username"],

            "role_id" => $request["role_id"],
            
            "user_type" => $request["user_type"],

        ]);

        return GlobalFunction::save(Message::USER_SAVE);
        
    }

    public function update(UserRequest $request, $id) {

        $userID = User::with("role")->find($id);

        if (!$userID) {
            return GlobalFunction::response_function(Message::NOT_FOUND, $users);
        }

        $this->authorize('update', $userID);

        $userID->contact_details = $request["personal_info"]["contact_details"];
        $userID->username = $request['username'];
        $userID->role_id = $request['role_id'];
        
        $userID->company_id = $request["personal_info"]["company_id"];
        $userID->company = $request["personal_info"]["company"];
        
        $userID->business_unit_id = $request["personal_info"]["business_unit_id"];
        $userID->business_unit = $request["personal_info"]["business_unit"];

        $userID->department_id = $request["personal_info"]["department_id"];
        $userID->department = $request["personal_info"]["department"];

        $userID->unit_id = $request["personal_info"]["unit_id"];
        $userID->unit = $request["personal_info"]["unit"];

        $userID->sub_unit_id = $request["personal_info"]["sub_unit_id"];
        $userID->sub_unit = $request["personal_info"]["sub_unit"];

        
        $userID->location_id = $request["personal_info"]["location_id"];
        $userID->location = $request["personal_info"]["location"];

        
        $userID->province_id = $request["personal_info"]["province_id"];
        $userID->store_id = $request["personal_info"]["store_id"];

        $userID->user_type = $request["user_type"];

        if (!$userID->isDirty()) {
            return GlobalFunction::response_function(Message::NO_CHANGES);
        }

        $userID->save();
       
        return GlobalFunction::response_function(Message::USER_UPDATE);
    }

    public function archived(Request $request, $id){
        
        if($id == auth('sanctum')->user()->id){
            return response()->json(['message' => 'Unable to Archive, User already in used!'],409);
        }

        $invalid_id = User::where("id", $id)
            ->withTrashed()
            ->get();

        if ($invalid_id->isEmpty()) {
            return GlobalFunction::not_found(Message::NOT_FOUND);
        }
        $user = User::withTrashed()->find($id);
        $is_active = User::withTrashed()
            ->where("id", $id)
            ->first();
        if (!$is_active) {
            return $is_active;
        } elseif (!$is_active->deleted_at) {
            
            $this->authorize('delete', $user);

            $user->update([
                'is_active' => 0
            ]);
            $user->delete();
            return GlobalFunction::response_function(Message::ARCHIVE_STATUS);
        } else {
            $this->authorize('restore', $user);

            $user->update([
                'is_active' => 1
            ]);
            $user->restore();
            return GlobalFunction::response_function(Message::RESTORE_STATUS);
        }

    }
}
