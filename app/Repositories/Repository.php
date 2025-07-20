<?php

namespace App\Repositories;

use App\Interfaces\BaseInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Repository implements BaseInterface
{
    protected Model $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;

    }

    /**
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->model->newQuery();
    }

    /*
     * @param mixed $id
     * @return Model
     */
    public function find($id)
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Summary of findAll
     * @return \Illuminate\Database\Eloquent\Collection<int, Model>
     */
    public function findAll()
    {
        return $this->model->all();
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function firstOrCreate(array $data): mixed
    {
        return $this->model->firstOrCreate($data);
    }

    /**
     * @param array $data
     * @return Model
     */
    public function create(array $data)
    {
        return $this->model->create($data);

    }

    /**
     * @param mixed $id
     * @param array $data
     * @return Model
     */
    public function update($id, array $data)
    {
        $model = $this->find($id);
        $model->update($data);
        return $model;
    }

    /**
     * @param mixed $id
     * @return bool
     */
    public function delete($id)
    {
        $model = $this->find($id);
        $model->delete();
        return
            true;
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return Model
     */
    public function firstWhere(string $column, $value)
    {
        return $this->model->where($column, $value)->first();
    }

    /**
     * @param string $column
     * @param mixed $value
     * @return Model
     */
    public function where(string $column, $value)
    {
        return $this->model->where($column, $value);
    }

    /**
     * @param string $column
     * @param string $direction
     * @return Model
     */
    public function orderBy(string $column, string $direction = 'asc')
    {
        return $this->model->orderBy($column, $direction);
    }
}