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

    public function store(Request $request){

        $create_user = User::create([
            "first_name" => $request["first_name"],
            "middle_name" => $request["middle_name"],
            "last_name" => $request["last_name"],
            "suffix" => $request["suffix"],
            "mobile_number" => $request["mobile_number"],
            "gender" => $request["gender"],
            "age" => $request["age"],

            "username" => $request["username"],
            "password" => $request["username"],

            "role_id" => $request["role_id"],

        ]);

        return GlobalFunction::save(Message::USER_SAVE, $create_user);
        
    }

    public function update(UserRequest $request, $id) {

        $userID = User::with("role")->find($id);

        if (!$userID) {
            return response()->json([
                'status_code' => "404",
                'message' => "User not found"
                ], 404);
        }

        $userID->update([
            "username" => $request["username"],
            "role_id" => $request["role_id"],
            "form_template_id" => $request["form_template_id"]
        ]);
       
        return GlobalFunction::response_function(Message::USER_UPDATE, $userID);
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
            $user->update([
                'is_active' => 0
            ]);
            $user->delete();
            return GlobalFunction::response_function(Message::ARCHIVE_STATUS);
        } else {
            $user->update([
                'is_active' => 1
            ]);
            $user->restore();
            return GlobalFunction::response_function(Message::RESTORE_STATUS);
        }

    }
}
