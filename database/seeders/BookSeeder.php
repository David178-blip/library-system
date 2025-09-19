<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        Book::create([
            'title' => 'Introduction to Programming',
            'author' => 'John Smith',
            'isbn' => '9781234567890',
            'copies' => 5,
        ]);

        Book::create([
            'title' => 'Database Systems',
            'author' => 'Jane Doe',
            'isbn' => '9789876543210',
            'copies' => 3,
        ]);

        Book::create([
            'title' => 'Artificial Intelligence Basics',
            'author' => 'Alan Turing',
            'isbn' => '9781112223334',
            'copies' => 7,
        ]);
    }
}
