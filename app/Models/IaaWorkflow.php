<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IaaWorkflow extends Model
{
    use HasFactory;

    public function steps()
    {
        // Bir iş akışının birden çok adımı vardır. Adımları 'order' kolonuna göre sıralı getir.
        return $this->hasMany(\App\Models\IaaWorkflowStep::class)->orderBy('order');
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'is_default',
    ];
}