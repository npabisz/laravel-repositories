<?php namespace Yorki\Repositories\Contracts;

use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;

interface RepositoryContract
{
    /**
     * @return ModelContract
     */
    public function getModel();

    /**
     * @return ModelContract
     */
    public function makeVisible($model);

    /**
     * @param array $data
     *
     * @return ModelContract
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
     * @return ModelContract|null
     */
    public function find($id);

    /**
     * @param array $where
     * @param int $perPage
     * @param int $page
     * @param string $orderBy
     * @param string $orderDirection
     *
     * @return Collection
     */
    public function get(array $where, $perPage = 20, $page = 1, $orderBy = null, $orderDirection = 'DESC');

    /**
     * @param array $where
     * @param string $orderBy
     * @param string $orderDirection
     *
     * @return ModelContract|null
     */
    public function getFirst(array $where, $orderBy = null, $orderDirection = 'DESC');

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
