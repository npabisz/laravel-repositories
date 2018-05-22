<?php namespace Yorki\Repositories\Contracts;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;

interface RepositoryContract
{
    /**
     * @return Model
     */
    public function getModel();

    /**
     * @return Model
     */
    public function makeVisible($model);

    /**
     * @param array $data
     *
     * @return Model
     */
    public function create(array $data);

    /**
     * @param $id
     * @param array $data
     */
    public function update($id, array $data);

    /**
     * @param $id
     */
    public function delete($id);

    /**
     * @param int|string $id
     *
     * @return Model|null
     */
    public function find($id);

    /**
     * @return Builder
     */
    public function getQuery();

    /**
     * @return Collection
     */
    public function all();

    /**
     * @return int
     */
    public function count();
}
