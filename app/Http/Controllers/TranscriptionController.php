<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class TranscriptionController extends Controller
{
    public function transcribe(Request $request)
    {
        $request->validate([
            'audio' => 'required|file|mimes:mp3,m4a,wav,webm',
        ]);

        $filePath = $request->file('audio')->getRealPath();

        // $response = OpenAI::audio()->transcriptions([
        //     'model' => 'whisper-1',
        //     'file' => fopen($filePath, 'r'),
        // ]);
        $response = null;

        return response()->json(['text' => $response]);
    }
}