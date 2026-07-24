<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

class SuperadminAircraftController extends Controller
{
    public function SuperadminAircraftPage()
    {
        $aircrafts = DB::table('aircrafts')
            ->leftJoin('training_providers', 'aircrafts.flying_id', '=', 'training_providers.id')
            ->select('aircrafts.*', 'training_providers.name as provider_name')
            ->orderBy('aircrafts.created_at', 'desc')
            ->get();

        $documents = DB::table('aircrafts_documents')->get();

        foreach ($aircrafts as $aircraft) {
            $aircraft->documents = $documents->where('aircraft_id', $aircraft->id)->values()->toArray();
        }

        $providers = DB::table('training_providers')->orderBy('name', 'asc')->get();

        return view('superadmin.aircraft.index', compact('aircrafts', 'providers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'registration' => 'required|string|max:255|unique:aircrafts,registration',
            'aircraftType' => 'required|string|max:255',
            'aircraftModel' => 'required|string|max:255',
            'totalHours' => 'required|numeric|min:0',
            'hoursToOverhaul' => 'required|numeric|min:0',
            'flyingSchool' => 'required|integer|exists:training_providers,id',
            'remarks' => 'nullable|string',
            'status' => 'required|string|max:255',
            'attachments' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $aircraftId = DB::table('aircrafts')->insertGetId([
                'registration' => $request->registration,
                'type' => $request->aircraftType,
                'model' => $request->aircraftModel,
                'total_hours' => $request->totalHours,
                'hours_to_overhaul' => $request->hoursToOverhaul,
                'flying_id' => $request->flyingSchool,
                'remarks' => $request->remarks,
                'status' => $request->status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->has('attachments') && is_array($request->attachments)) {
                if (!file_exists(public_path('uploads/aircraft_documents'))) {
                    mkdir(public_path('uploads/aircraft_documents'), 0777, true);
                }
                foreach ($request->attachments as $index => $attachmentData) {
                    if ($request->hasFile("attachments.{$index}.file")) {
                        $file = $request->file("attachments.{$index}.file");
                        $name = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('uploads/aircraft_documents'), $name);
                        $filePath = 'uploads/aircraft_documents/' . $name;

                        $docType = $attachmentData['type'] ?? 'Other';
                        if ($docType === 'Other') {
                            $docType = $attachmentData['custom_type'] ?? 'Other Document';
                        }

                        DB::table('aircrafts_documents')->insert([
                            'aircraft_id' => $aircraftId,
                            'document_type' => $docType,
                            'doc_no' => $attachmentData['number'] ?? null,
                            'expiration_date' => $attachmentData['expiration_date'] ?? null,
                            'attachment' => $filePath,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Aircraft record and documents saved successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'registration' => 'required|string|max:255|unique:aircrafts,registration,' . $id,
            'aircraftType' => 'required|string|max:255',
            'aircraftModel' => 'required|string|max:255',
            'totalHours' => 'required|numeric|min:0',
            'hoursToOverhaul' => 'required|numeric|min:0',
            'flyingSchool' => 'required|integer|exists:training_providers,id',
            'remarks' => 'nullable|string',
            'status' => 'required|string|max:255',
            'attachments' => 'nullable|array',
            'deleted_licenses' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $id) {
            DB::table('aircrafts')->where('id', $id)->update([
                'registration' => $request->registration,
                'type' => $request->aircraftType,
                'model' => $request->aircraftModel,
                'total_hours' => $request->totalHours,
                'hours_to_overhaul' => $request->hoursToOverhaul,
                'flying_id' => $request->flyingSchool,
                'remarks' => $request->remarks,
                'status' => $request->status,
                'updated_at' => now(),
            ]);

            // Handle deleted documents
            if ($request->filled('deleted_licenses')) {
                foreach ($request->deleted_licenses as $docId) {
                    $doc = DB::table('aircrafts_documents')->where('id', $docId)->first();
                    if ($doc && $doc->aircraft_id == $id) {
                        // Prevent directory traversal
                        if (strpos($doc->attachment, 'uploads/aircraft_documents/') === 0 && strpos($doc->attachment, '..') === false) {
                            $fullPath = public_path($doc->attachment);
                            if (file_exists($fullPath)) {
                                @unlink($fullPath);
                            }
                        }
                        DB::table('aircrafts_documents')->where('id', $docId)->delete();
                    }
                }
            }

            // Handle new uploads
            if ($request->has('attachments') && is_array($request->attachments)) {
                if (!file_exists(public_path('uploads/aircraft_documents'))) {
                    mkdir(public_path('uploads/aircraft_documents'), 0777, true);
                }
                foreach ($request->attachments as $index => $attachmentData) {
                    if ($request->hasFile("attachments.{$index}.file")) {
                        $file = $request->file("attachments.{$index}.file");
                        $name = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('uploads/aircraft_documents'), $name);
                        $filePath = 'uploads/aircraft_documents/' . $name;

                        $docType = $attachmentData['type'] ?? 'Other';
                        if ($docType === 'Other') {
                            $docType = $attachmentData['custom_type'] ?? 'Other Document';
                        }

                        DB::table('aircrafts_documents')->insert([
                            'aircraft_id' => $id,
                            'document_type' => $docType,
                            'doc_no' => $attachmentData['number'] ?? null,
                            'expiration_date' => $attachmentData['expiration_date'] ?? null,
                            'attachment' => $filePath,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Aircraft record and documents updated successfully.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $documents = DB::table('aircrafts_documents')->where('aircraft_id', $id)->get();
            foreach ($documents as $doc) {
                // Prevent directory traversal
                if (strpos($doc->attachment, 'uploads/aircraft_documents/') === 0 && strpos($doc->attachment, '..') === false) {
                    $fullPath = public_path($doc->attachment);
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }
            }
            DB::table('aircrafts')->where('id', $id)->delete();
        });

        return redirect()->back()->with('success', 'Aircraft record and associated documents deleted successfully.');
    }
}
