<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArabuluculukFile extends Model
{
    protected $table = 'arabuluculuk_files';
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'locked' => 'boolean',
        'archived_at' => 'datetime',
    ];

    public function case()
    {
        // Dosyanın bağlı olduğu ana davayı (Case) bulur
        return $this->belongsTo(ArabuluculukCase::class, 'case_id');
    }

    public function uploader() {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}