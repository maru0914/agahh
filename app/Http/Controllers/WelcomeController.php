<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class WelcomeController extends Controller
{
    public function __invoke(Request $request)
    {
        return Inertia::render('Welcome',
            [
                'messagesInToday' => $request->user()
                    ->messages()
                    ->today()
                    ->oldest()
                    ->limit(50)
                    ->get(),
            ]
        );
    }
}
