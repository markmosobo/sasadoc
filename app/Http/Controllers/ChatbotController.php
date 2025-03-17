<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

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
            $response = $client->post('https://api.replicate.com/v1/predictions', [
                'headers' => [
                    'Authorization' => "Token $apiToken",
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'version' => 'replicate/meta/llama-2-7b-chat',  // Adjust this to match your model version
                    'input' => ['prompt' => $userMessage],
                ],
            ]);

            $body = json_decode($response->getBody(), true);
            return response()->json(['reply' => $body['output'] ?? 'Sorry, I am unable to process your request.']);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch response'], 500);
        }
    }
}
