<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model {
    use HasFactory;

  protected $fillable = [
    'num_volumes',
    'class_no',
    'title',
    'author',
    'publisher',
    'publication_place',
    'copyright_year',
];


    public function borrows() {
        return $this->hasMany(Borrow::class);
    }
}
