<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Newsletter;
use App\Services\AlertService;
use App\Services\MailService;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class NewsletterController extends Controller implements HasMiddleware
{
    static function Middleware(): array
    {
        return [
            new Middleware('permission:Subscriber Management')
        ];
    }
    
    function index(): View
    {
        $subscribers = Newsletter::paginate(30);
        return view('admin.newsletter.index', compact('subscribers'));
    }

    function sendNewsletter(Request $request): RedirectResponse
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
        ]);

        Newsletter::chunk(100, function ($chunk) use ($request) {
            foreach ($chunk as $subscriber) {
                MailService::sendAndQueue($subscriber->email, $request->subject, $request->message);
            }
        });

        AlertService::created('Newsletter sent successfully');
        return redirect()->back();
    }
}
