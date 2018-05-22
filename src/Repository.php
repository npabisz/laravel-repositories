<?php namespace Yorki\Repositories;

use Yorki\Repositories\Contracts\RepositoryContract;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;
use \Illuminate\Database\Eloquent\Model;

abstract class Repository implements RepositoryContract
{
    /**
     * @var string
     */
    protected $modelClass;
    
    public function __construct($modelClass)
    {
        $this->modelClass = $modelClass;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return new $this->modelClass;
    }


    /**
     * @return Model
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
     * @return Model
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
     * @return Model|null
     */
    public function find($id)
    {
        return $this->getQuery()->find($id);
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->getQuery()->all();
    }

    /**
     * @return int
     */
    public function count()
    {
        return (int) $this->getQuery()->count();
    }
}
