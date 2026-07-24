<?php

namespace App\Http\Controllers\superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperadminStudentController extends Controller
{
    public function SuperadminStudentPage()
    {
        $students = DB::table('students')
            ->leftJoin('training_providers', 'students.flying_id', '=', 'training_providers.id')
            ->select('students.*', 'training_providers.name as provider_name')
            ->orderBy('students.created_at', 'desc')
            ->get();

        $licenses = DB::table('students_license')->get();

        foreach ($students as $student) {
            $student->licenses = $licenses->where('student_id', $student->id)->values()->toArray();
        }

        $providers = DB::table('training_providers')->orderBy('name', 'asc')->get();

        return view('superadmin.students.index', compact('students', 'providers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'studentEmail' => 'required|email|max:255|unique:students,email',
            'password' => 'required|string|min:6',
            'studentPhone' => 'required|string|max:255',
            'dateOfBirth' => 'required|date',
            'enrollmentDate' => 'required|date',
            'flyingSchool' => 'required|integer|exists:training_providers,id',
            'stage' => 'required|string|max:255',
            'requiredHours' => 'required|integer|min:1',
            'attachments' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request) {
            $studentId = DB::table('students')->insertGetId([
                'first_name' => $request->firstName,
                'middle_name' => $request->middleName,
                'last_name' => $request->lastName,
                'email' => $request->studentEmail,
                'password' => Hash::make($request->password),
                'phone' => $request->studentPhone,
                'date_of_birth' => $request->dateOfBirth,
                'enrollment_date' => $request->enrollmentDate,
                'flying_id' => $request->flyingSchool,
                'stage' => $request->stage,
                'required_hours' => $request->requiredHours,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            if ($request->has('attachments') && is_array($request->attachments)) {
                if (!file_exists(public_path('uploads/student_licenses'))) {
                    mkdir(public_path('uploads/student_licenses'), 0777, true);
                }
                foreach ($request->attachments as $index => $attachmentData) {
                    if ($request->hasFile("attachments.{$index}.file")) {
                        $file = $request->file("attachments.{$index}.file");
                        $name = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('uploads/student_licenses'), $name);
                        $filePath = 'uploads/student_licenses/' . $name;

                        $docType = $attachmentData['type'] ?? 'Other';
                        if ($docType === 'Other') {
                            $docType = $attachmentData['custom_type'] ?? 'Other Document';
                        }

                        DB::table('students_license')->insert([
                            'student_id' => $studentId,
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

        return redirect()->back()->with('success', 'Student record and attachments saved successfully.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'studentEmail' => 'required|email|max:255|unique:students,email,' . $id,
            'password' => 'nullable|string|min:6',
            'studentPhone' => 'required|string|max:255',
            'dateOfBirth' => 'required|date',
            'enrollmentDate' => 'required|date',
            'flyingSchool' => 'required|integer|exists:training_providers,id',
            'stage' => 'required|string|max:255',
            'requiredHours' => 'required|integer|min:1',
            'attachments' => 'nullable|array',
            'deleted_licenses' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $id) {
            $updateData = [
                'first_name' => $request->firstName,
                'middle_name' => $request->middleName,
                'last_name' => $request->lastName,
                'email' => $request->studentEmail,
                'phone' => $request->studentPhone,
                'date_of_birth' => $request->dateOfBirth,
                'enrollment_date' => $request->enrollmentDate,
                'flying_id' => $request->flyingSchool,
                'stage' => $request->stage,
                'required_hours' => $request->requiredHours,
                'updated_at' => now(),
            ];

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            DB::table('students')->where('id', $id)->update($updateData);

            // Handle deleted license documents
            if ($request->filled('deleted_licenses')) {
                foreach ($request->deleted_licenses as $licenseId) {
                    $license = DB::table('students_license')->where('id', $licenseId)->first();
                    if ($license && $license->student_id == $id) {
                        // Prevent directory traversal
                        if (strpos($license->attachment, 'uploads/student_licenses/') === 0 && strpos($license->attachment, '..') === false) {
                            $fullPath = public_path($license->attachment);
                            if (file_exists($fullPath)) {
                                @unlink($fullPath);
                            }
                        }
                        DB::table('students_license')->where('id', $licenseId)->delete();
                    }
                }
            }

            // Handle new document uploads
            if ($request->has('attachments') && is_array($request->attachments)) {
                if (!file_exists(public_path('uploads/student_licenses'))) {
                    mkdir(public_path('uploads/student_licenses'), 0777, true);
                }
                foreach ($request->attachments as $index => $attachmentData) {
                    if ($request->hasFile("attachments.{$index}.file")) {
                        $file = $request->file("attachments.{$index}.file");
                        $name = time() . '_' . uniqid() . '_' . $file->getClientOriginalName();
                        $file->move(public_path('uploads/student_licenses'), $name);
                        $filePath = 'uploads/student_licenses/' . $name;

                        $docType = $attachmentData['type'] ?? 'Other';
                        if ($docType === 'Other') {
                            $docType = $attachmentData['custom_type'] ?? 'Other Document';
                        }

                        DB::table('students_license')->insert([
                            'student_id' => $id,
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

        return redirect()->back()->with('success', 'Student record and attachments updated successfully.');
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $licenses = DB::table('students_license')->where('student_id', $id)->get();
            foreach ($licenses as $license) {
                // Prevent directory traversal
                if (strpos($license->attachment, 'uploads/student_licenses/') === 0 && strpos($license->attachment, '..') === false) {
                    $fullPath = public_path($license->attachment);
                    if (file_exists($fullPath)) {
                        @unlink($fullPath);
                    }
                }
            }
            DB::table('students')->where('id', $id)->delete();
        });

        return redirect()->back()->with('success', 'Student record and associated attachments deleted successfully.');
    }
}
