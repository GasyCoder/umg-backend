<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostPublicController extends Controller
{
    private const VIEW_SESSION_COOKIE = 'umg_post_view_sess';

    public function index(Request $request)
    {
        $status = $request->string('status')->toString();
        if (!in_array($status, [Post::STATUS_PUBLISHED, Post::STATUS_ARCHIVED], true)) {
            $status = Post::STATUS_PUBLISHED;
        }

        $q = Post::query()
            ->where('status', $status)
            ->whereNotNull('published_at')
            ->with(['coverImage','categories','tags'])
            ->orderByDesc('is_important')
            ->orderByDesc('published_at');

        $year = (int) $request->get('year', 0);
        $month = (int) $request->get('month', 0);
        if ($year >= 1970 && $year <= 2100) {
            $q->whereYear('published_at', $year);
            if ($month >= 1 && $month <= 12) {
                $q->whereMonth('published_at', $month);
            }
        }

        if ($request->filled('exclude')) {
            $excludeId = (int) $request->get('exclude');
            if ($excludeId > 0) {
                $q->where('id', '!=', $excludeId);
            }
        }

        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $q->where(fn($w) => $w->where('title','like',"%$term%")->orWhere('excerpt','like',"%$term%"));
        }

        if ($request->filled('category')) {
            $slug = $request->string('category')->toString();
            $q->whereHas('categories', fn($c) => $c->where('slug', $slug));
        }

        $tagsParam = $request->string('tags')->toString();
        if ($tagsParam !== '') {
            $slugs = array_values(array_filter(array_map('trim', explode(',', $tagsParam))));
            if (count($slugs)) {
                $q->whereHas('tags', fn($t) => $t->whereIn('slug', $slugs));
            }
        } elseif ($request->filled('tag')) {
            $slug = $request->string('tag')->toString();
            $q->whereHas('tags', fn($t) => $t->where('slug', $slug));
        }

        if ($request->boolean('featured')) {
            $q->where('is_featured', true);
        }

        if ($request->boolean('is_slide')) {
            $q->where('is_slide', true);
        }

        $per = min((int) $request->get('per_page', 12), 50);
        return PostResource::collection($q->paginate($per));
    }

    /**
     * GET /v1/posts/archive-months?status=archived
     * Returns month/year list for archive dropdown (WordPress-like).
     */
    public function archiveMonths(Request $request)
    {
        $status = $request->string('status')->toString();
        if (!in_array($status, [Post::STATUS_PUBLISHED, Post::STATUS_ARCHIVED], true)) {
            $status = Post::STATUS_ARCHIVED;
        }

        $rows = Post::query()
            ->where('status', $status)
            ->whereNotNull('published_at')
            ->selectRaw('YEAR(published_at) as y, MONTH(published_at) as m, COUNT(*) as c')
            ->groupBy('y', 'm')
            ->orderByDesc('y')
            ->orderByDesc('m')
            ->limit(60)
            ->get();

        $data = $rows->map(function ($r) {
            $y = (int) ($r->y ?? 0);
            $m = (int) ($r->m ?? 0);
            $c = (int) ($r->c ?? 0);
            $label = ($m >= 1 && $m <= 12 && $y > 0)
                ? \Carbon\Carbon::create($y, $m, 1)->locale('fr')->translatedFormat('F Y')
                : null;

            return [
                'year' => $y,
                'month' => $m,
                'count' => $c,
                'label' => $label,
            ];
        })->values();

        return response()->json(['data' => $data]);
    }

    public function show(string $slug)
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->whereIn('status', [Post::STATUS_PUBLISHED, Post::STATUS_ARCHIVED])
            ->whereNotNull('published_at')
            ->with(['coverImage','categories','tags','gallery','author'])
            ->firstOrFail();

        return new PostResource($post);
    }

    /**
     * POST /v1/posts/{slug}/view
     * Counts at most 1 view per browser session (cookie).
     */
    public function view(Request $request, string $slug)
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->whereIn('status', [Post::STATUS_PUBLISHED, Post::STATUS_ARCHIVED])
            ->whereNotNull('published_at')
            ->firstOrFail();

        $sessionId = (string) $request->cookie(self::VIEW_SESSION_COOKIE, '');
        if ($sessionId === '') {
            $sessionId = Str::random(40);
        }

        $inserted = DB::table('post_views')->insertOrIgnore([
            'post_id' => $post->id,
            'visitor_hash' => $sessionId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($inserted) {
            Post::query()->whereKey($post->id)->increment('views_count');
            Post::query()->whereKey($post->id)->increment('unique_views_count');
        }

        $fresh = Post::query()->select(['id','views_count','unique_views_count'])->findOrFail($post->id);

        return (new PostResource($fresh))
            ->response()
            ->cookie(self::VIEW_SESSION_COOKIE, $sessionId, 0, null, null, $request->isSecure(), true, false, 'lax');
    }
}
