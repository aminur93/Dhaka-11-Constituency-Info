<?php

namespace App\Http\Services\Api\V1\Admin\LogoBannerSlider;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\LogoBannerSlideResource;
use App\Models\LogoBannerSlide;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogoBannerSlideServiceImpl implements LogoBannerSlideService
{
    public function index(Request $request)
    {
        $query = LogoBannerSlide::query();

        // Sorting (secure)
        $sortableColumns = ['id', 'title', 'type', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $query->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $logo_banner_slide = $query->paginate($itemsPerPage);

        return LogoBannerSlideResource::collection($logo_banner_slide);
    }

    public function getAllLogoBannerSlides()
    {
        $logo_banner_slides = LogoBannerSlide::orderBy('id', 'desc')->get();

        return LogoBannerSlideResource::collection($logo_banner_slides);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            $logo_banner_slide = new LogoBannerSlide();

            $logo_banner_slide->title = $request->title;
            $logo_banner_slide->type = $request->type;

            if ($request->hasFile('image')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'user-image'
                );

                // DB columns
                $logo_banner_slide->image = $imagePath;
                $logo_banner_slide->image_url = asset('storage/' . $imagePath);
            }

            $logo_banner_slide->save();

            activity('Logo Banner Slide Store')
                ->performedOn($logo_banner_slide)
                ->causedBy($logo_banner_slide)
                ->withProperties(['attributes' => $request->all()])
                ->log('Logo Banner Slide Store Successful');

            DB::commit();

            return new LogoBannerSlideResource($logo_banner_slide);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $logo_banner_slide = LogoBannerSlide::findOrFail($id);

        return new LogoBannerSlideResource($logo_banner_slide);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();
        
        try {

            $logo_banner_slide = LogoBannerSlide::findOrFail($id);

            $logo_banner_slide->title = $request->title;
            $logo_banner_slide->type = $request->type;

            if ($request->hasFile('image')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'user-image'
                );

                // DB columns
                $logo_banner_slide->image = $imagePath;
                $logo_banner_slide->image_url = asset('storage/' . $imagePath);
            }

            $logo_banner_slide->save();

            activity('Logo Banner Slide Update')
                ->performedOn($logo_banner_slide)
                ->causedBy($logo_banner_slide)
                ->withProperties(['attributes' => $request->all()])
                ->log('Logo Banner Slide Update Successful');

            DB::commit();

            return new LogoBannerSlideResource($logo_banner_slide);

        } catch (\Throwable $th) {

            DB::rollBack();

            throw $th;
        }
    }

    public function destroy(int $id)
    {
        $logo_banner_slide = LogoBannerSlide::findOrFail($id);

        // i want to also delete from stroage folder
        if ($logo_banner_slide->image) {
            ImageUpload::deleteApplicationStorage($logo_banner_slide->image);
        }

        $logo_banner_slide->delete();

        activity('Logo Banner Slide Delete')
            ->performedOn($logo_banner_slide)
            ->causedBy($logo_banner_slide)
            ->withProperties(['id' => $id])
            ->log('Logo Banner Slide Delete Successful');

        return response()->noContent();
    }

    public function statusUpdate(int $id)
    {
        $logo_banner_slide = LogoBannerSlide::findOrFail($id);

        $logo_banner_slide->status = !$logo_banner_slide->status;
        $logo_banner_slide->save();

        activity('Logo Banner Slide Status Update')
            ->performedOn($logo_banner_slide)
            ->causedBy($logo_banner_slide)
            ->withProperties(['id' => $id, 'status' => $logo_banner_slide->status])
            ->log('Logo Banner Slide Status Update Successful');

        return new LogoBannerSlideResource($logo_banner_slide);
    }
}