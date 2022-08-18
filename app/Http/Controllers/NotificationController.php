<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return $request->user()->notifications->paginate();
    }

    public function unread(Request $request)
    {
        return $request->user()->unreadNotifications->paginate();
    }
}
