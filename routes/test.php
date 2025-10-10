<?php

use Illuminate\Support\Facades\Route;
use App\Services\OpenAIService;

Route::get('/test-ai', function () {
    try {
        $openAIService = new OpenAIService();
        
        // Simple test call
        $response = $openAIService->callOpenAI([
            'model' => 'gpt-4o-mini',
            'messages' => [
                [
                    'role' => 'user',
                    'content' => 'Say "Hello, AI is working!" in one sentence.'
                ]
            ],
            'max_tokens' => 50,
            'temperature' => 0.1,
        ]);
        
        $responseData = json_decode($response, true);
        
        if (isset($responseData['choices'][0]['message']['content'])) {
            return response()->json([
                'success' => true,
                'message' => $responseData['choices'][0]['message']['content'],
                'usage' => $responseData['usage'] ?? null
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'Invalid response format',
                'raw_response' => $responseData
            ]);
        }
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
});