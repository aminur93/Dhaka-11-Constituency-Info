<?php

namespace App\Http\Services\Api\V1\Admin\WardCommissioner;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\WardCommissionerResource;
use App\Models\WardCommissioner;
use App\Models\WardCommissionerDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WardCommissionerServiceImpl implements WardCommissionerService
{
    public function index(Request $request)
    {
        $ward_commissioner = WardCommissioner::with('user', 'ward', 'details', 'createdByUser');

         // Sorting (secure)
        $sortableColumns = ['id', 
                            'full_name_en', 
                            'full_name_bn', 
                            'phone_en', 
                            'phone_bn', 
                            'email', 
                            'nid_number_en', 
                            'nid_number_bn', 
                            'created_at'
                            ];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $ward_commissioner->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $ward_commissioner->where('full_name_en', 'like', "%{$search}%")
                ->orWhere('full_name_bn', 'like', "%{$search}%")
                ->orWhere('phone_en', 'like', "%{$search}%")
                ->orWhere('phone_bn', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('nid_number_en', 'like', "%{$search}%")
                ->orWhere('nid_number_bn', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $ward_commissioners = $ward_commissioner->paginate($itemsPerPage);

        return WardCommissionerResource::collection($ward_commissioners);
    }

    public function getAllWardCommissioners()
    {
        $ward_commissioner = WardCommissioner::with('user', 'ward', 'details', 'createdByUser')->latest()->get();

        return WardCommissionerResource::collection($ward_commissioner);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            /* =========================
            * Ward Commissioner (Main)
            * ========================= */
            $ward_commissioner = new WardCommissioner();

            $ward_commissioner->user_id = $request->user_id;
            $ward_commissioner->ward_id = $request->ward_id;
            $ward_commissioner->commissioner_id = $request->commissioner_id;
            $ward_commissioner->full_name_en = $request->full_name_en;
            $ward_commissioner->full_name_bn = $request->full_name_bn;
            $ward_commissioner->phone_en = $request->phone_en;
            $ward_commissioner->phone_bn = $request->phone_bn;
            $ward_commissioner->email = $request->email;
            $ward_commissioner->nid_number_en = $request->nid_number_en;
            $ward_commissioner->nid_number_bn = $request->nid_number_bn;

            if ($request->hasFile('photo')) {

                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('photo'),
                    'ward-commissioner-photo'
                );

                // DB columns
                $ward_commissioner->photo = $imagePath;
                $ward_commissioner->photo_url = asset('storage/' . $imagePath);
            }

            $ward_commissioner->political_party = $request->political_party;
            $ward_commissioner->term_start_date = $request->term_start_date;
            $ward_commissioner->term_end_date = $request->term_end_date;
            $ward_commissioner->election_year = $request->election_year;
            $ward_commissioner->status = $request->status;
            $ward_commissioner->is_current = $request->is_current;
            $ward_commissioner->created_by = Auth::id() ? $request->created_by : null;

            $ward_commissioner->save();

            /* =========================
            * Ward Commissioner Details
            * ========================= */
            $detailsData = $request->only([
                'date_of_birth',
                'gender',
                'blood_group',
                'education',
                'profession',
                'previous_experience',
                'achievements',
                'social_activities',
                'emergency_contact',
                'permanent_address_en',
                'permanent_address_bn',
                'present_address_en',
                'present_address_bn',
                'facebook_url',
                'twitter_url',
                'office_address_en',
                'office_address_bn',
                'office_phone_en',
                'office_phone_bn',
                'office_hours',
                'public_meeting_schedule',
                'biography_en',
                'biography_bn',
            ]);

            // only insert if at least one detail field is present
            if (!empty(array_filter($detailsData))) {
                $detailsData['commissioner_id'] = $ward_commissioner->id;

                WardCommissionerDetails::create($detailsData);
            }

            /* =========================
            * Activity Log
            * ========================= */
            activity('Ward Commissioner')
                ->performedOn($ward_commissioner)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Ward Commissioner Store Successful');

            DB::commit();

            return new WardCommissionerResource(
                $ward_commissioner->load('details')
            );

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $ward_commissioner = WardCommissioner::with('user', 'ward', 'details', 'createdByUser')->findOrFail($id);

        return new WardCommissionerResource($ward_commissioner);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            /* =========================
            * Find Commissioner
            * ========================= */
            $ward_commissioner = WardCommissioner::findOrFail($id);

            /* =========================
            * Update Main Table
            * ========================= */
            $ward_commissioner->user_id = $request->user_id ?? $ward_commissioner->user_id;
            $ward_commissioner->ward_id = $request->ward_id ?? $ward_commissioner->ward_id;
            $ward_commissioner->commissioner_id = $request->commissioner_id ?? $ward_commissioner->commissioner_id;
            $ward_commissioner->full_name_en = $request->full_name_en ?? $ward_commissioner->full_name_en;
            $ward_commissioner->full_name_bn = $request->full_name_bn ?? $ward_commissioner->full_name_bn;
            $ward_commissioner->phone_en = $request->phone_en ?? $ward_commissioner->phone_en;
            $ward_commissioner->phone_bn = $request->phone_bn ?? $ward_commissioner->phone_bn;
            $ward_commissioner->email = $request->email ?? $ward_commissioner->email;
            $ward_commissioner->nid_number_en = $request->nid_number_en ?? $ward_commissioner->nid_number_en;
            $ward_commissioner->nid_number_bn = $request->nid_number_bn ?? $ward_commissioner->nid_number_bn;

            if ($request->hasFile('photo')) {
                $imagePath = ImageUpload::uploadImageApplicationStorage(
                    $request->file('photo'),
                    'ward-commissioner-photo'
                );

                $ward_commissioner->photo = $imagePath;
                $ward_commissioner->photo_url = asset('storage/' . $imagePath);
            }

            $ward_commissioner->political_party = $request->political_party ?? $ward_commissioner->political_party;
            $ward_commissioner->term_start_date = $request->term_start_date ?? $ward_commissioner->term_start_date;
            $ward_commissioner->term_end_date = $request->term_end_date ?? $ward_commissioner->term_end_date;
            $ward_commissioner->election_year = $request->election_year ?? $ward_commissioner->election_year;
            $ward_commissioner->status = $request->status ?? $ward_commissioner->status;
            $ward_commissioner->is_current = $request->is_current ?? $ward_commissioner->is_current;

            $ward_commissioner->save();

            /* =========================
            * Update / Create Details
            * ========================= */
            $detailsData = $request->only([
                'date_of_birth',
                'gender',
                'blood_group',
                'education',
                'profession',
                'previous_experience',
                'achievements',
                'social_activities',
                'emergency_contact',
                'permanent_address_en',
                'permanent_address_bn',
                'present_address_en',
                'present_address_bn',
                'facebook_url',
                'twitter_url',
                'office_address_en',
                'office_address_bn',
                'office_phone_en',
                'office_phone_bn',
                'office_hours',
                'public_meeting_schedule',
                'biography_en',
                'biography_bn',
            ]);

            if (!empty(array_filter($detailsData))) {
                WardCommissionerDetails::updateOrCreate(
                    ['commissioner_id' => $ward_commissioner->id],
                    $detailsData
                );
            }

            /* =========================
            * Activity Log
            * ========================= */
            activity('Ward Commissioner')
                ->performedOn($ward_commissioner)
                ->causedBy(Auth::user())
                ->withProperties(['attributes' => $request->all()])
                ->log('Ward Commissioner Update Successful');

            DB::commit();

            return new WardCommissionerResource(
                $ward_commissioner->load('details')
            );

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function destroy(int $id)
    {
        DB::beginTransaction();

        try {
            // Find the commissioner
            $ward_commissioner = WardCommissioner::with('details')->findOrFail($id);

            // Delete related details first (optional if cascade exists)
            if ($ward_commissioner->details) {
                $ward_commissioner->details->delete();
            }

            // Delete main commissioner
            $ward_commissioner->delete();

            // Activity log
            activity('Ward Commissioner')
                ->performedOn($ward_commissioner)
                ->causedBy(Auth::user())
                ->withProperties(['commissioner_id' => $ward_commissioner->id])
                ->log('Ward Commissioner Deleted Successfully');

            DB::commit();

            return response()->json([
                'message' => 'Ward Commissioner deleted successfully.'
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to delete Ward Commissioner.',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    public function changeStatus(Request $request, int $id)
    {
        DB::beginTransaction();

        try {
            // Find commissioner
            $ward_commissioner = WardCommissioner::findOrFail($id);

            // Validate status
            $newStatus = $request->input('status');

            if (!in_array($newStatus, [0, 1, 2, 3])) {
                return response()->json([
                    'message' => 'Invalid status value. Allowed: 0,1,2,3'
                ], 422);
            }

            // Update status
            $ward_commissioner->status = $newStatus;
            $ward_commissioner->save();

            // Activity log
            activity('Ward Commissioner')
                ->performedOn($ward_commissioner)
                ->causedBy(Auth::user())
                ->withProperties(['status' => $ward_commissioner->status])
                ->log('Ward Commissioner Status Changed');

            DB::commit();

            return response()->json([
                'message' => 'Ward Commissioner status updated successfully.',
                'status' => $ward_commissioner->status
            ], 200);

        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update status.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}