<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * PlacementTestDefinition Model
 *
 * Model untuk mendefinisikan/membuat blueprint soal placement test (MODULE B #3)
 * Menyimpan definisi/template soal yang akan diberikan kepada mentee
 * Berbeda dengan PlacementTest yang menyimpan hasil/submission
 *
 * Attributes:
 * - name: Nama/judul dari placement test definition
 * - timestamps: created_at & updated_at otomatis
 *
 * Polymorphic Relation:
 * - Soal placement test disimpan di Question dengan questionable_type = PlacementTestDefinition
 * - Memungkinkan reuse question logic untuk multiple parent models (Exam & PlacementTestDefinition)
 *
 * @package App\Models
 */
class PlacementTestDefinition extends Model
{
    use HasFactory;

    /**
     * Attributes yang bisa di-mass assign
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Placement test definition punya banyak Question (polymorphic)
     * Soal-soal untuk placement test disimpan via polymorphic relation
     * Relasi polymorphic: questions.questionable_id & questions.questionable_type
     * Tipe: 'App\Models\PlacementTestDefinition'
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }
}