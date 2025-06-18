<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    protected $fillable = [
        'categorie_id',
        'name',
        'format_nomor',
        'file_path',
    ];

    
    public function category()
    {
        return $this->belongsTo(Category::class, 'categorie_id');
    }
        public function documents()
    {
        return $this->hasMany(Document::class);
    }
}