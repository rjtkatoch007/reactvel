<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $filable = [
        'title', 'body', 'user_id', 'image',
         'clapsCount', 'slug', 'excerpt', 'published'
    ];

    protected $appends = ['image_path'];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function tags(){
        return $this->belongsToMany(Tag::class);
    }

    public function getCreatedAtAttribute($value){

        return Carbon::parse($value)->diffForHumans();
    }

    public function getRouteKey(){
        return 'slug';
    }

    public function scopePublished($query){
        return $query->where('published', 1);
    }

    public function getImagePathAttribute(){

        return assert($this->image);
    }

}
