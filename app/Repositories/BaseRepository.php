<?php

namespace App\Repositories;

class BaseRepository implements IBaseRepository
{
    protected $obj;

    protected function __construct(object $obj)
    {
        $this->obj = $obj;
    }

    public function all(): object
    {
        return $this->obj->all();
    }

    public function findOne(string $id): ?object
    {
        return $this->obj->find($id);
    }

    public function findOneWith(string $id, string $with): ?object
    {
        return $this->obj->with($with)->find($id);
    }

    public function save(array $attributes): object
    {
        $this->obj->fill($attributes);
        $this->obj->save();
        return $this->obj->fresh();
    }

    public function update(string $id, array $attributes): bool
    {
        return $this->obj->find($id)->update($attributes);
    }

    public function delete(string $id): bool
    {
        return $this->obj->find($id)->delete();
    }
}
