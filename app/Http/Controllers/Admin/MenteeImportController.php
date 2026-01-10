<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Faculty;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MenteeImportController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.mentee-import.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'mentee_file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $path = $request->file('mentee_file')->getRealPath();
        $file = fopen($path, 'r');

        // Get header row to map columns, assuming first row is the header
        $header = fgetcsv($file);
        // Normalize headers (lowercase, replace spaces with underscores)
        $normalized_header = array_map(function($h) {
            return str_replace(' ', '_', strtolower($h));
        }, $header);
        
        $required_columns = ['npm', 'nama', 'email', 'fakultas', 'jenis_kelamin'];
        if (count(array_intersect($required_columns, $normalized_header)) != count($required_columns)) {
            return back()->with('error', 'Invalid CSV format. Please make sure columns contain: npm, nama, email, fakultas, jenis_kelamin.');
        }

        $menteeRoleId = Role::where('name', 'Mentee')->first()->id;
        $faculties = Faculty::pluck('id', 'name');
        
        $importedCount = 0;
        $errorRows = [];

        // Read the rest of the file
        while (($row = fgetcsv($file)) !== false) {
            $rowData = array_combine($normalized_header, $row);

            try {
                $facultyName = $rowData['fakultas'];
                $facultyId = $faculties->get($facultyName);

                if (!$facultyId) {
                    $errorRows[] = $rowData['npm'] . ' - Faculty not found: ' . $facultyName;
                    continue;
                }
                
                $gender = strtolower($rowData['jenis_kelamin']);
                if (!in_array($gender, ['male', 'female'])) {
                    $gender = 'male'; // Default gender if invalid
                }

                User::updateOrCreate(
                    ['email' => $rowData['email']],
                    [
                        'npm' => $rowData['npm'],
                        'name' => $rowData['nama'],
                        'password' => Hash::make($rowData['npm']), // Default password is NPM
                        'faculty_id' => $facultyId,
                        'role_id' => $menteeRoleId,
                        'gender' => $gender,
                        'email_verified_at' => now(), // Auto-verify email
                    ]
                );
                $importedCount++;

            } catch (\Exception $e) {
                Log::error("Error importing mentee row: " . $e->getMessage(), ['row' => $rowData]);
                $errorRows[] = 'NPM ' . ($rowData['npm'] ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        fclose($file);

        if (count($errorRows) > 0) {
            return redirect()->route('admin.mentees.index')
                ->with('warning', "Successfully imported {$importedCount} mentees. But some rows had errors: <br>" . implode('<br>', $errorRows));
        }

        return redirect()->route('admin.mentees.index')
                         ->with('success', "Successfully imported {$importedCount} mentees.");
    }
}
