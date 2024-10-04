<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Inertia\Inertia;
use JetBrains\PhpStorm\NoReturn;

class OpenAIController extends Controller
{
    public function askQuestion(Request $request){
        $question = $request->input('question');
        // Validar que haya una pregunta
        if (!$question) {
            return Inertia::render('aski', [
                'error' => 'Por favor ingrese una pregunta.'
            ]);
        }

        // Configurar el cliente de Guzzle para llamar a OpenAI
        $client = new Client();
        $apiKey = env('OPENAI_API_KEY');

        try {
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo', // O el modelo que estés usando
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'],
                        ['role' => 'user', 'content' => $question],
                    ],
                    'max_tokens' => 415,
                ]
            ]);

            $responseBody = json_decode($response->getBody(), true);
            $answer = $responseBody['choices'][0]['message']['content'] ?? 'No se encontró una respuesta.';
dd($answer);
            return Inertia::render('aski', [
                'answer' => $answer
            ]);
//            return back()->with('aski', [
//                'answer' => $answer
//            ]);

//            return response()->json([
//                'answer' => $responseBody['choices'][0]['message']['content'] ?? 'No answer found',
//            ]);
//            return back()->with('success', $responseBody['choices'][0]['text'] ?? 'No hay respuesta para eso');

        } catch (\Exception $th) {
            $mensajito = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            return back()->with('error', __('app.label.deleted_error', ['name' => 'Alejo']) . $mensajito);
        }
    }
}
