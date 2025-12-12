<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;

class NewsLetterController extends Controller
{
    function subscribeNewsletter(Request $request) : JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email']
        ]);

        if(Newsletter::where('email', $request->email)->exists()){
            throw ValidationException::withMessages([
                'email' => 'This email is already subscribed.'
            ]);
        }

        Newsletter::create([
            'email' => $request->email,
            'is_verified' => true
        ]);

        return response()->json(['message' => 'Subscribed successfully.']);
    }
}
