<?php

namespace Npabisz\Repositories;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Npabisz\Repositories\Contracts\RepositoryContract;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Database\Eloquent\Model;

/**
 * @template TModel of Model
 */
abstract class Repository implements RepositoryContract
{
    /**
     * @var string
     */
    protected string $modelClass;

    /**
     * @param class-string<Model<TModel>> $modelClass
     */
    public function __construct (string $modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return Model|TModel
     */
    public function getModel ()
    {
        return new $this->modelClass;
    }


    /**
     * @return Model|TModel
     */
    public function makeVisible ($model)
    {
        return $model->makeVisible(
            $this->getModel()->getHidden()
        );
    }

    public function getQuery ()
    {
        return $this->getModel()->newModelQuery();
    }

    /**
     * @param array $data
     *
     * @return Model|TModel
     */
    public function create (array $data)
    {
        return $this->getQuery()->create($data);
    }

    /**
     * @param int|string $id
     * @param array $data
     *
     * @return void
     */
    public function update (int|string $id, array $data): void
    {
        $this->getQuery()
            ->where(($this->getModel())->getKeyName(), $id)
            ->first()
            ?->update($data);
    }

    /**
     * @param int|string $id
     *
     * @return void
     */
    public function delete (int|string $id): void
    {
        $this->getQuery()
            ->where(($this->getModel())->getKeyName(), $id)
            ->first()
            ?->delete();
    }

    /**
     * @param int|string $id
     *
     * @return Model|TModel|null
     */
    public function find (int|string $id)
    {
        return $this->getQuery()->find($id);
    }

    /**
     * @return Collection
     */
    public function all (): Collection
    {
        return $this->getQuery()->all();
    }

    /**
     * @return int
     */
    public function count (): int
    {
        return (int) $this->getQuery()->count();
    }

    /**
     * @param ?Builder $query
     * @param int $perPage
     * @param int $page
     * @param ?string $orderBy
     * @param ?string $orderDirection
     *
     * @return Collection
     */
    public function get (int $perPage = 10, int $page = 1, ?Builder $query = null, string $orderBy = null, ?string $orderDirection = 'DESC'): Collection
    {
        $query = $query ?: $this->getQuery();

        if ($perPage > 0) {
            $query->take($perPage)
                ->offset(($page - 1) * $perPage);
        }

        if (null === $orderBy) {
            $orderBy = $this->getModel()->getKeyName();
        }

        if ($orderDirection) {
            $query->orderBy($orderBy, $orderDirection);
        }

        return $query->get();
    }

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
    public function paginate (int $perPage = 10, int $currentPage = null, ?Builder $query = null, string $orderBy = null, ?string $orderDirection = 'DESC', string $pageName = 'page'): LengthAwarePaginator
    {
        $currentPage = $currentPage ?: Paginator::resolveCurrentPage($pageName);

        if ($query) {
            $count = DB::table(DB::raw("({$query->toSql()}) as sub"))
                ->mergeBindings($query->getQuery())
                ->count();
        } else {
            $count = $this->count();
        }

        $items = $this->get($perPage, $currentPage, $query, $orderBy, $orderDirection);

        return $this->makePaginator(
            $items,
            $count,
            $perPage,
            $currentPage,
            $pageName
        );
    }

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
    public function makePaginator (Collection|\Illuminate\Support\Collection $items, int $total, int $perPage = 10, ?int $currentPage = null, string $pageName = 'page', array $columns = ['*']): LengthAwarePaginator
    {
        $currentPage = $currentPage ?: Paginator::resolveCurrentPage($pageName);
        $options = [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => $pageName,
        ];

        return Container::getInstance()->makeWith(LengthAwarePaginator::class, compact(
            'items', 'total', 'perPage', 'currentPage', 'options'
        ));
    }
}
