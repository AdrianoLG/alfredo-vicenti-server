<?php

namespace App\Services;

use App\Models\Book;
use App\Models\User;

class BookService
{
    private $model;

    public function __construct()
    {
        $this->model = new Book();
    }

    public function getBooks(int $user_id)
    {
        $user = User::find($user_id);
        return $user->books;
    }

    public function postBook(array $book)
    {
        $this->model->create($book);
    }

    public function putBook(int $user_id, int $id, array $book)
    {
        $user = User::find($user_id);
        $bookToUpdate = $user->books->find($id);

        if (!is_null($bookToUpdate)) {
            $bookToUpdate->update($book);
            return true;
        }
        return false;
    }

    public function deleteBook(int $user_id, int $id)
    {
        $user = User::find($user_id);
        $book = $user->books->find($id);

        if (!is_null($book)) {
            $book->delete();
            return true;
        }
        return false;
    }
}