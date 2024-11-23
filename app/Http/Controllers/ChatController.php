<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'name' => 'required|string|max:255',
            ],
            [
                'name.required' => 'Por favor, ingrese un nombre para su chat',
            ]
        );

        $chat = new Chat();
        $chat->name = $request->name;
        $chat->code = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(8 / strlen($x)))), 1, 8);
        $chat->save();


        $chat->users()->attach(auth()->user());

        return redirect()->route('home')->with([
            'create' => 'ok',
            'name' => $chat->name,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $chat = Chat::findOrFail($id);
        $messages = $chat->messages()->orderBy('created_at', 'asc')->get();
        return view('chats.show', compact('chat', 'messages'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function add(Request $request)
    {
        $request->validate(
            [
                'codigo' => 'required|string',
            ],
            [
                'codigo.required' => 'Por favor, ingrese el codigo',
            ]
        );

        $chat = Chat::where('code', $request->codigo)->first();
        if ($chat) {
            // Asociar el usuario autenticado a la sala
            $chat->users()->attach(auth()->user());
            return redirect()->route('home')->with([
                'add' => 'ok',
                'name' => $chat->name,
            ]);
        } else {
            // Sala no encontrada
            return redirect()->back()->with('error', 'Sala no encontrada, por favor ingrese otro código.');
        }
    }

    public function sendMessage(Request $request, $chatId)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $message = new Message();
        $message->user_id = Auth::id();
        $message->chat_id = $chatId;
        $message->content = $request->message;
        $message->save();

        // Devolver el mensaje como respuesta JSON
        return response()->json(['status' => 'Mensaje enviado', 'message' => $message]);
    }
}
