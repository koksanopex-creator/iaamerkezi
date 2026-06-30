<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BulkMailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'subject',
        'body',
        'total_recipients',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->hasMany(BulkMailRecipient::class);
    }
}
