<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Transaction;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'evaluator_id',
        'evaluated_id',
        'score',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    public function evaluator()
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function evaluated()
    {
        return $this->belongsTo(User::class, 'evaluated_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

}
