<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgetMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use mysql_xdevapi\Exception;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['error' => 'Invalid Email Or Password'], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        // Revoke current access token
        $request->user()->token()->revoke(); //set flag
//        $request->user()->token()->delete(); // delete one token
//        $request->user()->tokens()->delete(); // delete all device token

        return response()->json([
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $email = $request->email;
        if (User::where('email', $email)->doesntExist()) {
            return response([
                'message' => "Email Is Invalid"
            ], 401);
        }
        $token = rand(10, 100000);
        try {
            $existing = DB::table('password_reset_tokens')->where('email', $email)->first();

            if ($existing) {
                $cooldownMinutes = 5;

                if (Carbon::parse($existing->created_at)->addMinutes($cooldownMinutes)->isFuture()) {
                    return response()->json([
                        'message' => "Please wait $cooldownMinutes minutes before requesting another reset link."
                    ], 429);
                }
            }
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $email],
                [
                    'token' => $token,
                    'created_at' => now(),
                ]
            );
        } catch (\Exception $e) {
            return response([
                'message' => $e->getMessage(),
            ], 400);
        }
        try {
            Mail::to($email)->send(new ForgetMail($token));
            return response()->json(['message' => 'Password reset link sent to your email.'], 200);

        }catch (\Exception $ex){
            return response()->json(['message' => 'Unable to send reset link.'], 500);
        }
    }

    public function resetPassword(Request $request)
    {
        // 1. Validate input
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // 2. Find token record
        $tokenData = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$tokenData) {
            return response()->json(['message' => 'Invalid token or email.'], 400);
        }

        // 3. Check if token is expired (optional: expires after 2 minutes)
        if (Carbon::parse($tokenData->created_at)->addMinutes(2)->isPast()) {
            return response()->json(['message' => 'Token expired.'], 400);
        }

        // 4. Update user's password
        $user = User::where('email', $request->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();

        // 5. Delete used token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password reset successfully.'], 200);
    }
}
