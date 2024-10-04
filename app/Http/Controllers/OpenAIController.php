<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use JetBrains\PhpStorm\NoReturn;

class OpenAIController extends Controller
{
    public function askQuestion(Request $request){
        $question = $request->input('question');

        // Valida que haya una pregunta
        if (!$question) {
            return response()->json(['error' => 'No question provided'], 400);
        }

        // Configura el cliente de Guzzle
        $client = new Client();
        $apiKey = env('OPENAI_API_KEY'); // Llave desde .env

        // Hace la solicitud a la API de OpenAI
        try {
            $response = $client->post('https://api.openai.com/v1/chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'gpt-3.5-turbo',  // Cambia a gpt-4 si tienes acceso
                    'messages' => [
                        ['role' => 'system', 'content' => 'You are a helpful assistant.'], // Contexto del sistema
                        ['role' => 'user', 'content' => $question] // Pregunta del usuario
                    ],
                    'max_tokens' => 150,  // Ajusta el número de tokens según lo necesites
                ]
            ]);

            $responseBody = json_decode($response->getBody(), true);
            return response()->json([
                'answer' => $responseBody['choices'][0]['message']['content'] ?? 'No answer found',
            ]);
//            return back()->with('success', $responseBody['choices'][0]['text'] ?? 'No hay respuesta para eso');

        } catch (\Exception $th) {
            $mensajito = $th->getMessage() . ' L:' . $th->getLine() . ' Ubi: ' . $th->getFile();
            return back()->with('error', __('app.label.deleted_error', ['name' => 'Alejo']) . $mensajito);
        }
    }
}
