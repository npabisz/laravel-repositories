<?php

namespace Npabisz\Repositories\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
trait HasCrud
{
    /**
     * @param array $data
     *
     * @return Model|TModel
     */
    public function create (array $data)
    {
        return $this->repository->create($data);
    }

    /**
     * @param int|string $id
     * @param array $data
     *
     * @return void
     */
    public function update (int|string $id, array $data): void
    {
        $this->repository->update($id, $data);
    }

    /**
     * @param int|string $id
     *
     * @return void
     */
    public function delete (int|string $id): void
    {
        $this->repository->delete($id);
    }

    /**
     * @param int|string $id
     *
     * @return Model|TModel|null
     */
    public function find (int|string $id)
    {
        return $this->repository->find($id);
    }

    /**
     * @return Collection<TModel>
     */
    public function all (): Collection
    {
        return $this->repository->all();
    }

    /**
     * @return int
     */
    public function count (): int
    {
        return $this->repository->count();
    }
}
