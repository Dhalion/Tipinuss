<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\RegisterData;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

final class RegisterUserAction
{
    public function __construct(private UserRepositoryInterface $users) {}

    public function execute(RegisterData $data): User
    {
        $user = new User([
            'name' => $data->name,
            'email' => $data->email,
        ]);

        $user->password = Hash::make($data->password);

        return $this->users->save($user);
    }
}
