<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentCategory extends Model
{
    use HasFactory;

    protected $table = 'assessment_categories';

    protected $fillable = [
        'nama',
        'deskripsi',
        'urutan',
        'is_active',
        'sub_points',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
        'sub_points' => 'array',
    ];

    /**
     * Mengembalikan list sub-poin yang sudah dibersihkan (array of string)
     */
    public function getSubPointsListAttribute(): array
    {
        if (is_array($this->sub_points)) {
            return array_values(array_filter(array_map('trim', $this->sub_points)));
        }

        if (is_string($this->sub_points)) {
            $decoded = json_decode($this->sub_points, true);
            if (is_array($decoded)) {
                return array_values(array_filter(array_map('trim', $decoded)));
            }
        }

        return [];
    }
}
