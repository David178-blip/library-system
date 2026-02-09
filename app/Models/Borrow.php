<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'accession_number', // which physical copy was borrowed
        'status',
        'borrowed_at',
        'due_at',
        'returned_at',
        'approval',
    ];

    protected $dates = [
        'borrowed_at',
        'due_at',
        'returned_at',
    ];

    // Make dates Carbon instances
    protected $casts = [
        'borrowed_at' => 'datetime',
        'due_at'      => 'datetime',
        'returned_at' => 'datetime',
    ];

    // For reports filtering by accession number
    public function scopeFilterByAccession($query, ?string $accessionNumber)
    {
        if ($accessionNumber) {
            $query->where('accession_number', 'like', '%' . $accessionNumber . '%');
        }

        return $query;
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}