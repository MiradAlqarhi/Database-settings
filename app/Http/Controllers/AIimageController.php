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
        if (!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $file = $request->file('image');

        if (!$file) {
            return response()->json([
                'error' => 'No image provided'
            ], 400);
        }

        try {
            // compress image
            $compressedImage = $this->compressImage($file);
            $base64 = base64_encode($compressedImage);

            // AI request
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->openaiKey,
                'Content-Type' => 'application/json'
            ])->post('https://api.openai.com/v1/chat/completions', [
                "model" => "gpt-4o-mini",
                "response_format" => ["type" => "json_object"], // 🔥 مهم
                "messages" => [
                    [
                        "role" => "user",
                        "content" => [
                            [
                                "type" => "text",
                               "text" => "You are a certificate data extractor. Extract data from this certificate image and return ONLY a valid JSON object with these exact fields:
- certificateType: must be exactly 'Participation Certificate' or 'Achievement Certificate' based on what you see, or null if unclear
- tournamentName: the name of the tournament as written
- tournamentdate: the date in YYYY-MM-DD format or null if not found

Return ONLY the JSON object, no explanation, no markdown."
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

            // ✅ تنظيف الرد قبل التحويل إلى JSON
            $content = $responseData['choices'][0]['message']['content'];
            $content = preg_replace('/```json|```/', '', $content);
            $content = trim($content);

            $data = json_decode($content, true);

            if (!$data) {
                return response()->json([
                    'error' => 'Failed to decode AI JSON',
                    'raw' => $content
                ], 500);
            }

            // sanitize certificate type
            $allowedTypes = [
                'Participation Certificate',
                'Achievement Certificate'
            ];

            $type = $data['certificateType'] ?? null;

            if (!in_array($type, $allowedTypes)) {
                $type = null;
            }

            // save to DB
           $player = auth()->user()->player;

if (!$player) {
    return response()->json([
        'error' => 'Player not found for this user'
    ], 404);
}

$tournament = Tournament::create([
    'certificateType' => $type,
    'tournamentName' => $data['tournamentName'] ?? null,
    'tournamentdate' => $data['tournamentdate'] ?? null,
    'rank' => $request->input('rank'),
    'player_id' => $player->id,
]);

            return response()->json([
                'data' => $tournament
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Server error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // compress image
    private function compressImage($file)
    {
        $imageData = file_get_contents($file->getRealPath());
        $image = imagecreatefromstring($imageData);

        if (!$image) {
            throw new \Exception("Invalid image file");
        }

        $width = imagesx($image);
        $height = imagesy($image);

        $newWidth = 800;
        $newHeight = (int)(($height / $width) * $newWidth);

        $tmp = imagecreatetruecolor($newWidth, $newHeight);

        imagealphablending($tmp, false);
        imagesavealpha($tmp, true);
        $white = imagecolorallocate($tmp, 255, 255, 255);
        imagefilledrectangle($tmp, 0, 0, $newWidth, $newHeight, $white);

        imagecopyresampled($tmp, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        ob_start();
        imagejpeg($tmp, null, 70);
        $compressed = ob_get_clean();

        imagedestroy($image);
        imagedestroy($tmp);

        return $compressed;
    }
}