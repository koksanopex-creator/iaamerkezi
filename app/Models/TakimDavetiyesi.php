<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class TakimDavetiyesi extends Model
{
    use HasFactory;
    protected $table = 'takim_davetiyeleri';
    protected $fillable = ['takim_id', 'davet_eden_user_id', 'davet_edilen_user_id', 'durum', 'type'];
    
    public function takim() { return $this->belongsTo(Takim::class); }
    public function davetEden() { return $this->belongsTo(User::class, 'davet_eden_user_id'); }
    public function davetEdilen() { return $this->belongsTo(User::class, 'davet_edilen_user_id'); }
}