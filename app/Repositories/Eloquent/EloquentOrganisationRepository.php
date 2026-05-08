<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Organisation;
use App\Repositories\Contracts\OrganisationRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class EloquentOrganisationRepository implements OrganisationRepositoryInterface
{
    public function findById(string $id): ?Organisation
    {
        return Organisation::find($id);
    }

    public function findAll(): Collection
    {
        return Organisation::with('users')->orderBy('name')->get();
    }

    public function save(Organisation $organisation): Organisation
    {
        $organisation->save();

        return $organisation;
    }

    public function delete(Organisation $organisation): void
    {
        $organisation->delete();
    }
}
