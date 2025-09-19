<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrow extends Model
{
    protected $fillable = [
        'user_id',
        'book_id',
        'status',
        'borrowed_at',
        'due_at',
        'returned_at',
        'status',
    ];

       protected $dates = [
        'borrowed_at',
        'due_at',
        'returned_at',
    ];

    // 🔥 This makes borrowed_at and returned_at Carbon instances
protected $casts = [
    'borrowed_at' => 'datetime',
    'due_at' => 'datetime',
    'returned_at' => 'datetime',
];


    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
