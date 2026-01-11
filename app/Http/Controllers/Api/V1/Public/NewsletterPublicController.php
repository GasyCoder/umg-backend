<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\NewsletterSubscribeRequest;
use App\Http\Requests\Public\NewsletterUnsubscribeRequest;
use App\Models\NewsletterSubscriber;
use Illuminate\Support\Str;

class NewsletterPublicController extends Controller
{
    public function subscribe(NewsletterSubscribeRequest $request)
    {
        $data = $request->validated();

        $subscriber = NewsletterSubscriber::query()->firstOrNew(['email' => $data['email']]);
        
        // If already active, just return success
        if ($subscriber->exists && $subscriber->status === 'active') {
            return response()->json(['message' => 'Vous êtes déjà inscrit.']);
        }

        $subscriber->name = $data['name'] ?? $subscriber->name;
        // status is pending
        $subscriber->status = 'pending';
        $subscriber->token = Str::random(64);
        $subscriber->subscribed_at = null; // Will be set on verification
        $subscriber->save();

        // Send verification email
        // Frontend URL: http://localhost:3000/newsletter/verify?token=...
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
        $verificationUrl = $frontendUrl . '/newsletter/verify?token=' . $subscriber->token;

        try {
            \Illuminate\Support\Facades\Mail::to($subscriber->email)->send(new \App\Mail\NewsletterVerification($verificationUrl));
        } catch (\Exception $e) {
            // Log error but don't fail the request completely if mail fails in dev
            \Illuminate\Support\Facades\Log::error('Newsletter mail error: ' . $e->getMessage());
        }

        return response()->json(['data' => true, 'message' => 'Veuillez vérifier votre email pour confirmer.']);
    }

    public function verify(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $token = $request->input('token');

        $subscriber = NewsletterSubscriber::query()->where('token', $token)->first();

        if (!$subscriber) {
             return response()->json(['message' => 'Token invalide.'], 404);
        }

        $subscriber->status = 'active';
        $subscriber->subscribed_at = now();
        // Keep the token for unsubscribe actions
        $subscriber->save();

        return response()->json(['data' => true, 'message' => 'Inscription confirmée !']);
    }

    public function unsubscribe(NewsletterUnsubscribeRequest $request)
    {
        $token = $request->validated()['token'];

        $subscriber = NewsletterSubscriber::query()->where('token', $token)->firstOrFail();
        $subscriber->status = 'unsubscribed';
        $subscriber->unsubscribed_at = now();
        $subscriber->save();

        return response()->json(['data' => true]);
    }
}
