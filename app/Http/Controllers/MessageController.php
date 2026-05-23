<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Store a newly created message in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        try {
            Message::create($validated);
            return response()->json(['success' => true, 'message' => 'Message sent successfully!']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to send message. Please try again.'], 500);
        }
    }

    /**
     * Display a listing of messages for the admin.
     */
    public function index(Request $request)
    {
        $sessionUser = $request->session()->get('user');
        if (!$sessionUser || $sessionUser->role !== 'admin') {
            return redirect()->route('index')->with('error', 'Unauthorized access.');
        }

        $messages = Message::orderBy('created_at', 'desc')->get();
        return view('admin.messages', compact('messages'));
    }

    /**
     * Mark a message as read.
     */
    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        $message->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    /**
     * Delete a message.
     */
    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();
        return response()->json(['success' => true]);
    }
}
