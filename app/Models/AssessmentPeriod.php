<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssessmentPeriod extends Model
{
    use HasFactory;

    protected $table = 'assessment_periods';

    protected $fillable = [
        'nama',
        'tipe',
        'tahun_ajaran',
        'is_active',
        'keterangan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Dapatkan model periode yang sedang aktif
     */
    public static function getActivePeriod(): ?self
    {
        return self::where('is_active', true)->first();
    }

    /**
     * Dapatkan nama periode yang aktif (dengan fallback ke semester berjalan)
     */
    public static function getActivePeriodName(): string
    {
        $active = self::getActivePeriod();
        if ($active) {
            return $active->nama;
        }

        $y = date('Y');
        $ny = (int)$y + 1;
        return "Semester Ganjil {$y}/{$ny}";
    }

    /**
     * Set satu periode menjadi aktif dan non-aktifkan yang lain
     */
    public static function setActiveById($id): bool
    {
        self::query()->update(['is_active' => false]);
        $period = self::find($id);
        if ($period) {
            $period->is_active = true;
            $period->save();
            return true;
        }
        return false;
    }
}
