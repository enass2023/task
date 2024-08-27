<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'uuid',
        'title',
        'content',
        'user_id',
    ];

    protected $casts = [
        'title' => 'string',
        'content'=>'string',
    
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class,'comments');
    }
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }




}
