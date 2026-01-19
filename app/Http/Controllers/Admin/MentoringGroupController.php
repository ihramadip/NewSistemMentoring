<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MentoringGroup;
use App\Models\User;
use App\Models\Level;

/**
 * MentoringGroupController
 *
 * Controller untuk manage mentoring groups (MODULE B #4: Mentoring Group Management)
 * Admin dapat create, read, update, delete kelompok mentoring
 *
 * Fitur:
 * - Index: list semua mentoring groups dengan mentor & level info, paginated 10
 * - Create: show form untuk create group baru, dropdown mentors & levels
 * - Store: save group baru, validate name unique, mentor_id & level_id exists
 * - Show: display group detail dengan mentor & members (eager load faculty)
 * - Edit: show form untuk edit group, manage mentee members via checkbox/select
 * - Update: update group info & sync mentees (many-to-many via group_members pivot)
 * - Delete: hapus group (cascade delete related data)
 *
 * Data structure:
 * - MentoringGroup: name, mentor_id, level_id, schedule_info
 * - Relationship: mentor (belongsTo User), level (belongsTo Level)
 * - Relationship: members (belongsToMany User via group_members pivot)
 * - Pivot: group_members table (group_id, user_id)
 *
 * Members management:
 * - members() adalah many-to-many relationship
 * - Use sync() untuk update/manage members (replace old dengan new)
 * - Sync dengan empty array [] untuk remove all members
 * - Eager load: members.faculty untuk display faculty info
 *
 * Mentor & Mentee filtering:
 * - Query users dengan whereHas('role') untuk filter by role name
 * - Mentors: role name = 'Mentor'
 * - Mentees: role name = 'Mentee'
 * - Dynamic filtering via role relationship (more flexible than role_id)
 *
 * Flow:
 * 1. Admin view mentoring groups (index)
 * 2. Admin create group baru (create form, select mentor & level)
 * 3. Auto-grouping process add members automatically (AutoGroupingService)
 * 4. Admin manually edit group & manage members (edit form)
 * 5. Admin delete group (cascade delete atau soft delete)
 *
 * @package App\Http\Controllers\Admin
 */
class MentoringGroupController extends Controller
{
    /**
     * Menampilkan list semua mentoring groups
     *
     * Proses:
     * 1. Query semua mentoring groups dengan eager load mentor & level
     * 2. Paginate dengan 10 records per halaman
     * 3. Return view dengan list groups
     *
     * Eager loading:
     * - mentor: user yang jadi mentor (belongsTo User)
     * - level: tingkat kemampuan (belongsTo Level)
     * - Untuk mencegah N+1 query problem di view
     *
     * @return \Illuminate\View\View View list mentoring groups
     */
    public function index()
    {
        // Query semua groups dengan eager load mentor & level, paginate 10
        $mentoringGroups = MentoringGroup::with(['mentor', 'level'])->paginate(10);

        // Return view dengan groups list
        return view('admin.mentoring-groups.index', compact('mentoringGroups'));
    }

    /**
     * Menampilkan form untuk create mentoring group baru
     *
     * Proses:
     * 1. Fetch semua levels dari database
     * 2. Fetch semua users dengan role = 'Mentor'
     * 3. Return create view dengan levels & mentors dropdown
     *
     * Query mentors:
     * - Use whereHas('role') untuk filter users by role relationship
     * - Filter: role name = 'Mentor'
     * - Dynamic filtering via role name (lebih flexible)
     *
     * Data:
     * - levels: untuk dropdown pemilihan level group
     * - mentors: untuk dropdown pemilihan mentor group leader
     *
     * @return \Illuminate\View\View View form create group
     */
    public function create()
    {
        // Fetch semua levels untuk dropdown
        $levels = Level::all();

        // Fetch mentors: users dengan role name = 'Mentor'
        $mentors = User::whereHas('role', function ($query) {
            $query->where('name', 'Mentor');
        })->get();

        // Return create view dengan levels & mentors
        return view('admin.mentoring-groups.create', compact('levels', 'mentors'));
    }

    /**
     * Menyimpan mentoring group baru ke database
     *
     * Proses:
     * 1. Validasi input:
     *    - name: required, string, max 255, unique di mentoring_groups table
     *    - mentor_id: required, exists in users table
     *    - level_id: required, exists in levels table
     *    - schedule_info: optional, string, max 255 (jadwal grup)
     * 2. Create MentoringGroup record dengan validated data
     * 3. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - name: required, string, max:255, unique:mentoring_groups,name
     * - mentor_id: required, exists:users,id
     * - level_id: required, exists:levels,id
     * - schedule_info: nullable, string, max:255
     *
     * Note:
     * - Members tidak di-assign saat create
     * - Members di-add via auto-grouping process atau manual edit
     * - Schedule_info: informasi jadwal grup (optional)
     *
     * @param Request $request Form request dengan name, mentor_id, level_id, schedule_info
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function store(Request $request)
    {
        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:mentoring_groups,name',
            'mentor_id' => 'required|exists:users,id',
            'level_id' => 'required|exists:levels,id',
            'schedule_info' => 'nullable|string|max:255',
        ]);

        // Create new mentoring group
        MentoringGroup::create($validatedData);

        // Redirect ke index dengan success message
        return redirect()->route('admin.mentoring-groups.index')
                        ->with('success', 'Kelompok mentoring berhasil ditambahkan.');
    }

    /**
     * Menampilkan detail mentoring group
     *
     * Proses:
     * 1. MentoringGroup di-resolve via route model binding
     * 2. Eager load mentor (User), members dengan faculty
     * 3. Return view dengan group detail
     *
     * Data:
     * - mentoringGroup: group info (name, schedule_info, created_at, etc)
     * - mentor: leader user info
     * - members: list of mentees dengan faculty info
     * - level: tingkat group
     *
     * Eager loading:
     * - mentor: mentor user (belongsTo User)
     * - members.faculty: mentee users & faculty mereka (many-to-many dengan eager load)
     * - Untuk display all info di view tanpa N+1 queries
     *
     * @param MentoringGroup $mentoringGroup MentoringGroup model via route binding
     * @return \Illuminate\View\View View group detail
     */
    public function show(MentoringGroup $mentoringGroup)
    {
        // Eager load mentor & members dengan faculty
        $mentoringGroup->load(['mentor', 'members.faculty']);

        // Return view dengan group detail
        return view('admin.mentoring-groups.show', compact('mentoringGroup'));
    }

    /**
     * Menampilkan form untuk edit mentoring group
     *
     * Proses:
     * 1. MentoringGroup di-resolve via route model binding
     * 2. Fetch semua levels untuk dropdown
     * 3. Fetch semua mentors (users dengan role = 'Mentor')
     * 4. Fetch semua mentees (users dengan role = 'Mentee')
     * 5. Return edit view dengan group, levels, mentors, & mentees
     *
     * Data:
     * - mentoringGroup: current group untuk populate form
     * - levels: dropdown untuk change group level
     * - mentors: dropdown untuk change group mentor
     * - mentees: list all mentees untuk checkbox/select (manage members)
     *
     * Admin dapat:
     * - Change group name, mentor, level, schedule_info
     * - Manage members: add/remove mentees dari group
     * - Use sync() untuk update members
     *
     * @param MentoringGroup $mentoringGroup MentoringGroup model via route binding
     * @return \Illuminate\View\View View form edit group
     */
    public function edit(MentoringGroup $mentoringGroup)
    {
        // Fetch semua levels untuk dropdown
        $levels = Level::all();

        // Fetch mentors: users dengan role name = 'Mentor'
        $mentors = User::whereHas('role', function ($query) {
            $query->where('name', 'Mentor');
        })->get();

        // Fetch mentees: users dengan role name = 'Mentee'
        $mentees = User::whereHas('role', function ($query) {
            $query->where('name', 'Mentee');
        })->get();

        // Return edit view dengan group, levels, mentors, mentees
        return view('admin.mentoring-groups.edit', compact('mentoringGroup', 'levels', 'mentors', 'mentees'));
    }

    /**
     * Memperbarui mentoring group di database
     *
     * Proses:
     * 1. Validasi input:
     *    - name: required, string, max 255, unique (except current group)
     *    - mentor_id: required, exists in users table
     *    - level_id: required, exists in levels table
     *    - schedule_info: optional, string, max 255
     *    - mentee_ids: optional, array (list of mentee ids to assign)
     *    - mentee_ids.*: exists in users table (validate each id)
     * 2. Update MentoringGroup record: name, mentor_id, level_id, schedule_info
     * 3. Sync members (many-to-many): update group_members pivot table
     *    - sync() replace old members dengan new (dari mentee_ids)
     *    - sync([]) untuk remove all members
     *    - sync(mentee_ids) untuk set members ke list yang dipilih
     * 4. Redirect ke index dengan success message
     *
     * Validasi (inline):
     * - name: required, string, max:255, unique:mentoring_groups,name,{id}
     * - mentor_id: required, exists:users,id
     * - level_id: required, exists:levels,id
     * - schedule_info: nullable, string, max:255
     * - mentee_ids: nullable, array (optional jika tidak manage members)
     * - mentee_ids.*: exists:users,id (setiap id harus valid)
     *
     * sync() behavior:
     * - Replace: hapus member lama yang tidak di-select, add member baru
     * - Empty array []: remove semua members (kosongkan group)
     * - Efficient: hanya perubahan yang di-save (tidak full delete-insert)
     *
     * @param Request $request Form request dengan group info & mentee_ids
     * @param MentoringGroup $mentoringGroup MentoringGroup model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function update(Request $request, MentoringGroup $mentoringGroup)
    {
        // Validasi input
        $validatedData = $request->validate([
            'name' => 'required|string|max:255|unique:mentoring_groups,name,' . $mentoringGroup->id,
            'mentor_id' => 'required|exists:users,id',
            'level_id' => 'required|exists:levels,id',
            'schedule_info' => 'nullable|string|max:255',
            'mentee_ids' => 'nullable|array',
            'mentee_ids.*' => 'exists:users,id',
        ]);

        // Update group info
        $mentoringGroup->update([
            'name' => $validatedData['name'],
            'mentor_id' => $validatedData['mentor_id'],
            'level_id' => $validatedData['level_id'],
            'schedule_info' => $validatedData['schedule_info'],
        ]);

        // Sync mentees (many-to-many via group_members pivot)
        // sync() replace old members dengan new dari mentee_ids
        // sync([]) jika mentee_ids kosong (remove all members)
        $mentoringGroup->members()->sync($validatedData['mentee_ids'] ?? []);

        // Redirect ke index dengan success message
        return redirect()->route('admin.mentoring-groups.index')
                        ->with('success', 'Kelompok mentoring berhasil diperbarui.');
    }

    /**
     * Menghapus mentoring group dari database
     *
     * Proses:
     * 1. MentoringGroup di-resolve via route model binding
     * 2. Delete MentoringGroup record
     * 3. Cascade delete: members akan di-remove dari group_members pivot
     * 4. Redirect ke index dengan success message
     *
     * Cascade deletion:
     * - MentoringGroup delete -> group_members pivot records delete
     * - MentoringGroup delete -> sessions (jika ada, depends on FK settings)
     * - Users (mentees) tidak di-delete, hanya removed dari group
     *
     * WARNING:
     * - Jika ada sessions atau meetings ter-record, pertimbangkan soft delete
     * - Atau implement archive/deactivate logic
     * - Maintain history untuk audit purposes
     *
     * @param MentoringGroup $mentoringGroup MentoringGroup model via route binding
     * @return \Illuminate\Http\RedirectResponse Redirect ke index dengan success
     */
    public function destroy(MentoringGroup $mentoringGroup)
    {
        // Delete mentoring group record
        $mentoringGroup->delete();

        // Redirect ke index dengan success message
        return redirect()->route('admin.mentoring-groups.index')
                         ->with('success', 'Kelompok mentoring berhasil dihapus.');
    }
}
