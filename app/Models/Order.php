<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, HasUuids;

    public const SIZES = ['S', 'M', 'L', 'XL', 'XXL'];

    public const STATUS_POTONGAN_SELESAI = 'potongan_selesai';
    public const STATUS_DALAM_JAHIT = 'dalam_jahit';
    public const STATUS_QC_AWAL = 'qc_awal';
    public const STATUS_PENDING_PERMAK = 'pending_permak';
    public const STATUS_PENDING_CUCI = 'pending_cuci';
    public const STATUS_QC_ULANG = 'qc_ulang';
    public const STATUS_PACKING = 'packing';
    public const STATUS_DIKIRIM = 'dikirim';

    protected $fillable = [
        'kode',
        'original_kode',
        'tanggal',
        'style',
        'warna',
        'total',
        'penjahit',
        'status',
        'delivered_at',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'delivered_at' => 'datetime',
    ];

    public static function statuses(): array
    {
        return [
            self::STATUS_POTONGAN_SELESAI,
            self::STATUS_DALAM_JAHIT,
            self::STATUS_QC_AWAL,
            self::STATUS_PENDING_PERMAK,
            self::STATUS_PENDING_CUCI,
            self::STATUS_QC_ULANG,
            self::STATUS_PACKING,
            self::STATUS_DIKIRIM,
        ];
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['kode'] ?? null, fn (Builder $query, string $kode) => $query->where('kode', 'like', "%{$kode}%"))
            ->when($filters['penjahit'] ?? null, fn (Builder $query, string $penjahit) => $query->where('penjahit', 'like', "%{$penjahit}%"))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['tanggal_mulai'] ?? null, fn (Builder $query, string $date) => $query->whereDate('tanggal', '>=', $date))
            ->when($filters['tanggal_selesai'] ?? null, fn (Builder $query, string $date) => $query->whereDate('tanggal', '<=', $date));
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(OrderSize::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(OrderHistory::class);
    }
}
