<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssessmentObservation extends Model
{
    use HasFactory;
    protected $guarded = [];

    // Relasi balik ke Penilaian Asesor
    public function assessorScore()
    {
        return $this->belongsTo(AssessorScore::class, 'assessor_score_id');
    }
}
