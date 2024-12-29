<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    public function update(User $user, Expense $expense)
    {
        return $expense->user_id === $user->id;
    }
    
    public function delete(User $user, Expense $expense)
    {
        return $expense->user_id === $user->id;
    }
    
}
