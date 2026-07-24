<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TrainingProvider;

class SuperadminFlightSchoolController extends Controller
{
    public function SuperadminFlightSchoolPage()
    {
        $providers = TrainingProvider::orderBy('created_at', 'desc')->get();
        return view('superadmin.flight_school.index', compact('providers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'schoolName' => 'required|string|max:255',
            'schoolCode' => 'required|string|max:255|unique:training_providers,code',
            'schoolAddress' => 'required|string|max:255',
            'contactEmail' => 'required|email|max:255',
            'contactPhone' => 'required|string|max:255',
            'accreditation_course' => 'required|string|max:255',
            'atoc_attachment' => 'nullable|array',
            'atoc_attachment.*' => 'file|max:10240', // max 10MB per file
        ]);

        $filePaths = [];
        if ($request->hasFile('atoc_attachment')) {
            if (!file_exists(public_path('uploads/atoc_attachments'))) {
                mkdir(public_path('uploads/atoc_attachments'), 0777, true);
            }
            foreach ($request->file('atoc_attachment') as $file) {
                $name = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/atoc_attachments'), $name);
                $filePaths[] = 'uploads/atoc_attachments/' . $name;
            }
        }

        TrainingProvider::create([
            'name' => $request->schoolName,
            'code' => $request->schoolCode,
            'address' => $request->schoolAddress,
            'email' => $request->contactEmail,
            'phone' => $request->contactPhone,
            'accreditation_course' => $request->accreditation_course,
            'atoc_attachment' => $filePaths,
        ]);

        return redirect()->back()->with('success', 'Training Provider created successfully.');
    }

    public function show($id)
    {
        $provider = TrainingProvider::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => $provider
        ]);
    }

    public function update(Request $request, $id)
    {
        $provider = TrainingProvider::findOrFail($id);

        $request->validate([
            'schoolName' => 'required|string|max:255',
            'schoolCode' => 'required|string|max:255|unique:training_providers,code,' . $id,
            'schoolAddress' => 'required|string|max:255',
            'contactEmail' => 'required|email|max:255',
            'contactPhone' => 'required|string|max:255',
            'accreditation_course' => 'required|string|max:255',
            'atoc_attachment' => 'nullable|array',
            'atoc_attachment.*' => 'file|max:10240',
            'deleted_attachments' => 'nullable|array',
        ]);

        $currentAttachments = $provider->atoc_attachment ?? [];

        // Handle deleted attachments
        if ($request->filled('deleted_attachments')) {
            foreach ($request->deleted_attachments as $deletedPath) {
                // Prevent directory traversal
                if (strpos($deletedPath, 'uploads/atoc_attachments/') === 0 && strpos($deletedPath, '..') === false) {
                    if (($key = array_search($deletedPath, $currentAttachments)) !== false) {
                        unset($currentAttachments[$key]);
                        $fullPath = public_path($deletedPath);
                        if (file_exists($fullPath)) {
                            @unlink($fullPath);
                        }
                    }
                }
            }
            $currentAttachments = array_values($currentAttachments); // Re-index array
        }

        // Handle new uploads
        if ($request->hasFile('atoc_attachment')) {
            if (!file_exists(public_path('uploads/atoc_attachments'))) {
                mkdir(public_path('uploads/atoc_attachments'), 0777, true);
            }
            foreach ($request->file('atoc_attachment') as $file) {
                $name = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads/atoc_attachments'), $name);
                $currentAttachments[] = 'uploads/atoc_attachments/' . $name;
            }
        }

        $provider->update([
            'name' => $request->schoolName,
            'code' => $request->schoolCode,
            'address' => $request->schoolAddress,
            'email' => $request->contactEmail,
            'phone' => $request->contactPhone,
            'accreditation_course' => $request->accreditation_course,
            'atoc_attachment' => $currentAttachments,
        ]);

        return redirect()->back()->with('success', 'Training Provider updated successfully.');
    }

    public function destroy($id)
    {
        $provider = TrainingProvider::findOrFail($id);

        // Delete associated files from storage
        if (!empty($provider->atoc_attachment)) {
            foreach ($provider->atoc_attachment as $filePath) {
                $fullPath = public_path($filePath);
                if (file_exists($fullPath)) {
                    @unlink($fullPath);
                }
            }
        }

        $provider->delete();

        return redirect()->back()->with('success', 'Training Provider deleted successfully.');
    }
}
