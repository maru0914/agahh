<?php

namespace App\Http\Controllers;

use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Welcome', [
            'previousMessages' => Message::where('user_id', auth()->id())->oldest()->get(),
        ]);
    }
}
