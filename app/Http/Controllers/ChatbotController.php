<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class ChatbotController extends Controller
{
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
                    'version' => 'meta/meta-llama-3-8b-instruct',
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
