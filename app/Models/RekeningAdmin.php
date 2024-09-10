<?php

namespace App\Models;

use App\Models\Bank;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RekeningAdmin extends Model
{
    use HasFactory;
    protected $table = 'rekening_admin';
    protected $fillable = ['bank_id', 'no_rekening', 'atas_nama'];

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
}
