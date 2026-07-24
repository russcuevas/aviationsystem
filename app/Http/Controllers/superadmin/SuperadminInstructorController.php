<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperadminInstructorController extends Controller
{
    public function SuperadminInstructorPage()
    {
        $instructors = DB::table('instructors')
            ->leftJoin('training_providers', 'instructors.flying_id', '=', 'training_providers.id')
            ->select('instructors.*', 'training_providers.name as provider_name')
            ->orderBy('instructors.created_at', 'desc')
            ->get();

        $licenses = DB::table('instructors_license')->get();

        foreach ($instructors as $instructor) {
            $instructor->licenses = $licenses->where('instructor_id', $instructor->id)->values()->toArray();
        }

        $providers = DB::table('training_providers')->orderBy('name', 'asc')->get();

        return view('superadmin.instructors.index', compact('instructors', 'providers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'instructorEmail' => 'required|email|max:255|unique:instructors,email',
            'password' => 'required|string|min:6',
            'instructorPhone' => 'required|string|max:255',
            'flyingSchool' => 'required|integer|exists:training_providers,id',
            'attachments' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $instructorId = DB::table('instructors')->insertGetId([
                'first_name' => $request->firstName,
                'middle_name' => $request->middleName,
                'last_name' => $request->lastName,
                'email' => $request->instructorEmail,
                'password' => Hash::make($request->password),
                'phone' => $request->instructorPhone,
                'flying_id' => $request->flyingSchool,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->has('attachments') && is_array($request->attachments)) {
                if (!file_exists(public_path('uploads/instructor_licenses'))) {
                    mkdir(public_path('uploads/instructor_licenses'), 0777, true);
                }
                foreach ($request->attachments as $index => $attachmentData) {
                    if ($request->hasFile("attachments.{$index}.file")) {
                        $file = $request->file("attachments.{$index}.file");
                        $name = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('uploads/instructor_licenses'), $name);
                        $filePath = 'uploads/instructor_licenses/' . $name;

                        $docType = $attachmentData['type'] ?? 'Other';
                        if ($docType === 'Other') {
                            $docType = $attachmentData['custom_type'] ?? 'Other Document';
                        }

                        DB::table('instructors_license')->insert([
                            'instructor_id' => $instructorId,
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

        return redirect()->back()->with('success', 'Instructor record and attachments saved successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'instructorEmail' => 'required|email|max:255|unique:instructors,email,' . $id,
            'password' => 'nullable|string|min:6',
            'instructorPhone' => 'required|string|max:255',
            'flyingSchool' => 'required|integer|exists:training_providers,id',
            'attachments' => 'nullable|array',
            'deleted_licenses' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $id) {
            $updateData = [
                'first_name' => $request->firstName,
                'middle_name' => $request->middleName,
                'last_name' => $request->lastName,
                'email' => $request->instructorEmail,
                'phone' => $request->instructorPhone,
                'flying_id' => $request->flyingSchool,
                'updated_at' => now(),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            DB::table('instructors')->where('id', $id)->update($updateData);

            // Handle deleted license documents
            if ($request->filled('deleted_licenses')) {
                foreach ($request->deleted_licenses as $licenseId) {
                    $license = DB::table('instructors_license')->where('id', $licenseId)->first();
                    if ($license && $license->instructor_id == $id) {
                        // Prevent directory traversal
                        if (strpos($license->attachment, 'uploads/instructor_licenses/') === 0 && strpos($license->attachment, '..') === false) {
                            $fullPath = public_path($license->attachment);
                            if (file_exists($fullPath)) {
                                @unlink($fullPath);
                            }
                        }
                        DB::table('instructors_license')->where('id', $licenseId)->delete();
                    }
                }
            }

            // Handle new document uploads
            if ($request->has('attachments') && is_array($request->attachments)) {
                if (!file_exists(public_path('uploads/instructor_licenses'))) {
                    mkdir(public_path('uploads/instructor_licenses'), 0777, true);
                }
                foreach ($request->attachments as $index => $attachmentData) {
                    if ($request->hasFile("attachments.{$index}.file")) {
                        $file = $request->file("attachments.{$index}.file");
                        $name = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('uploads/instructor_licenses'), $name);
                        $filePath = 'uploads/instructor_licenses/' . $name;

                        $docType = $attachmentData['type'] ?? 'Other';
                        if ($docType === 'Other') {
                            $docType = $attachmentData['custom_type'] ?? 'Other Document';
                        }

                        DB::table('instructors_license')->insert([
                            'instructor_id' => $id,
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

        return redirect()->back()->with('success', 'Instructor record and attachments updated successfully.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $licenses = DB::table('instructors_license')->where('instructor_id', $id)->get();
            foreach ($licenses as $license) {
                // Prevent directory traversal
                if (strpos($license->attachment, 'uploads/instructor_licenses/') === 0 && strpos($license->attachment, '..') === false) {
                    $fullPath = public_path($license->attachment);
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }
            }
            DB::table('instructors')->where('id', $id)->delete();
        });

        return redirect()->back()->with('success', 'Instructor record and associated attachments deleted successfully.');
    }
}
