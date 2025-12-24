<?php

namespace App\Http\Services\Api\V1\Admin\Hero;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\HeroResource;
use App\Models\Hero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HeroServiceImpl implements HeroService
{
    public function index(Request $request)
    {
        $query = Hero::query();

        // Sorting (secure)
        $sortableColumns = ['id', 'title_en', 'title_bn', 'sub_title_en', 'sub_title_bn', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $query->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $query->where('title_en', 'like', "%{$search}%")
                ->orWhere('title_bn', 'like', "%{$search}%")
                ->orWhere('sub_title_en', 'like', "%{$search}%")
                ->orWhere('sub_title_bn', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $heros = $query->paginate($itemsPerPage);

        return HeroResource::collection($heros);
    }

    public function getAllHeros()
    {
        $heros = Hero::orderBy('id', 'desc')->get();

        return HeroResource::collection($heros);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            $hero = new Hero();

            $hero->title_en = $request->title_en;
            $hero->title_bn = $request->title_bn;
            $hero->sub_title_en = $request->sub_title_en;
            $hero->sub_title_bn = $request->sub_title_bn;
            $hero->description_en = $request->description_en;
            $hero->description_bn = $request->description_bn;

            // Image upload
             if ($request->hasFile('image')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'heros'
                );

                // DB columns
                $hero->image = $imagePath;
                $hero->image_url = asset('storage/' . $imagePath);
            }

            $hero->status = $request->status;

            $hero->save();

             activity('Hero Store')
                ->performedOn($hero)
                ->causedBy($hero)
                ->withProperties(['attributes' => $request->all()])
                ->log('Hero Store Successful');

            DB::commit();

            return new HeroResource($hero);

        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function show(int $id)
    {
        $hero = Hero::findOrFail($id);

        return new HeroResource($hero);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            
            $hero = Hero::findOrFail($id);

            $hero->title_en = $request->title_en ?? $hero->title_en;
            $hero->title_bn = $request->title_bn ?? $hero->title_bn;
            $hero->sub_title_en = $request->sub_title_en ?? $hero->sub_title_en;
            $hero->sub_title_bn = $request->sub_title_bn ?? $hero->sub_title_bn;
            $hero->description_en = $request->description_en ?? $hero->description_en;
            $hero->description_bn = $request->description_bn ?? $hero->description_bn;

            // Image upload
             if ($request->hasFile('image')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('image'),
                    'heros'
                );

                // DB columns
                $hero->image = $imagePath;
                $hero->image_url = asset('storage/' . $imagePath);
            }

            if ($request->has('status')) {
                $hero->status = $request->status;
            }

            $hero->save();

              activity('Hero Update')
                ->performedOn($hero)
                ->causedBy($hero)
                ->withProperties(['attributes' => $request->all()])
                ->log('Hero Update Successful');

            DB::commit();

            return new HeroResource($hero);

        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
    }

    public function destroy(int $id)
    {
        $hero = Hero::findOrFail($id);

        if ($hero->image) {
            
            ImageUpload::deleteApplicationStorage($hero->image);
        }

        $hero->delete();

         activity('Hero Delete')
            ->performedOn($hero)
            ->causedBy($hero)
            ->withProperties(['id' => $id])
            ->log('Hero Delete Successful');

        return response()->noContent();
    }

    public function statusUpdate(int $id)
    {
        $hero = Hero::findOrFail($id);

        $hero->status = !$hero->status;
        $hero->save();

          activity('Hero Status Update')
            ->performedOn($hero)
            ->causedBy($hero)
            ->withProperties(['id' => $id, 'status' => $hero->status])
            ->log('Hero Status Update Successful');

        return new HeroResource($hero);
    }
}