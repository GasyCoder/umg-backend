<?php

namespace App\Http\Controllers\Api\V1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class PostPublicController extends Controller
{
    public function index(Request $request)
    {
        $q = Post::query()
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->with(['coverImage','categories','tags'])
            ->orderByDesc('is_important')
            ->orderByDesc('published_at');

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

    public function show(Request $request, string $slug)
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->where('status', 'published')
            ->whereNotNull('published_at')
            ->with(['coverImage','categories','tags','gallery','author'])
            ->firstOrFail();

        $ip = (string) ($request->ip() ?? '');
        $ua = (string) ($request->userAgent() ?? '');
        $key = (string) config('app.key');
        $visitorHash = hash_hmac('sha256', $ip . '|' . $ua, $key);

        Post::query()->whereKey($post->id)->increment('views_count');
        $post->setAttribute('views_count', ((int) ($post->views_count ?? 0)) + 1);

        $inserted = DB::table('post_views')->insertOrIgnore([
            'post_id' => $post->id,
            'visitor_hash' => $visitorHash,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($inserted) {
            Post::query()->whereKey($post->id)->increment('unique_views_count');
            $post->setAttribute('unique_views_count', ((int) ($post->unique_views_count ?? 0)) + 1);
        }

        return new PostResource($post);
    }
}
