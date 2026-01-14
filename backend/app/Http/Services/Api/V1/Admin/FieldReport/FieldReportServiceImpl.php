<?php

namespace App\Http\Services\Api\V1\Admin\FieldReport;

use App\Helper\ImageUpload;
use App\Http\Resources\Api\V1\Admin\FieldReportResource;
use App\Models\FieldReport;
use App\Models\FieldReportMedia;
use Illuminate\Container\Attributes\Auth as AttributesAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FieldReportServiceImpl implements FieldReportService
{
    public function index(Request $request)
    {
        $field_report = FieldReport::with('task', 'volunteer', 'media', 'createdBy');

        // Sorting (secure)
        $sortableColumns = ['id', 'report_title', 'findings', 'recommendations', 'created_at'];

        $sortBy = $request->get('sortBy', 'id');
        $sortDesc = $request->get('sortDesc', 'true') === 'true' ? 'desc' : 'asc';

        if (! in_array($sortBy, $sortableColumns)) {
            $sortBy = 'id';
        }

        $field_report->orderBy($sortBy, $sortDesc);

        // Search
        if ($search = $request->get('search')) {
            $field_report->where('report_title', 'like', "%{$search}%");
        }

        // Pagination
        $itemsPerPage = (int) $request->get('itemsPerPage', 10);
        $field_reports = $field_report->paginate($itemsPerPage);

        return FieldReportResource::collection($field_reports);
    }

    public function getAllFieldReports()
    {
        $field_reports = FieldReport::with('task', 'volunteer', 'media', 'createdBy')->latest()->get();

        return FieldReportResource::collection($field_reports);
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            
            // Create Field Report
            $report = new FieldReport();

            $report->task_id = $request->task_id;
            $report->volunteer_id = $request->volunteer_id;
            $report->report_title = $request->report_title;
            $report->report_description = $request->report_description;
            $report->findings = $request->findings;
            $report->recommendations = $request->recommendations;
            $report->people_met = $request->people_met;
            $report->latitude = $request->latitude;
            $report->longitude = $request->longitude;
            $report->submitted_at = $request->submitted_at ?? now();

            $report->created_by = Auth::id() ?? null;

            $report->save();

            // Save Field Report Media (if exists)
            if ($request->has('media') && is_array($request->media)) {

                foreach ($request->media as $media) {
                    $reportMedia = new FieldReportMedia();

                    $reportMedia->report_id = $report->id;
                    $reportMedia->media_type = $media['media_type'];

                     // Image upload
                    if ($request->hasFile('file_path')) {

                        $imagePath = ImageUpload::uploadImageApplicationStorage(
                            $request->file('file_path'),
                            'field-report-media'
                        );

                        // DB columns
                        $reportMedia->file_path = $imagePath;
                        $reportMedia->file_path_url = asset('storage/' . $imagePath);
                    }

                    $reportMedia->caption = $media['caption'] ?? null;
                    $reportMedia->latitude = $media['latitude'] ?? null;
                    $reportMedia->longitude = $media['longitude'] ?? null;
                    $reportMedia->uploaded_at = $media['uploaded_at'] ?? now();

                    $reportMedia->created_by = Auth::id() ?? null;

                    $reportMedia->save();
                }
            }

            // Activity Log
            activity('Field Report')
                ->performedOn($report)
                ->causedBy(Auth::user())
                ->withProperties([
                    'report' => $report->toArray(),
                    'media_count' => isset($request->media) ? count($request->media) : 0
                ])
                ->log('Field report created successfully');

            DB::commit();

            return new FieldReportResource(
                $report->load('media')
            );

        } catch (\Throwable $th) {
            DB::rollBack();

            throw $th;
        }
    }

    public function show(int $id)
    {
        $field_report = FieldReport::with('task', 'volunteer', 'media', 'createdBy')->findOrFail($id);

        return new FieldReportResource($field_report);
    }

    public function update(Request $request, int $id)
    {
        DB::beginTransaction();

        try {

            $report = FieldReport::with('media')->findOrFail($id);

            // Update Field Report
            $report->task_id = $request->task_id ?? $report->task_id;
            $report->volunteer_id = $request->volunteer_id ?? $report->volunteer_id;
            $report->report_title = $request->report_title ?? $report->report_title;
            $report->report_description = $request->report_description ?? $report->report_description;
            $report->findings = $request->findings ?? $report->findings;
            $report->recommendations = $request->recommendations ?? $report->recommendations;
            $report->people_met = $request->people_met ?? $report->people_met;
            $report->latitude = $request->latitude ?? $report->latitude;
            $report->longitude = $request->longitude ?? $report->longitude;
            $report->submitted_at = $request->submitted_at ?? $report->submitted_at;

            $report->created_by = Auth::id() ?? null;

            $report->save();

            /*
            |--------------------------------------------------------------------------
            | Handle Deleted Media
            |--------------------------------------------------------------------------
            */
            if ($request->has('deleted_media_ids') && is_array($request->deleted_media_ids)) {
                FieldReportMedia::where('report_id', $report->id)
                    ->whereIn('id', $request->deleted_media_ids)
                    ->delete();
            }

            /*
            |--------------------------------------------------------------------------
            | Handle Media Create / Update
            |--------------------------------------------------------------------------
            */
            if ($request->has('media') && is_array($request->media)) {

                foreach ($request->media as $index => $media) {

                    // Update existing media
                    if (!empty($media['id'])) {

                        $reportMedia = FieldReportMedia::where('report_id', $report->id)
                            ->where('id', $media['id'])
                            ->first();

                        if ($reportMedia) {

                            $reportMedia->media_type = $media['media_type'] ?? $reportMedia->media_type;
                            $reportMedia->caption = $media['caption'] ?? $reportMedia->caption;
                            $reportMedia->latitude = $media['latitude'] ?? $reportMedia->latitude;
                            $reportMedia->longitude = $media['longitude'] ?? $reportMedia->longitude;
                            $reportMedia->uploaded_at = $media['uploaded_at'] ?? $reportMedia->uploaded_at;

                            // Image upload (if new file provided)
                            if ($request->hasFile("media.$index.file_path")) {

                                $imagePath = ImageUpload::uploadImageApplicationStorage(
                                    $request->file("media.$index.file_path"),
                                    'field-report-media'
                                );

                                $reportMedia->file_path = $imagePath;
                                $reportMedia->file_path_url = asset('storage/' . $imagePath);
                            }

                            $reportMedia->created_by = Auth::id() ?? null;
                            $reportMedia->save();
                        }

                    } 
                    // Create new media
                    else {

                        $reportMedia = new FieldReportMedia();
                        $reportMedia->report_id = $report->id;
                        $reportMedia->media_type = $media['media_type'];

                        if ($request->hasFile("media.$index.file_path")) {

                            $imagePath = ImageUpload::uploadImageApplicationStorage(
                                $request->file("media.$index.file_path"),
                                'field-report-media'
                            );

                            $reportMedia->file_path = $imagePath;
                            $reportMedia->file_path_url = asset('storage/' . $imagePath);
                        }

                        $reportMedia->caption = $media['caption'] ?? null;
                        $reportMedia->latitude = $media['latitude'] ?? null;
                        $reportMedia->longitude = $media['longitude'] ?? null;
                        $reportMedia->uploaded_at = $media['uploaded_at'] ?? now();
                        $reportMedia->created_by = Auth::id() ?? null;

                        $reportMedia->save();
                    }
                }
            }

            // Activity Log
            activity('Field Report')
                ->performedOn($report)
                ->causedBy(Auth::user())
                ->withProperties([
                    'report_id' => $report->id,
                    'updated_data' => $request->all()
                ])
                ->log('Field report updated successfully');

            DB::commit();

            return new FieldReportResource(
                $report->load('media')
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
            // Find report with media
            $report = FieldReport::with('media')->findOrFail($id);

            // Delete related media files and records
            if ($report->media && $report->media->count() > 0) {

                foreach ($report->media as $media) {

                    // Delete file from storage if exists
                    if ($media->file_path) {
                        ImageUpload::deleteApplicationStorage($media->file_path);
                    }

                    // Delete media record
                    $media->delete();
                }
            }

            // Delete the field report
            $report->delete();

            // Activity log
            activity('Field Report')
                ->performedOn($report)
                ->causedBy(Auth::user())
                ->withProperties([
                    'report_id' => $id,
                    'media_deleted' => $report->media->count()
                ])
                ->log('Field report deleted successfully');

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            DB::rollBack();
            
            throw $th;
        }
    }

}