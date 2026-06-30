<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataCalibrationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'type',
        'old_value',
        'new_value',
        'description',
        'causer_id'
    ];

    public function causer()
    {
        return $this->belongsTo(User::class, 'causer_id');
    }

    public function getModelInstance()
    {
        $class = "App\\Models\\" . $this->model_type;
        if (class_exists($class)) {
            return $class::find($this->model_id);
        }
        return null;
    }
}
