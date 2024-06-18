<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

interface UserServiceInterface
{
    public function getUser(): Collection;

    public function addNewUser(array $params): void;

    public function showUser(int $id): void;

    public function editUser(Request $request, int $id): void;

    public function deleteUser(int $id): void;
}
