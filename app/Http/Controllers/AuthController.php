<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /***
     * AuthController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /***
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => 'required',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /***
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|between:2,100',
            'phone_number' => 'required|unique:users',
            'password' => 'required|string|confirmed|min:6',
            'permanent_latitude' => 'required',
            'permanent_longitude' => 'required',
            'student_name' => 'required_if:user_type,' . User::PARENT,
            'standard' => 'required_if:user_type,' . User::PARENT,
            'user_type' => 'required|in:' . User::PARENT . ',' . User::DRIVER . ',' . User::ADMIN,
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $d = $request->all();
        $d['password'] = bcrypt($request->password);
        $user = User::create($d);

        return response()->json([
            'message' => 'User successfully registered',
            'user' => $user
        ], 201);
    }

    /***
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /***
     * @return mixed
     */
    public function refresh()
    {
        return $this->createNewToken(auth()->refresh());
    }

    /***
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(auth()->user());
    }

    /***
     * @param $token
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            'user' => auth()->user()
        ]);
    }

    public function getParents(Request $request)
    {
        $parents = User::getParents();
        return response()->json(['message' => '', 'parents' => $parents]);
    }

    public function getDrivers(Request $request)
    {
        $drivers = User::getDrivers();
        return response()->json(['message' => '', 'drivers' => $drivers]);
    }

    public function assignDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'driver_id' => 'required|exists:users,id',
            'parent_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $params = ['driver_id' => $request->driver_id];
        $user = User::Edit($request->parent_id, $params);
        return response()->json(['message' => '', 'user' => $user]);
    }

    public function trackingUpdate(Request $request)
    {
        $authUser = Auth::id();
        $validator = Validator::make($request->all(), [
            'latitude' => 'required',
            'longitude' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $params = ['latitude' => $request->latitude, 'longitude' => $request->longitude];
        $user = User::Edit($authUser, $params);
        return response()->json(['message' => '', 'user' => $user]);
    }

    public function getDriverTracking(Request $request)
    {
        $authUser = Auth::id();
        $driver = User::getDriverDetail($authUser);
        return response()->json(['message' => '', 'user' => $driver]);
    }

    public function updateArrivingTime(Request $request)
    {
        $authUser = Auth::id();
        $validator = Validator::make($request->all(), [
            'arriving_time' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $params = ['arriving_time' => $request->arriving_time];
        $user = User::Edit($authUser, $params);
        return response()->json(['message' => '', 'user' => $user]);
    }

    public function updateDismissal(Request $request)
    {
        $authUser = Auth::id();
        $validator = Validator::make($request->all(), [
            'is_dismissal' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $params = ['is_dismissal' => $request->is_dismissal, 'dismissal_note' => $request->dismissal_note];
        $user = User::Edit($authUser, $params);
        return response()->json(['message' => '', 'user' => $user]);
    }

    public function deleteUser(Request $request, $id)
    {
        $data = ['id' => $id];
        $validator = Validator::make($data, [
            'id' => 'required|exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }
        $params = ['driver_id' => null];
        User::Edit($id, $params);
        return response()->json(['message' => 'User updated successfully']);
    }
}
