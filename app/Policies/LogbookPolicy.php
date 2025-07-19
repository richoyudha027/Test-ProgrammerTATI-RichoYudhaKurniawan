<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Logbook;
use Illuminate\Auth\Access\HandlesAuthorization;

class LogbookPolicy
{
    use HandlesAuthorization;
    /**
     * Create a new policy instance.
     */
    public function create()
    {
        return in_array($user->role, ['Kepala Bagian 1', 'Kepala Bagian 2', 'Staf Bagian 1', 'Staf Bagian 2']);
    }
    public function update(User $user, Logbook $logbook): bool
    {
        return $user->id === $logbook->user_id;
    }

    public function delete(User $user, Logbook $logbook): bool
    {
        return $user->id === $logbook->user_id;
    }
}
