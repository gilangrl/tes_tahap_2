<?php

namespace App\Models;

use App\Models\Bank;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransaksiTransfer extends Model
{
    use HasFactory;
    protected $table = 'transaksi_transfer';
    protected $fillable = ['id', 'user_id', 'bank_tujuan_id', 'rekening_tujuan', 'kode_unik', 'jumlah_transfer'];

    public function bank_tujuan()
    {
        return $this->belongsTo(Bank::class, 'bank_tujuan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
