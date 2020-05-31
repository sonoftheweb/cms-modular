<?php


namespace App\Repository\Eloquent;


use App\Models\UserAsModel;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\Collection;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function all(): Collection
    {
        return UserAsModel::all();
    }
}
