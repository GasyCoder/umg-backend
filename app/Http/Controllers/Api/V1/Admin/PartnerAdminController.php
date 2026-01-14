<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePartnerRequest;
use App\Http\Requests\Admin\UpdatePartnerRequest;
use App\Http\Resources\PartnerResource;
use App\Models\Partner;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PartnerAdminController extends Controller
{
    public function index(Request $request)
    {
        $q = Partner::query()->with('logo');

        if ($request->filled('type')) $q->where('type', $request->string('type'));

        $per = min((int) $request->get('per_page', 20), 50);
        return PartnerResource::collection($q->orderBy('name')->paginate($per));
    }

    public function store(StorePartnerRequest $request)
    {
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $media = $this->uploadLogo($request->file('logo'), $request->user()->id);
            $data['logo_id'] = $media->id;
        }

        // Remove logo from data as it's not a model field
        unset($data['logo']);

        $partner = Partner::create($data);
        return new PartnerResource($partner->load('logo'));
    }

    public function show(int $id)
    {
        $partner = Partner::with('logo')->findOrFail($id);
        return new PartnerResource($partner);
    }

    public function update(UpdatePartnerRequest $request, int $id)
    {
        $partner = Partner::findOrFail($id);
        $data = $request->validated();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $media = $this->uploadLogo($request->file('logo'), $request->user()->id);
            $data['logo_id'] = $media->id;
        }

        // Remove logo from data as it's not a model field
        unset($data['logo']);

        $partner->fill($data)->save();

        return new PartnerResource($partner->load('logo'));
    }

    public function destroy(int $id)
    {
        $partner = Partner::findOrFail($id);
        $partner->delete();
        return response()->json(['data' => true]);
    }

    /**
     * Upload logo file and create Media record
     */
    private function uploadLogo($file, int $userId): Media
    {
        $disk = 'public';
        $folder = 'uploads/' . now()->format('Y/m');
        $filename = (string) Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs($folder, $filename, $disk);

        return Media::create([
            'disk' => $disk,
            'path' => $path,
            'mime' => $file->getMimeType(),
            'size' => $file->getSize(),
            'alt' => 'Partner logo',
            'created_by' => $userId,
        ]);
    }
}
