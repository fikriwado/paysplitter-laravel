<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentType extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'payment_types';

    protected $guarded = ['id'];

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }
}
