<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Inertia\Inertia;
use JetBrains\PhpStorm\NoReturn;

class OpenAIController extends Controller
{
    public function askQuestion(Request $request)
    {
        try {
            $question = $request->question;
            return Inertia::render('aski', [
                'answer' => '',
//                'answer' => (fn() => $this->contestar($question)),
                // 'answer' => Inertia::lazy(fn() => $this->contestar($question)),
            ]);

        } catch (\Exception $th) {
            $mensajito = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            return back()->with('error', __('app.label.deleted_error', ['name' => 'Alejo']) . $mensajito);
        }
    }

    public function contestar($question)
    {
        if(!$question) return ' ';
        // Configurar el cliente de Guzzle para llamar a OpenAI
        $client = new Client();
        $apiKey = env('OPENAI_API_KEY');
        $response = $client->post('https://api.openai.com/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                    ['role' => 'user', 'content' => $question],
                ],
                'max_tokens' => 10,
            ]
        ]);

        $responseBody = json_decode($response->getBody(), true);
        return $responseBody['choices'][0]['message']['content'] ?? 'No se encontró una respuesta a: '.$question;
    }
}
