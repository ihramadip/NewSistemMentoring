<?php

namespace App\Services;

use App\Models\Exam;
use Illuminate\Support\Facades\Auth;

/**
 * ExamService
 *
 * Service untuk mengelola operasi CRUD ujian (MODULE C: Mentoring Session System)
 * Menyediakan fungsi untuk membuat, memperbarui, dan menghapus ujian
 *
 * Fungsi utama:
 * - createExam(): Membuat ujian baru
 * - updateExam(): Memperbarui ujian yang sudah ada
 * - deleteExam(): Menghapus ujian
 *
 * @package App\Services
 */
class ExamService
{
    /**
     * Membuat ujian baru
     *
     * Proses:
     * 1. Tambahkan ID pengguna yang sedang login sebagai pembuat ujian
     * 2. Simpan data ujian ke database
     *
     * @param array $data Data yang telah divalidasi dari permintaan
     * @return Exam Instance ujian yang baru dibuat
     */
    public function createExam(array $data): Exam
    {
        // Tambahkan ID pengguna yang sedang login sebagai pembuat ujian
        return Exam::create($data + ['created_by' => Auth::id()]);
    }

    /**
     * Memperbarui ujian yang sudah ada
     *
     * Proses:
     * 1. Perbarui data ujian dengan data baru
     * 2. Simpan perubahan ke database
     *
     * @param Exam $exam Instance ujian yang akan diperbarui
     * @param array $data Data yang telah divalidasi dari permintaan
     * @return Exam Instance ujian yang telah diperbarui
     */
    public function updateExam(Exam $exam, array $data): Exam
    {
        // Perbarui data ujian dengan data baru
        $exam->update($data);
        return $exam;
    }

    /**
     * Menghapus ujian
     *
     * Proses:
     * 1. Hapus instance ujian dari database
     *
     * @param Exam $exam Instance ujian yang akan dihapus
     * @return void
     */
    public function deleteExam(Exam $exam): void
    {
        // Hapus instance ujian dari database
        $exam->delete();
    }
}
