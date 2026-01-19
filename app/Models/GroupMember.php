<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * GroupMember Model
 *
 * Model untuk pivot table/junction table relasi many-to-many antara User & MentoringGroup
 * (MODULE B #7: Pengelompokan Mentee)
 *
 * Pivot table yang menyimpan data keanggotaan mentee di kelompok mentoring
 * Berisi metadata tentang hubungan mentee-group seperti status & timestamp join
 *
 * Attributes:
 * - mentee_id: Foreign key ke User (mentee yang bergabung)
 * - mentoring_group_id: Foreign key ke MentoringGroup (kelompok yang di-join)
 * - timestamps: created_at & updated_at (track kapan mentee bergabung)
 *
 * Database table: group_members
 * Digunakan via relasi many-to-many di User & MentoringGroup model
 *
 * @package App\Models
 */
class GroupMember extends Model
{
    // Pivot table - relasi many-to-many antara User & MentoringGroup
    // Tidak perlu $fillable karena dikelola via relationship attach/detach
}
