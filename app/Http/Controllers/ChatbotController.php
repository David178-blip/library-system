<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;

class ChatbotController extends Controller {
    public function chat(Request $request) {
        $request->validate(['message'=>'required']);
        
        $client = new Client();
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers'=>[
                'Authorization'=>'Bearer '.env('OPENAI_API_KEY'),
                'Content-Type'=>'application/json',
            ],
            'json'=>[
                'model'=>'gpt-3.5-turbo',
                'messages'=>[
                    ['role'=>'system','content'=>'You are a helpful library assistant.'],
                    ['role'=>'user','content'=>$request->message]
                ],
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        $reply = $data['choices'][0]['message']['content'] ?? 'Sorry, I could not respond.';

        return response()->json(['reply'=>$reply]);
    }
}
