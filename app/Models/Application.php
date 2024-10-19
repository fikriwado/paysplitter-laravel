<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'applications';

    protected $guarded = ['id'];

    public function paymentTypes(): HasMany
    {
        return $this->hasMany(PaymentType::class, 'application_id');
    }
}
