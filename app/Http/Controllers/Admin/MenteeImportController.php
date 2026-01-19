<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Faculty;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

/**
 * MenteeImportController
 *
 * Controller untuk import data mentee dari CSV file (MODULE B #1: Data Mahasiswa dari Kemahasiswaan)
 * Admin dapat import batch data mentee dengan membaca file CSV/TXT
 *
 * Fitur:
 * - Upload & parse CSV/TXT file
 * - Validasi struktur file (harus ada kolom: npm, nama, email, fakultas, jenis_kelamin)
 * - Create/update User records dengan role = Mentee
 * - Default password = NPM
 * - Auto-verify email
 * - Handle error rows & report
 *
 * @package App\Http\Controllers\Admin
 */
class MenteeImportController extends Controller
{
    /**
     * Menampilkan form untuk upload file import mentee
     *
     * Form untuk memilih & upload CSV/TXT file
     * Format file harus: npm, nama, email, fakultas, jenis_kelamin
     *
     * @return \Illuminate\View\View View form import mentee
     */
    public function create()
    {
        return view('admin.mentee-import.create');
    }

    /**
     * Memproses file import mentee dan menyimpan ke database
     *
     * Proses:
     * 1. Validasi: file harus CSV/TXT
     * 2. Baca header row & normalize column names (lowercase, underscore)
     * 3. Validasi: harus ada kolom npm, nama, email, fakultas, jenis_kelamin
     * 4. Loop setiap row & proses:
     *    a. Cek fakultas ada di database
     *    b. Validasi jenis_kelamin (male/female)
     *    c. Create/update User dengan updateOrCreate (upsert)
     *    d. Set password default = npm (hashed)
     *    e. Set email_verified_at = now()
     * 5. Collect error rows & report
     * 6. Redirect dengan pesan sukses/warning
     *
     * Validasi:
     * - mentee_file: required, file, mimes:csv,txt
     *
     * Error handling:
     * - Faculty not found: skip row, log error
     * - Exception: log & add ke error list
     * - Show warning jika ada error rows, tetap import yang sukses
     *
     * @param Request $request HTTP request dengan field 'mentee_file' (CSV/TXT file)
     * @return \Illuminate\Http\RedirectResponse Redirect ke mentees index dengan pesan
     */
    public function store(Request $request)
    {
        // Validasi file upload
        $request->validate([
            'mentee_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        // Ambil path file & buka
        $path = $request->file('mentee_file')->getRealPath();
        $file = fopen($path, 'r');

        // Baca header row (baris pertama)
        $header = fgetcsv($file);

        // Normalize header (lowercase, replace spaces dengan underscore)
        $normalized_header = array_map(function ($h) {
            return str_replace(' ', '_', strtolower($h));
        }, $header);

        // Validasi: harus ada required columns
        $required_columns = ['npm', 'nama', 'email', 'fakultas', 'jenis_kelamin'];
        if (count(array_intersect($required_columns, $normalized_header)) != count($required_columns)) {
            return back()->with('error', 'Invalid CSV format. Please make sure columns contain: npm, nama, email, fakultas, jenis_kelamin.');
        }

        // Ambil role 'Mentee' id & preload faculties untuk mapping
        $menteeRoleId = Role::where('name', 'Mentee')->first()->id;
        $faculties = Faculty::pluck('id', 'name');

        // Initialize counters
        $importedCount = 0;
        $errorRows = [];

        // Read sisa file (loop setiap row setelah header)
        while (($row = fgetcsv($file)) !== false) {
            // Combine header dengan row values
            $rowData = array_combine($normalized_header, $row);

            try {
                // Ambil faculty_id dari nama fakultas
                $facultyName = $rowData['fakultas'];
                $facultyId = $faculties->get($facultyName);

                // Jika faculty tidak ketemu, skip row & log error
                if (!$facultyId) {
                    $errorRows[] = $rowData['npm'] . ' - Faculty not found: ' . $facultyName;
                    continue;
                }

                // Validasi & normalize gender (male/female)
                $gender = strtolower($rowData['jenis_kelamin']);
                if (!in_array($gender, ['male', 'female'])) {
                    $gender = 'male'; // Default ke male jika invalid
                }

                // Create atau update User dengan updateOrCreate (upsert)
                // Key: email (unique identifier)
                // Value: update npm, name, password, faculty_id, role_id, gender, email_verified_at
                User::updateOrCreate(
                    ['email' => $rowData['email']],
                    [
                        'npm' => $rowData['npm'],
                        'name' => $rowData['nama'],
                        'password' => Hash::make($rowData['npm']), // Password default = npm
                        'faculty_id' => $facultyId,
                        'role_id' => $menteeRoleId,
                        'gender' => $gender,
                        'email_verified_at' => now(), // Auto-verify email
                    ]
                );

                // Increment counter sukses
                $importedCount++;

            } catch (\Exception $e) {
                // Log error & collect ke error list
                Log::error("Error importing mentee row: " . $e->getMessage(), ['row' => $rowData]);
                $errorRows[] = 'NPM ' . ($rowData['npm'] ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        // Close file handle
        fclose($file);

        // Return response: pesan sukses/warning dengan detail
        if (count($errorRows) > 0) {
            return redirect()->route('admin.mentees.index')
                            ->with('warning', "Successfully imported {$importedCount} mentees. But some rows had errors: <br>" . implode('<br>', $errorRows));
        }

        return redirect()->route('admin.mentees.index')
                        ->with('success', "Successfully imported {$importedCount} mentees.");
    }
}
