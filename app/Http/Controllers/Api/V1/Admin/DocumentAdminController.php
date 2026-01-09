<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDocumentRequest;
use App\Http\Requests\Admin\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Support\Slugger;
use Illuminate\Http\Request;

class DocumentAdminController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Document::class);

        $q = Document::query()->with(['category','file']);

        if ($request->filled('status')) $q->where('status', $request->string('status'));
        if ($request->filled('q')) {
            $term = $request->string('q')->toString();
            $q->where(fn($w) => $w->where('title','like',"%$term%")->orWhere('description','like',"%$term%"));
        }

        $per = min((int) $request->get('per_page', 15), 50);
        return DocumentResource::collection($q->orderByDesc('id')->paginate($per));
    }

    public function store(StoreDocumentRequest $request)
    {
        $data = $request->validated();

        $doc = Document::create([
            'title' => $data['title'],
            'slug' => Slugger::uniqueSlug(Document::class, $data['title']),
            'description' => $data['description'] ?? null,
            'status' => 'draft',
            'document_category_id' => $data['document_category_id'],
            'file_id' => $data['file_id'],
            'created_by' => $request->user()->id,
        ]);

        return new DocumentResource($doc->load(['category','file']));
    }

    public function show(int $id)
    {
        $this->authorize('viewAny', Document::class);

        $doc = Document::with(['category','file'])->findOrFail($id);
        return new DocumentResource($doc);
    }

    public function update(UpdateDocumentRequest $request, int $id)
    {
        $doc = Document::findOrFail($id);
        $this->authorize('update', $doc);

        $data = $request->validated();

        $doc->fill([
            'title' => $data['title'] ?? $doc->title,
            'description' => $data['description'] ?? $doc->description,
            'document_category_id' => $data['document_category_id'] ?? $doc->document_category_id,
            'file_id' => $data['file_id'] ?? $doc->file_id,
        ])->save();

        return new DocumentResource($doc->load(['category','file']));
    }

    public function destroy(int $id)
    {
        $doc = Document::findOrFail($id);
        $this->authorize('delete', $doc);

        $doc->delete();
        return response()->json(['data' => true]);
    }

    public function submit(int $id, Request $request)
    {
        $doc = Document::findOrFail($id);
        $this->authorize('submit', $doc);

        $doc->update(['status' => 'pending']);
        return new DocumentResource($doc);
    }

    public function approve(int $id, Request $request)
    {
        $doc = Document::findOrFail($id);
        $this->authorize('approve', $doc);

        $doc->update([
            'status' => 'published',
            'published_at' => now(),
            'validated_by' => $request->user()->id,
            'validated_at' => now(),
        ]);

        return new DocumentResource($doc);
    }

    public function reject(int $id, Request $request)
    {
        $doc = Document::findOrFail($id);
        $this->authorize('reject', $doc);

        $doc->update([
            'status' => 'draft',
            'validated_by' => $request->user()->id,
            'validated_at' => now(),
        ]);

        return new DocumentResource($doc);
    }

    public function archive(int $id, Request $request)
    {
        $doc = Document::findOrFail($id);
        $this->authorize('archive', $doc);

        $doc->update(['status' => 'archived']);
        return new DocumentResource($doc);
    }
}
