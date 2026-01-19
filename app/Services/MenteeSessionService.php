<?php

namespace App\Services;

use App\Models\User;
use App\Models\Session;
use App\Models\AdditionalSession;

/**
 * MenteeSessionService
 *
 * Service untuk mengelola sesi mentoring yang diikuti oleh mentee (MODULE C: Mentoring Session System)
 * Menyediakan fungsi untuk mendapatkan semua sesi wajib dan tambahan untuk mentee
 *
 * Fungsi utama:
 * - getSessions(): Mendapatkan semua sesi wajib dan tambahan untuk mentee
 *
 * @package App\Services
 */
class MenteeSessionService
{
    /**
     * Mendapatkan semua sesi wajib dan tambahan untuk grup mentee yang diberikan
     *
     * Proses:
     * 1. Ambil grup mentoring dari mentee
     * 2. Jika mentee tidak memiliki grup, kembalikan array kosong
     * 3. Ambil semua sesi wajib untuk grup mentee ini dengan data kehadiran dan laporan progres mentee
     * 4. Ambil semua sesi tambahan untuk mentee ini
     * 5. Kembalikan array berisi sesi wajib, sesi tambahan, dan informasi grup
     *
     * @param User $mentee Instance mentee yang akan diambil sesinya
     * @return array Array berisi sesi wajib, sesi tambahan, dan informasi grup mentoring
     */
    public function getSessions(User $mentee): array
    {
        // Ambil grup mentoring dari mentee
        $mentoringGroup = $mentee->mentoringGroupsAsMentee()->first();

        if (!$mentoringGroup) {
            // Jika mentee tidak memiliki grup, kembalikan array kosong
            return [
                'sessions' => collect(), // Koleksi sesi wajib kosong
                'additionalSessions' => collect(), // Koleksi sesi tambahan kosong
                'mentoringGroup' => null, // Informasi grup mentoring kosong
            ];
        }

        // Ambil semua sesi wajib untuk grup mentee ini
        // Termasuk data kehadiran dan laporan progres khusus untuk mentee ini
        $sessions = Session::where('mentoring_group_id', $mentoringGroup->id)
            ->with([
                'attendances' => function ($query) use ($mentee) {
                    // Ambil kehadiran hanya untuk mentee ini
                    $query->where('mentee_id', $mentee->id);
                },
                'progressReports' => function ($query) use ($mentee) {
                    // Ambil laporan progres hanya untuk mentee ini
                    $query->where('mentee_id', $mentee->id);
                }
            ])
            ->orderBy('date', 'asc') // Urutkan berdasarkan tanggal terlama ke terbaru
            ->get();

        // Ambil semua sesi tambahan untuk mentee ini (kembali ke logika asli)
        $additionalSessions = AdditionalSession::where('mentee_id', $mentee->id)
            ->orderBy('date', 'asc') // Urutkan berdasarkan tanggal terlama ke terbaru
            ->get();

        // Kembalikan array berisi sesi wajib, sesi tambahan, dan informasi grup
        return [
            'sessions' => $sessions, // Koleksi sesi wajib
            'additionalSessions' => $additionalSessions, // Koleksi sesi tambahan
            'mentoringGroup' => $mentoringGroup, // Informasi grup mentoring
        ];
    }
}
