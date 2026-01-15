<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ContactMessageRequest;
use App\Mail\ContactMessageConfirmation;
use App\Mail\ContactMessageToUniversity;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactPublicController extends Controller
{
    public function send(ContactMessageRequest $request)
    {
        $data = $request->validated();
        $siteEmail = Setting::get('site_email', config('mail.from.address'));

        try {
            Mail::to($siteEmail)->queue(new ContactMessageToUniversity(
                $data['name'],
                $data['email'],
                $data['subject'],
                $data['message']
            ));

            Mail::to($data['email'])->queue(new ContactMessageConfirmation(
                $data['name'],
                $data['subject']
            ));
        } catch (\Exception $e) {
            Log::error('Contact mail error: ' . $e->getMessage());
            return response()->json(['message' => 'Impossible d\'envoyer le message. Réessayez.'], 500);
        }

        return response()->json(['data' => true, 'message' => 'Message envoyé. Un email de confirmation vous a été envoyé.']);
    }
}
