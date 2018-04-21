<?php namespace Yorki\Repositories;

use Yorki\Repositories\Contracts\ModelContract;
use Yorki\Repositories\Contracts\RepositoryContract;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Database\Eloquent\Model;

abstract class Repository implements RepositoryContract
{
    /**
     * @return ModelContract|Model
     */
    abstract public function getModel();

    /**
     * @return ModelContract
     */
    public function makeVisible($model)
    {
        return $model->makeVisible(
            $this->getModel()->getHidden()
        );
    }

    /**
     * @return Builder
     */
    public function getQuery()
    {
        return $this->getModel()->newQuery();
    }

    /**
     * @param array $data
     *
     * @return ModelContract|Model
     */
    public function create(array $data)
    {
        return $this->getQuery()->create($data);
    }

    /**
     * @param $id
     * @param array $data
     */
    public function update($id, array $data)
    {
        $this->getQuery()
            ->where(($this->getModel())->getKeyName(), $id)
            ->update($data);
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->getQuery()
            ->where(($this->getModel())->getKeyName(), $id)
            ->delete();
    }

    /**
     * @param int|string $id
     *
     * @return ModelContract|Model|null
     */
    public function find($id)
    {
        return $this->getQuery()->find($id);
    }

    /**
     * @param array $where
     * @param int $perPage
     * @param int $page
     * @param string $orderBy
     * @param string $orderDirection
     *
     * @return Collection
     */
    public function get(array $where, $perPage = 20, $page = 1, $orderBy = null, $orderDirection = 'DESC')
    {
        $query = $this->getQuery();

        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }

        if ($perPage > 0) {
            $query->take($perPage)
                ->offset(($page - 1) * $perPage);
        }

        if (null === $orderBy) {
            $orderBy = $this->getModel()->getKeyName();
        }

        $query->orderBy($orderBy, $orderDirection);

        return $query->get();
    }

    /**
     * @param array $where
     * @param string $orderBy
     * @param string $orderDirection
     *
     * @return ModelContract|Model|null
     */
    public function getFirst(array $where, $orderBy = null, $orderDirection = 'DESC')
    {
        $query = $this->getQuery();

        foreach ($where as $key => $value) {
            $query->where($key, $value);
        }

        if (null === $orderBy) {
            $orderBy = $this->getModel()->getKeyName();
        }

        $query->orderBy($orderBy, $orderDirection);

        return $query->first();
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return ($this->getModel())::all();
    }

    /**
     * @return int
     */
    public function count()
    {
        return (int) $this->getQuery()->count();
    }
}
