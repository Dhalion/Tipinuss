<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Organisation;
use Illuminate\Database\Eloquent\Collection;

interface OrganisationRepositoryInterface
{
    public function findById(string $id): ?Organisation;

    /** @return Collection<int, Organisation> */
    public function findAll(): Collection;

    public function save(Organisation $organisation): Organisation;

    public function delete(Organisation $organisation): void;
}
