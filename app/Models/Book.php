<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    // 'copies' = total physical copies for this title
    protected $fillable = [
        'title',
        'author',
        'year',
        'copies',
        'course',
        'accession_number',
    ];

    // Individual physical copies (each has an accession_number)
    public function copies()
    {
        return $this->hasMany(BookCopy::class);
    }

    // Only available physical copies
    public function availableCopies()
    {
        return $this->hasMany(BookCopy::class)->where('status', 'available');
    }

    public function borrows()
    {
        return $this->hasMany(Borrow::class);
    }
}
