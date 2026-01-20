<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsletterTrackingController extends Controller
{
    /**
     * Pixel de tracking transparent 1x1 GIF
     */
    private const TRANSPARENT_GIF = "\x47\x49\x46\x38\x39\x61\x01\x00\x01\x00\x80\x00\x00\xff\xff\xff\x00\x00\x00\x21\xf9\x04\x01\x00\x00\x00\x00\x2c\x00\x00\x00\x00\x01\x00\x01\x00\x00\x02\x02\x44\x01\x00\x3b";

    /**
     * GET /v1/newsletter/track/{token}/open.gif
     *
     * Enregistre l'ouverture d'un email et retourne un pixel transparent.
     * Le token est un hash unique pour chaque envoi (newsletter_send.id encodé).
     */
    public function trackOpen(Request $request, string $token)
    {
        try {
            // Décoder le token (base64 de l'ID + signature simple)
            $sendId = $this->decodeToken($token);

            if ($sendId) {
                $send = NewsletterSend::find($sendId);

                if ($send && $send->status === 'sent') {
                    // Première ouverture : enregistrer la date
                    if (!$send->opened_at) {
                        $send->opened_at = now();
                    }

                    // Incrémenter le compteur d'ouvertures
                    $send->increment('open_count');

                    Log::channel('daily')->info('Newsletter opened', [
                        'send_id' => $send->id,
                        'campaign_id' => $send->newsletter_campaign_id,
                        'subscriber_id' => $send->newsletter_subscriber_id,
                        'open_count' => $send->open_count + 1,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silencieux - on ne veut pas bloquer l'affichage de l'email
            Log::channel('daily')->warning('Newsletter tracking error', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
        }

        // Toujours retourner le pixel transparent (même en cas d'erreur)
        return response(self::TRANSPARENT_GIF)
            ->header('Content-Type', 'image/gif')
            ->header('Content-Length', strlen(self::TRANSPARENT_GIF))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT');
    }

    /**
     * Encoder un ID de send en token sécurisé
     */
    public static function encodeToken(int $sendId): string
    {
        $data = $sendId . '|' . substr(md5($sendId . config('app.key')), 0, 8);
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    /**
     * Décoder un token en ID de send
     */
    private function decodeToken(string $token): ?int
    {
        try {
            $data = base64_decode(strtr($token, '-_', '+/'));
            $parts = explode('|', $data);

            if (count($parts) !== 2) {
                return null;
            }

            $sendId = (int) $parts[0];
            $signature = $parts[1];

            // Vérifier la signature
            $expectedSignature = substr(md5($sendId . config('app.key')), 0, 8);

            if (!hash_equals($expectedSignature, $signature)) {
                return null;
            }

            return $sendId;
        } catch (\Exception $e) {
            return null;
        }
    }
}
