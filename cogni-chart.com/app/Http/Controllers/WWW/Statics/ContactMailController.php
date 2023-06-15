<?php

namespace App\Http\Controllers\WWW\Statics;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Expand\Validation\ExpValidation;
use Config;
use App\Mail\ContactUsMail;
use Mail;

class ContactMailController extends Controller
{

    public function send(Request $request)
    {
        $expValidation = new ExpValidation(['your_email_address', 'subject', 'body_of_email'], "www_validation");
        $expValidation->validateWithRedirect($request);

        $yourEmailAddress = $request->input("your_email_address");
        $subject = $request->input("subject");
        $bodyOfEmail = $request->input("body_of_email");

        Mail::to(Config::get("mail.from.address"))
            ->send(
                new ContactUsMail($yourEmailAddress, $subject, $bodyOfEmail)
            );
        return redirect()->route('mailsent');
    }

}
