<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Role;

/**
 * MenteeController
 *
 * Controller untuk manage data mentee (MODULE B #1: Mentee Management)
 * Admin dapat view, show detail, delete mentee individual atau bulk
 *
 * Fitur:
 * - Index: list semua mentees dengan search by name/npm, paginated 15, eager load faculty
 * - Create: abort 501 (not implemented - mentee dibuat via MenteeImportController)
 * - Store: abort 501 (not implemented)
 * - Show: display mentee detail (verify role adalah Mentee, abort 404 jika bukan)
 * - Edit: abort 501 (not implemented)
 * - Update: abort 501 (not implemented)
 * - Destroy: delete single mentee
 * - DestroyAll: delete all mentees at once (dangerous operation)
 * - BulkDestroy: delete multiple selected mentees with id array validation
 *
 * Mentee creation:
 * - Primary way: MenteeImportController (via CSV import)
 * - Not via create/store method (so return 501 Not Implemented)
 * - Direct edit/update: tidak di-implement (mentee data dari import CSV)
 *
 * Data access:
 * - Search: name (like search) atau npm (student ID number)
 * - Filter: role_id = 3 (Mentee role) via Role::where('name', 'Mentee')
 * - Eager load: faculty relationship untuk display faculty name
 * - Fallback: jika Mentee role tidak ketemu, default ke role_id = 3
 *
 * Deletion:
 * - Single: destroy($mentee)
 * - All: destroyAll() - delete semua mentees (user dengan role_id = mentee)
 * - Bulk: bulkDestroy($request) - validate ids array, delete selected mentees
 *
 * Safety checks:
 * - show(): verify role_id adalah Mentee (abort 404 jika bukan)
 * - bulkDestroy(): validate ids array, ensure exists:users,id
 * - Role lookup: dynamic via Role::where('name', 'Mentee'), fallback ke 3
 *
 * @package App\Http\Controllers\Admin
 */
class MenteeController extends Controller
{
    /**
     * Menampilkan list semua mentees
     *
     * Proses:
     * 1. Find Mentee role ID dinamis via Role::where('name', 'Mentee')
     * 2. Fallback ke hardcoded role_id = 3 jika role tidak ketemu
     * 3. Build query: User dengan role_id = menteeRoleId
     * 4. Eager load faculty relationship
     * 5. Check input search (optional parameter)
     * 6. Jika ada search: filter by name OR npm (like search)
     * 7. Order by name ascending
     * 8. Paginate dengan 15 records per halaman
     * 9. Append search parameter ke pagination links
     * 10. Return view dengan mentees list
     *
     * Query building:
     * - Start dari User::where('role_id', $menteeRoleId)
     * - Add eager load: ->with('faculty') untuk prevent N+1
     * - Add search filter dengan where closure: name like OR npm like
     * - Add order by name, paginate 15
     *
     * Search behavior:
     * - Search value diteruskan ke pagination links (appends)
     * - User dapat refine search dengan multiple queries
     *
     * @param Request $request Request dengan optional search parameter
     * @return \Illuminate\View\View View list mentees dengan pagination
     */
    public function index(Request $request)
    {
        // Find Mentee role ID dinamis dari database
        $menteeRole = Role::where('name', 'Mentee')->first();

        // Fallback ke role_id = 3 jika Mentee role tidak ketemu
        $menteeRoleId = $menteeRole ? $menteeRole->id : 3;

        // Get search parameter dari request (optional)
        $search = $request->input('search');

        // Build query: User dengan role_id = mentee
        $menteesQuery = User::where('role_id', $menteeRoleId)
                            ->with('faculty'); // Eager load faculty untuk prevent N+1

        // Add search filter jika ada search parameter
        if ($search) {
            // Filter: name like OR npm like (untuk search by name atau npm)
            $menteesQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%')
                    ->orWhere('npm', 'like', '%' . $search . '%');
            });
        }

        // Order by name ascending, paginate 15 records per halaman
        $mentees = $menteesQuery->orderBy('name', 'asc')->paginate(15);

        // Append search parameter ke pagination links (untuk refine search)
        $mentees->appends(['search' => $search]);

        // Return view dengan mentees list
        return view('admin.mentees.index', compact('mentees'));
    }

    /**
     * Menampilkan form untuk create mentee baru
     *
     * Note:
     * - Method tidak di-implement (return 501)
     * - Mentee dibuat via MenteeImportController (CSV import)
     * - Manual create form tidak dibutuhkan untuk system ini
     *
     * @return void Abort 501 Not Implemented
     */
    public function create()
    {
        // Not Implemented - mentees created via MenteeImportController (CSV import)
        abort(501);
    }

    /**
     * Menyimpan mentee baru ke database
     *
     * Note:
     * - Method tidak di-implement (return 501)
     * - Mentee dibuat via MenteeImportController (CSV import)
     *
     * @param Request $request
     * @return void Abort 501 Not Implemented
     */
    public function store(Request $request)
    {
        // Not Implemented - mentees created via MenteeImportController (CSV import)
        abort(501);
    }

    /**
     * Menampilkan detail mentee
     *
     * Proses:
     * 1. User di-resolve via route model binding
     * 2. Verify: user role adalah Mentee (role->name === 'Mentee')
     * 3. Jika bukan Mentee, abort 404 (not found)
     * 4. Return view dengan mentee detail
     *
     * Safety check:
     * - Ensure hanya mentee yang bisa di-show
     * - Jika user adalah mentor/admin, abort 404
     * - Prevent exposure mentee data jika di-access mentee user lain
     *
     * @param User $mentee User model via route binding
     * @return \Illuminate\View\View View mentee detail
     */
    public function show(User $mentee)
    {
        // Verify role adalah Mentee (safety check)
        if ($mentee->role->name !== 'Mentee') {
            abort(404); // Not found jika bukan mentee
        }

        // Return view dengan mentee detail
        return view('admin.mentees.show', compact('mentee'));
    }

    /**
     * Menampilkan form untuk edit mentee
     *
     * Note:
     * - Method tidak di-implement (return 501)
     * - Mentee data tidak boleh di-edit via controller ini
     * - Mentee data dari CSV import, tidak untuk manual edit
     * - Jika perlu update: re-import CSV dengan data terbaru
     *
     * @param User $mentee
     * @return void Abort 501 Not Implemented
     */
    public function edit(User $mentee)
    {
        // Not Implemented - mentee data not editable, only viewable
        abort(501);
    }

    /**
     * Memperbarui mentee di database
     *
     * Note:
     * - Method tidak di-implement (return 501)
     * - Mentee data tidak boleh di-edit
     *
     * @param Request $request
     * @param User $mentee
     * @return void Abort 501 Not Implemented
     */
    public function update(Request $request, User $mentee)
    {
        // Not Implemented - mentee data not editable
        abort(501);
    }

    /**
     * Menghapus single mentee dari database
     *
     * Proses:
     * 1. User di-resolve via route model binding
     * 2. Delete user record (cascade delete related data)
     * 3. Redirect ke index dengan success message
     *
     * Cascade deletion:
     * - User delete -> GroupMember soft/hard delete
     * - User delete -> PlacementTest delete
     * - User delete -> ExamSubmission delete
     * - User delete -> Attendance delete
     * - User delete -> ProgressReport delete
     * - Etc (depends on foreign key cascade settings)
     *
     * WARNING:
     * - Dangerous operation, ensure admin authorization
     * - Consider soft delete untuk maintain history
     * - Or implement audit trail untuk track deletions
     *
     * @param User $mentee User model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function destroy(User $mentee)
    {
        // Delete mentee user record
        $mentee->delete();

        // Redirect ke index dengan success message
        return redirect()->route('admin.mentees.index')
                        ->with('success', 'Mentee berhasil dihapus.');
    }

    /**
     * Menghapus semua mentees dari database
     *
     * WARNING: DANGEROUS OPERATION
     * - Delete semua users dengan role = Mentee
     * - NO validation, NO confirmation
     * - Semua mentee data hilang selamanya
     *
     * Proses:
     * 1. Find Mentee role ID
     * 2. Delete semua User dengan role_id = menteeRoleId
     * 3. Redirect ke index dengan success message
     *
     * Security considerations:
     * - Only super admin should access this
     * - Require password confirmation
     * - Implement soft delete untuk safety
     * - Add audit logging
     *
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function destroyAll()
    {
        // Find Mentee role ID
        $menteeRole = Role::where('name', 'Mentee')->first();

        // Delete semua mentees jika role ketemu
        if ($menteeRole) {
            User::where('role_id', $menteeRole->id)->delete();
        }

        // Redirect ke index dengan success message
        return redirect()->route('admin.mentees.index')
                        ->with('success', 'Semua data mentee berhasil dihapus.');
    }

    /**
     * Menghapus multiple selected mentees dari database
     *
     * Proses:
     * 1. Validasi request: ids (required array), ids.* (exists:users,id)
     * 2. Find Mentee role ID
     * 3. Delete users dengan role = mentee AND id in request ids
     * 4. Count jumlah yang ter-delete
     * 5. Redirect ke index dengan success message (include delete count)
     *
     * Validasi (inline):
     * - ids: required, array (must be array)
     * - ids.*: exists:users,id (each id must exist in users table)
     * - Ensure semua selected ids valid sebelum delete
     *
     * Delete logic:
     * - Query: User::where('role_id', $menteeRoleId)->whereIn('id', $ids)->delete()
     * - Only delete users yang role_id = mentee (safety check)
     * - whereIn('id', $ids) untuk delete multiple selected
     * - Return count untuk feedback ke user
     *
     * @param Request $request Request dengan ids array
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success & count
     */
    public function bulkDestroy(Request $request)
    {
        // Validasi request: ids array, setiap id exists di users table
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id',
        ]);

        // Find Mentee role ID
        $menteeRole = Role::where('name', 'Mentee')->first();

        // Init count
        $count = 0;

        // Delete selected mentees jika role ketemu
        if ($menteeRole) {
            $count = User::where('role_id', $menteeRole->id)
                        ->whereIn('id', $request->input('ids'))
                        ->delete();
        }

        // Redirect ke index dengan success message & delete count
        return redirect()->route('admin.mentees.index')
                        ->with('success', "Berhasil menghapus {$count} mentee terpilih.");
    }
}
