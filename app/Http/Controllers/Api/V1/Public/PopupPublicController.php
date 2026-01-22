<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Models\Popup;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PopupPublicController extends Controller
{
    /**
     * Récupère le popup actif courant pour une page donnée
     */
    public function active(Request $request): JsonResponse
    {
        $page = $request->get('page', '/');

        $popup = Popup::currentActive()
            ->forPage($page)
            ->with('image')
            ->first();

        if (!$popup) {
            return response()->json([
                'data' => null,
            ]);
        }

        return response()->json([
            'data' => [
                'id' => $popup->id,
                'title' => $popup->title,
                'content_html' => $popup->content_html,
                'button_text' => $popup->button_text,
                'button_url' => $popup->button_url,
                'image_url' => $popup->image_url,
                'icon' => $popup->icon,
                'icon_color' => $popup->icon_color,
                'items' => $popup->items,
                'delay_ms' => $popup->delay_ms,
            ],
        ]);
    }
}
