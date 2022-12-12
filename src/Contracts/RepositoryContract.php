<?php

namespace Npabisz\Repositories\Contracts;

use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\Builder;
use \Illuminate\Database\Eloquent\Collection;

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
}
