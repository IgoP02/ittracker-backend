<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function index()
    {
        return $messages = Message::select("message", "type", "created_at")->latest()->limit(5)->get();
    }
    public function store(Request $request, Message $message)
    {
        $message->message = $request["message"];
        $message->type = $request["type"];
        $message->save();

    }

    public function getLatestMessage(Request $request)
    {

        return Message::find($request["id"])->latest()->first();
    }

    public function destroyLatestMessage(Request $request)
    {
        Message::find($request["id"])->latest()->first()->delete();
        return response();
    }
}
