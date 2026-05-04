<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // GET /notifications — returns unread count + latest notifications
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->latest()
            ->take(10)
            ->get();

        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count'  => $unreadCount,
        ]);
    }

    // POST /notifications/read-all
    public function readAll()
    {
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }

    // POST /notifications/{id}/read
    public function read($id)
    {
        Auth::user()->notifications()->where('id', $id)->update(['is_read' => true]);
        return response()->json(['success' => true]);
    }
}
