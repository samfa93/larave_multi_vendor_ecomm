<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Frontend\ContactStoreRequest;
use App\Mail\GenericMail;
use App\Models\Contact;
use App\Services\AlertService;
use App\Services\MailService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    function index() : View
    {
        return view('frontend.pages.contact');
    }

    function store(ContactStoreRequest $request) : RedirectResponse
    {
        // send mail to admin
        Mail::to(config('settings.site_email'))->send(new GenericMail($request->subject, $request->message, $request->email));
        Contact::create($request->validated());
        AlertService::created('Your message has been sent successfully. We will get back to you soon.');

        return redirect()->route('contact.index');

    }
}
