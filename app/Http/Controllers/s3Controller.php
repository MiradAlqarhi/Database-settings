<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\VideoService; 
use App\Models\User; 
use Aws\S3\S3Client;

class s3Controller extends Controller
{   
public function updateAvatar(Request $request)
{
    $request->validate([
        'avatar' => 'required|image|max:2048',
    ]);

    $user = auth()->user();

    if ($user->avatar) {
        Storage::disk('s3')->delete($user->avatar);
    }

    $path = $request->file('avatar')->store(
        'avatars/' . $user->id,
        's3'
    );

    $user->update([
        'avatar' => $path
    ]);

    return response()->json([
        'avatar_url' => Storage::disk('s3')->url($path),
        'path' => $path
    ]);
}

public function presign(Request $request)
{
    $request->validate([
        'filename' => 'required|string',
        'filetype' => 'required|string',
    ]);

    $s3 = new S3Client([
        'region' => env('AWS_DEFAULT_REGION'),
        'version' => 'latest',
        'credentials' => [
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
        ],
    ]);

    $key = 'videos/' . uniqid() . '_' . $request->filename;

    $cmd = $s3->getCommand('PutObject', [
        'Bucket' => env('AWS_BUCKET'),
        'Key' => $key,
        'ContentType' => $request->filetype,
    ]);

    $requestPresigned = $s3->createPresignedRequest($cmd, '+10 minutes');

    return response()->json([
        'upload_url' => (string) $requestPresigned->getUri(),
        'key' => $key,
    ]);
}

public function store(Request $request)
{
    $request->validate([
        'tournament_id'        => 'required|exists:tournaments,id',
        'videos'               => 'required|array',
        'videos.*.key'         => 'required|string',
        'videos.*.videoSize'   => 'required|integer',
    ]);

    $saved = [];

    foreach ($request->videos as $video) {

        $url = Storage::disk('s3')->url($video['key']);

        $saved[] = VideoService::create([
            'url'            => $url,
            'videoSize'      => $video['videoSize'],
            'tournament_id' => $request->tournament_id,
        ]);
    }

    return response()->json($saved);
}
}