<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

require_once(app_path('Helpers/chatgpt.php'));

class MessageController extends Controller
{
    public function review(Request $request)
    {
        $message = $request->input('message');
        // Aquí invocas la función `reviewMessage` y pasas el mensaje
        $reviewedMessage = reviewMessage($message); 

        return response()->json(['message' => $reviewedMessage]);
    }
}
