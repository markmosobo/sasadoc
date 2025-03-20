<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ChatbotController extends Controller
{

     // User Registration
     public function register(Request $request)
     {
         $request->validate([
             'first_name' => 'required|string|max:255',
             'last_name' => 'required|string|max:255',
             'email' => 'required|string|email|max:255|unique:users',
             'password' => 'required|string|min:6|confirmed',
         ]);
 
         $user = User::create([
             'first_name' => $request->first_name,
             'last_name' => $request->last_name,
             'email' => $request->email,
             'password' => Hash::make($request->password),
         ]);
 
         $token = $user->createToken('auth_token')->plainTextToken;
 
         return response()->json(['message' => 'User registered successfully', 'token' => $token], 201);
     }
 
     // User Login
     public function login(Request $request)
     {
         $request->validate([
             'email' => 'required|email',
             'password' => 'required',
         ]);
 
         if (!Auth::attempt($request->only('email', 'password'))) {
             return response()->json(['error' => 'Invalid credentials'], 401);
         }
 
         $user = Auth::user();
         $token = $user->createToken('auth_token')->plainTextToken;
 
         return response()->json(['message' => 'Login successful', 'token' => $token], 200);
     }

      // Logout Function
    public function logout(Request $request)
    {
        // Revoke the user's token
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function chat(Request $request)
    {
        $userMessage = $request->input('message');
    
        if (!$userMessage) {
            return response()->json(['error' => 'Message is required'], 400);
        }
    
        $client = new Client();
        $apiToken = env('REPLICATE_API_TOKEN');
    
        try {
            // Step 1: Send Request
            $response = $client->post('https://api.replicate.com/v1/predictions', [
                'headers' => [
                    'Authorization' => "Token $apiToken",
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'version' => 'meta/meta-llama-3-70b-instruct',
                    'input'   => ['prompt' => $userMessage],
                ],
            ]);
    
            $body = json_decode($response->getBody(), true);
            Log::info('Replicate API Initial Response:', $body);
    
            // Step 2: Poll for Completion
            $getUrl = $body['urls']['get'] ?? null;
            if (!$getUrl) {
                return response()->json(['error' => 'Failed to start AI response'], 500);
            }
    
            for ($i = 0; $i < 10; $i++) {
                sleep(2);
    
                $pollResponse = $client->get($getUrl, [
                    'headers' => ['Authorization' => "Token $apiToken"],
                ]);
                $pollBody = json_decode($pollResponse->getBody(), true);
                Log::info('Replicate API Polling Response:', $pollBody);
    
                if ($pollBody['status'] === 'succeeded' && isset($pollBody['output'])) {
                    return response()->json(['reply' => $pollBody['output']]);
                }
            }
    
            return response()->json(['error' => 'AI response took too long'], 500);
    
        } catch (\Exception $e) {
            Log::error('Replicate API Error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch response'], 500);
        }
    }
        

}
