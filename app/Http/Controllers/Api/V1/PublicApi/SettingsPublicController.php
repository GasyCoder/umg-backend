<?php

namespace App\Http\Controllers\Api\V1\PublicApi;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;

class SettingsPublicController extends Controller
{
    public function stats(): JsonResponse
    {
        $stats = Setting::byGroup('stats');
        
        return response()->json([
            'students' => (int) ($stats['stat_students'] ?? 15000),
            'staff' => (int) ($stats['stat_staff'] ?? 500),
            'formations' => (int) ($stats['stat_formations'] ?? 50),
            'establishments' => (int) ($stats['stat_establishments'] ?? 8),
            'services' => (int) ($stats['stat_services'] ?? 30),
        ]);
    }

    public function maintenanceStatus(): JsonResponse
    {
        $settings = Setting::byGroup('maintenance');
        $general = Setting::byGroup('general'); // Need site name/email for maintenance page
        
        $logoId = $general['logo_id'] ?? null;
        $logoUrl = null;
        
        if ($logoId) {
            $logo = \App\Models\Media::find($logoId);
            $logoUrl = $logo?->url;
        }

        return response()->json([
            'maintenance_mode' => filter_var($settings['maintenance_mode'] ?? false, FILTER_VALIDATE_BOOLEAN),
            'maintenance_message' => $settings['maintenance_message'] ?? 'Le site est en maintenance.',
            'site_name' => $general['site_name'] ?? 'Université de Mahajanga',
            'site_email' => $general['site_email'] ?? '',
            'site_phone' => $general['site_phone'] ?? '',
            'logo_url' => $logoUrl,
        ]);
    }
    public function index(): JsonResponse
    {
        $general = Setting::byGroup('general');
        $social = Setting::byGroup('social');

        $logoId = $general['logo_id'] ?? null;
        $faviconId = $general['favicon_id'] ?? null;
        
        $logoUrl = null;
        $faviconUrl = null;
        
        if ($logoId) {
            $logo = \App\Models\Media::find($logoId);
            $logoUrl = $logo?->url;
        }

        if ($faviconId) {
            $favicon = \App\Models\Media::find($faviconId);
            $faviconUrl = $favicon?->url;
        }

        return response()->json([
            'site_name' => $general['site_name'] ?? 'Université de Mahajanga',
            'site_description' => $general['site_description'] ?? '',
            'site_keywords' => $general['site_keywords'] ?? '',
            'site_email' => $general['site_email'] ?? 'contact@univ-mahajanga.mg',
            'site_phone' => $general['site_phone'] ?? '+261 20 62 225 61',
            'site_address' => $general['site_address'] ?? 'Campus Ambondrona, BP 652, Mahajanga 401, Madagascar',
            'logo_url' => $logoUrl,
            'favicon_url' => $faviconUrl,
            'social' => [
                'facebook' => $social['facebook_url'] ?? '#',
                'twitter' => $social['twitter_url'] ?? '#',
                'linkedin' => $social['linkedin_url'] ?? '#',
                'instagram' => $social['instagram_url'] ?? '#',
                'youtube' => $social['youtube_url'] ?? '#',
            ]
        ]);
    }
}
