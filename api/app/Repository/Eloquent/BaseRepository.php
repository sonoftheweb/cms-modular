<?php


namespace App\Repository\Eloquent;


use App\Repository\EloquentRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BaseRepository implements EloquentRepositoryInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function create(array $attributes): Model
    {
        return $this->model->create($attributes);
    }

    public function find($id, $relations = []): ?Collection
    {
        $data = $this->model->find($id);

        if (!$data)
            return collect([]);

        if (!empty($relations))
            $data->load($relations);

        $resourceClass = $data->getModel()->dataResource['resource'];
        $data = collect(new $resourceClass($data));

        return $data;
    }

    public function getWhere(string $column, string $data): ?Collection
    {
        return $this->model::where($column, $data)->get();
    }
}
