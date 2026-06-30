<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportUserAuthorization extends Model
{
    protected $table = 'report_user_authorizations';

    protected $fillable = [
        'report_name',
        'user_id',
        'data_scope',
        'specific_department_ids',
    ];

    protected function casts(): array
    {
        return [
            'specific_department_ids' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
