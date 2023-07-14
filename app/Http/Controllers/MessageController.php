<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        return $messages = Message::select("message", "type", "created_at")->latest()->limit(4)->get();
    }
    public function store(Request $request, Message $message)
    {

        $message->message = $request["message"];
        $message->type = $request["type"];
        $message->save();
        activity("message_post")->log("Usuario " .
            $request->user()->name . " publicó un anuncio");

    }

    public function getLatestMessage(Request $request)
    {

        return Message::latest()->first();
    }

    public function destroyLatestMessage(Request $request)
    {
        Message::latest()->first()->delete();
    }
}
