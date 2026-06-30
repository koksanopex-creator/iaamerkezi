<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkMailRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'bulk_mail_log_id',
        'user_id',
        'status',
        'error_message',
    ];

    public function log()
    {
        return $this->belongsTo(BulkMailLog::class, 'bulk_mail_log_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
