<?php

namespace App\Policies;

use App\Models\Expense;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ExpensePolicy
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
