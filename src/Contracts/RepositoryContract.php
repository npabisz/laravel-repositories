<?php

namespace Npabisz\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Container\BindingResolutionException;

/**
 * @template TModel of Model
 */
interface RepositoryContract
{
    /**
     * @param class-string<Model<TModel>> $modelClass
     */
    public function __construct (string $modelClass);

    /**
     * @return Model|TModel
     */
    public function getModel ();

    /**
     * @return Model|TModel
     */
    public function makeVisible ($model);

    /**
     * @param array $data
     *
     * @return Model|TModel
     */
    public function create (array $data);

    /**
     * @param int|string $id
     * @param array $data
     *
     * @return void
     */
    public function update (int|string $id, array $data);

    /**
     * @param int|string $id
     *
     * @return void
     */
    public function delete (int|string $id);

    /**
     * @param int|string $id
     *
     * @return Model|TModel|null
     */
    public function find (int|string $id);

    /**
     * @return Builder|TModel
     */
    public function getQuery ();

    /**
     * @return Collection<TModel>
     */
    public function all (): Collection;

    /**
     * @return int
     */
    public function count (): int;

    /**
     * @param ?Builder $query
     * @param int $perPage
     * @param int $page
     * @param ?string $orderBy
     * @param ?string $orderDirection
     *
     * @return Collection
     */
    public function get (int $perPage = 10, int $page = 1, ?Builder $query = null, string $orderBy = null, ?string $orderDirection = 'DESC'): Collection;

    /**
     * @param int $perPage
     * @param ?int $currentPage
     * @param ?Builder $query
     * @param ?string $orderBy
     * @param ?string $orderDirection
     * @param string $pageName
     *
     * @throws BindingResolutionException
     *
     * @return LengthAwarePaginator
     */
    public function paginate (int $perPage = 10, int $currentPage = null, ?Builder $query = null, string $orderBy = null, ?string $orderDirection = 'DESC', string $pageName = 'page'): LengthAwarePaginator;

    /**
     * @param Collection|\Illuminate\Support\Collection $items
     * @param int $total
     * @param int $perPage
     * @param ?int $currentPage
     * @param string $pageName
     * @param array $columns
     *
     * @throws BindingResolutionException
     *
     * @return LengthAwarePaginator
     */
    public function makePaginator (Collection|\Illuminate\Support\Collection $items, int $total, int $perPage = 10, ?int $currentPage = null, string $pageName = 'page', array $columns = ['*']): LengthAwarePaginator;
}
