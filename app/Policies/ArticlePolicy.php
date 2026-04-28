<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Article;

class ArticlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('view articles');
    }

    public function view(User $user, Article $article): bool
    {
        return $user->can('view articles');
    }

    public function create(User $user): bool
    {
        return $user->can('create articles');
    }

    public function update(User $user, Article $article): bool
    {
        // Admin bisa edit semua, Editor bisa edit artikel sendiri, Author hanya milik sendiri
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('editor')) {
            return $user->id === $article->user_id;
        }

        if ($user->hasRole('author')) {
            return $user->id === $article->user_id;
        }

        return false;
    }

    public function delete(User $user, Article $article): bool
    {
        // Hanya admin yang bisa hapus
        return $user->hasRole('admin');
    }

    public function publish(User $user, Article $article): bool
    {
        // Admin dan editor bisa publish
        if ($user->hasRole('admin')) {
            return true;
        }

        if ($user->hasRole('editor')) {
            return true;
        }

        return false;
    }
}