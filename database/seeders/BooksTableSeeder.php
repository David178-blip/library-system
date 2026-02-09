<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Book;

class BooksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $books = [
            [
                'title' => 'C++ programming program design including data structure',
                'author' => 'D.S. Malik',
                'year' => 2014,
                'course' => 'BSIT',
                'copies' => 1,
            ],
            [
                'title' => 'Web Design',
                'author' => 'Laing,Roger & Lewis, Rhys',
                'year' => 2002,
                'course' => 'BSIT',
                'copies' => 1,
            ],
            [
                'title' => 'Introduction to Linguistics',
                'author' => 'Fromkin, Victoria[et. al.]',
                'year' => 2010,
                'course' => 'BSED',
                'copies' => 1,
            ],
            [
                'title' => 'The Teacher and the School Curriculum',
                'author' => 'Bilbao,Purita',
                'year' => 2020,
                'course' => 'BSED',
                'copies' => 1,
            ],
            [
                'title' => 'Introduction to Criminology 3rd ed.',
                'author' => 'Lagumen, Dennis D.',
                'year' => 2023,
                'course' => 'BSCRIM',
                'copies' => 1,
            ],
            [
                'title' => 'Firearms and firearms safety',
                'author' => 'Peckley, Miller F.',
                'year' => 2013,
                'course' => 'BSCRIM',
                'copies' => 1,
            ],
            [
                'title' => 'Principles of marketing',
                'author' => 'Lamb, Charles[et.al.]',
                'year' => 2013,
                'course' => 'BSBA',
                'copies' => 1,
            ],
            [
                'title' => 'Marketing management',
                'author' => 'Marshall,Greg',
                'year' => 2010,
                'course' => 'BSBA',
                'copies' => 1,
            ],
            [
                'title' => 'The Teacher and School Curriculum',
                'author' => 'Bilbao, Purita P.',
                'year' => 2020,
                'course' => 'BEED',
                'copies' => 1,
            ],
            [
                'title' => 'Effective teaching',
                'author' => 'Aquino,Gaudencio V.',
                'year' => 2003,
                'course' => 'BEED',
                'copies' => 1,
            ],
        ];

        foreach ($books as $book) {
            Book::create($book);
        }
    }
}
