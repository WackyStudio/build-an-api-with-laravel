<?php

namespace App\Policies;

use App\User;
use App\Book;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the book.
     *
     * @param  \App\User  $user
     * @param  \App\Book  $book
     * @return mixed
     */
    public function view(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can create books.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can update the book.
     *
     * @param  \App\User  $user
     * @param  \App\Book  $book
     * @return mixed
     */
    public function update(User $user, Book $book)
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can delete the book.
     *
     * @param  \App\User  $user
     * @param  \App\Book  $book
     * @return mixed
     */
    public function delete(User $user, Book $book)
    {
        return $user->role === 'admin';
    }

}
