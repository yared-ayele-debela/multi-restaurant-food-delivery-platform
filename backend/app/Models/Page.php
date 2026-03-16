<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Page extends Model
{
    protected $fillable = ['title', 'slug', 'is_home'];

    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }
}
