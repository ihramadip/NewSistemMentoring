<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role; // Import Role model

class MenteeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Find the 'Mentee' role ID dynamically.
        $menteeRole = Role::where('name', 'Mentee')->first();

        // Fallback to a static ID if the role isn't found, though it should be.
        $menteeRoleId = $menteeRole ? $menteeRole->id : 3;

        $search = $request->input('search');

        $menteesQuery = User::where('role_id', $menteeRoleId)
                        ->with('faculty'); // Eager load faculty relationship

        if ($search) {
            $menteesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                      ->orWhere('npm', 'like', '%' . $search . '%');
            });
        }

        $mentees = $menteesQuery->orderBy('name', 'asc')->paginate(15);

        $mentees->appends(['search' => $search]);

        return view('admin.mentees.index', compact('mentees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // For manual creation if needed
        // return view('admin.mentees.create');
        abort(501); // Not Implemented
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        abort(501); // Not Implemented
    }

    /**
     * Display the specified resource.
     */
    public function show(User $mentee)
    {
        // Ensure we are only showing users that are mentees
        if ($mentee->role->name !== 'Mentee') {
            abort(404);
        }
        return view('admin.mentees.show', compact('mentee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $mentee)
    {
        //
        abort(501); // Not Implemented
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $mentee)
    {
        //
        abort(501); // Not Implemented
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $mentee)
    {
        //
        $mentee->delete();
        return redirect()->route('admin.mentees.index')->with('success', 'Mentee deleted successfully.');
    }

    /**
     * Remove all mentees from storage.
     */
    public function destroyAll()
    {
        $menteeRole = Role::where('name', 'Mentee')->first();
        if ($menteeRole) {
            User::where('role_id', $menteeRole->id)->delete();
        }
        
        return redirect()->route('admin.mentees.index')->with('success', 'All mentee data has been deleted successfully.');
    }

    /**
     * Remove the specified resources from storage.
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        $menteeRole = Role::where('name', 'Mentee')->first();
        $count = 0;

        if ($menteeRole) {
            $count = User::where('role_id', $menteeRole->id)
                         ->whereIn('id', $request->input('ids'))
                         ->delete();
        }

        return redirect()->route('admin.mentees.index')
                         ->with('success', "Successfully deleted {$count} selected mentees.");
    }
}
