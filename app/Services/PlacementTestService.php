<?php

namespace App\Services;

use App\Models\User;
use App\Models\PlacementTest;
use App\Models\PlacementTestDefinition;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * PlacementTestService
 *
 * Service untuk mengelola proses placement test (MODULE B: Mentee Management)
 * Menyediakan fungsi untuk menangani pengumpulan jawaban teori dan rekaman audio
 * serta menghitung skor placement test
 *
 * Fungsi utama:
 * - handleSubmission(): Menangani pengumpulan placement test
 * - calculateTheoryScore(): Menghitung skor teori berdasarkan jawaban pengguna
 *
 * @package App\Services
 */
class PlacementTestService
{
    /**
     * Menangani pengumpulan placement test
     *
     * Proses:
     * 1. Gunakan transaksi database untuk memastikan integritas data
     * 2. Hitung skor teori berdasarkan jawaban pengguna
     * 3. Simpan file audio ke storage
     * 4. Buat record placement test dengan skor teori dan path file audio
     * 5. Jika terjadi kesalahan, bersihkan file audio yang tersisa dan lempar exception
     *
     * @param User $user Pengguna yang mengumpulkan tes
     * @param array $answers Jawaban pengguna untuk pertanyaan teori
     * @param UploadedFile $audioFile Rekaman audio dari pengguna
     * @return PlacementTest Record pengumpulan placement test yang dibuat
     * @throws \Exception Jika terjadi kesalahan saat proses pengumpulan
     */
    public function handleSubmission(User $user, array $answers, UploadedFile $audioFile): PlacementTest
    {
        $path = null;
        try {
            // Gunakan transaksi untuk memastikan integritas data
            // Jika ada langkah yang gagal, seluruh proses akan di-rollback
            $submission = DB::transaction(function () use ($user, $answers, $audioFile, &$path) {

                // 1. Hitung Skor Teori
                $theory_score = $this->calculateTheoryScore($answers);

                // 2. Simpan File Audio
                // File disimpan di direktori private
                $path = $audioFile->store('placement-tests/audio', 'local');
                if (!$path) {
                    throw new \Exception("Gagal menyimpan file audio untuk pengguna {$user->id}.");
                }

                // 3. Buat Record Placement Test
                return PlacementTest::create([
                    'mentee_id' => $user->id,                    // ID mentee yang mengikuti tes
                    'audio_recording_path' => $path,            // Path file rekaman audio
                    'theory_score' => $theory_score,            // Skor teori yang dihitung
                    'audio_reading_score' => null,              // Skor bacaan, akan dinilai oleh admin
                    'final_level_id' => null,                   // Level akhir, akan ditentukan oleh admin
                ]);
            });

            return $submission;

        } catch (\Throwable $e) {
            // Jika transaksi gagal, coba bersihkan file audio yang tersisa
            if ($path && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }

            // Log error detail untuk debugging
            Log::error('Placement test submission failed for user ' . $user->id . ': ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            // Lempar ulang exception agar ditangani oleh controller
            throw new \Exception('Terjadi kesalahan tak terduga saat mengumpulkan tes. Silakan coba lagi.');
        }
    }

    /**
     * Menghitung skor teori berdasarkan jawaban pengguna
     *
     * Proses:
     * 1. Ambil definisi placement test beserta pertanyaan dan opsi jawaban
     * 2. Untuk setiap pertanyaan, cocokkan jawaban pengguna dengan opsi yang benar
     * 3. Hitung total skor berdasarkan nilai skor untuk setiap jawaban benar
     * 4. Kembalikan persentase skor dari total nilai maksimum
     *
     * @param array $userAnswers Jawaban dari pengguna
     * @return float Skor teori dalam bentuk persentase (0-100)
     */
    private function calculateTheoryScore(array $userAnswers): float
    {
        // Di skenario dunia nyata, Anda mungkin ingin meng-cache ini
        $definition = PlacementTestDefinition::with('questions.options')->firstOrFail();

        $score = 0;          // Skor total untuk jawaban benar
        $totalValue = 0;     // Total nilai maksimum dari semua pertanyaan

        foreach ($definition->questions as $question) {
            $totalValue += $question->score_value;  // Tambahkan nilai skor pertanyaan ke total
            $correctOption = $question->options->firstWhere('is_correct', true);  // Cari opsi yang benar

            // Jika pengguna menjawab dengan opsi yang benar, tambahkan nilai skor ke skor total
            if ($correctOption && isset($userAnswers[$question->id]) && $userAnswers[$question->id] === $correctOption->option_text) {
                $score += $question->score_value;
            }
        }

        if ($totalValue === 0) {
            return 0;  // Hindari pembagian dengan nol
        }

        // Skor dihitung berdasarkan jumlah nilai_skor, bukan jumlah pertanyaan
        return ($score / $totalValue) * 100;
    }
}
