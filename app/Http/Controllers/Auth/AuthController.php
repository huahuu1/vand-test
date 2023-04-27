<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\UserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    protected $userService;

    /**
     * AuthController constructor.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Register api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        try {
            User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request['password']),
            ]);

            return response()->json([
                'message' => __('authCustom.register.success'),
            ], 200);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json([
                'message' => __('authCustom.register.fail'),
                'error' => $e,
            ], 400);
        }
    }

    /**
     * Login api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        try {
            $user = $this->userService->getUserByUsernameOrEmail($request->email ?? $request->username);
            
            if (!$user || !Hash::check($request->password, $user->password, [])) {
                return response()->json([
                    'message' => __('authCustom.login.login_invalid'),
                ], 422);
            }

            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'data' => $user,
            ], 200);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json([
                'message' => __('authCustom.login.fail'),
                'error' => $e,
            ], 400);
        }
    }

    /**
     * Logout api
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            $id = $request->user()->currentAccessToken()->id;
            Auth::user()->tokens()->where('id', $id)->delete();
            return response()->json([
                'message' => __('authCustom.logout.success'),
            ], 200);
        } catch (Exception $e) {
            Log::error($e);

            return response()->json([
                'error' => $e,
            ], 400);
        }
    }
}
