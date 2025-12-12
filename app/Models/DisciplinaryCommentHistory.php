<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DisciplinaryCommentHistory extends Model
{
    protected $guarded = [];
    
    // İlişki: Bu geçmiş kaydı hangi kullanıcıya ait
    public function user() {
        return $this->belongsTo(User::class);
    }
}