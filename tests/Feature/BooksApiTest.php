<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

use function PHPUnit\Framework\assertNotContainsEquals;
use function PHPUnit\Framework\assertNotCount;

class BooksApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function can_get_all_books()
    {
        $books = Book::factory(4)->create();
        // dd($books->count());
        // $this->get('api/books')->dump();
        $this->getJson(route('books.index'))
            ->assertJsonFragment([
                'title' => $books[0]->title,
            ])->assertJsonFragment([
                'title' => $books[1]->title,
            ]);
    }

    /** @test */
    function can_get_one_book()
    {
        $book = Book::factory()->create();
        // dd(route('books.show', $book));
        $this->getJson(route('books.show', $book))
            ->assertJsonFragment([
                'title' => $book->title,
            ]);
    }

    /** @test */
    function can_create_books()
    {
        $this->postJson(route('books.store', []))
            ->assertJsonValidationErrorFor('title');

        $this->postJson(route('books.store', [
            'title' => 'Mi nuevo libro',
        ]))->assertJsonFragment([
            'title' => 'Mi nuevo libro',
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Mi nuevo libro',
        ]);
    }

    /** @test */
    function can_update_books()
    {
        $book = Book::factory()->create();

        $this->patchJson(route('books.update', $book))
            ->assertJsonValidationErrorFor('title');

        $this->patchJson(route('books.update', $book), [
            'title' => 'Libro editado'
        ])->assertJsonFragment([
            'title' => 'Libro editado'
        ]);

        $this->assertDatabaseHas('books', [
            'title' => 'Libro editado'
        ]);
    }

    /** @test */
    function can_delete_books()
    {
        $book = Book::factory()->create();
        $this->deleteJson(route('books.destroy', $book))
            ->assertNoContent();

        $this->assertDatabaseCount('books', 0);
    }
}
