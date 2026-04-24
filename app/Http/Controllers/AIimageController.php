<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Tournament;

class AIimageController extends Controller
{
    private $openaiKey;

    public function __construct()
    {
        $this->openaiKey = env('OPENAI_API_KEY');
    }

    public function analyze(Request $request)
    {
        $file = $request->file('image');

        if (!$file) {
            return response()->json([
                'error' => 'No image provided'
            ], 400);
        }

        // compress image
        $compressedImage = $this->compressImage($file);
        $base64 = base64_encode($compressedImage);

        // AI request
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->openaiKey,
            'Content-Type' => 'application/json'
        ])->post('https://api.openai.com/v1/chat/completions', [
            "model" => "gpt-4o-mini",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => "You are a strict JSON generator.

Extract data from this certificate image and return ONLY valid JSON.

Allowed values:
certificateType must be EXACTLY one of:
- Participation Certificate
- Achievement Certificate

Fields:
- certificateType (string or null)
- tournamentName (string or null)
- tournamentdate (string or null)

Rules:
- Return ONLY JSON
- No explanation
- No markdown
- If unsure return null

Example:
{
  \"certificateType\": \"Participation Certificate\",
  \"tournamentName\": \"ABC Tournament\",
  \"tournamentdate\": \"2024-01-01\"
}"
                        ],
                        [
                            "type" => "image_url",
                            "image_url" => [
                                "url" => "data:image/jpeg;base64," . $base64
                            ]
                        ]
                    ]
                ]
            ],
            "temperature" => 0
        ]);

         if (!$response->successful()) {
    return response()->json([
        'error' => 'OpenAI request failed',
        'status' => $response->status(),
        'body' => $response->body()
       ], 500);
      }

        $responseData = $response->json();

        if (!isset($responseData['choices'][0]['message']['content'])) {
            return response()->json([
                'error' => 'Invalid AI response',
                'raw' => $responseData
            ], 500);
        }

        // decode AI JSON
        $content = $responseData['choices'][0]['message']['content'];
        $data = json_decode($content, true);

        if (!$data) {
            return response()->json([
                'error' => 'Failed to decode AI JSON',
                'raw' => $content
            ], 500);
        }

        // 🔥 sanitize certificate type
        $allowedTypes = [
            'Participation Certificate',
            'Achievement Certificate'
        ];

        $type = $data['certificateType'] ?? null;

        if (!in_array($type, $allowedTypes)) {
            $type = null;
        }

        // save to DB
        $tournament = Tournament::create([
            'certificateType' => $type,
            'tournamentName' => $data['tournamentName'] ?? null,
            'tournamentdate' => $data['tournamentdate'] ?? null,
            'rank' => $request->input('rank'),
            'player_id' => auth()->id(),
        ]);

        return response()->json([
            'data' => $tournament
        ]);
    }

    // compress image
    private function compressImage($file)
    {
        $image = imagecreatefromstring(file_get_contents($file));

        $width = imagesx($image);
        $height = imagesy($image);

        $newWidth = 800;
        $newHeight = ($height / $width) * $newWidth;

        $tmp = imagecreatetruecolor($newWidth, $newHeight);

        imagecopyresampled($tmp, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($tmp, null, 70);
        $compressed = ob_get_clean();

        imagedestroy($image);
        imagedestroy($tmp);

        return $compressed;
    }
}