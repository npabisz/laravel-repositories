<?php

namespace Npabisz\Repositories\Console\Commands;

use \Illuminate\Console\Command;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Filesystem\Filesystem;
use \Illuminate\Support\Str;

class MakeMigration extends Command
{
    const MODEL_NAMESPACE = 'App\Models';

    /**
     * @var string
     */
    protected $signature = 'make:repository-migration {model} {--model-namespace=}';

    /**
     * @var string
     */
    protected $description = 'Create new migration based on model class';

    /**
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * @param \Illuminate\Filesystem\Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * @return string
     */
    protected function getStub()
    {
        return $this->files->get(__DIR__ . '/Stubs/Migration.stub');
    }

    /**
     * @return string
     */
    protected function getModel()
    {
        return $this->argument('model');
    }

    /**
     * @return Model
     */
    protected function getModelInstance()
    {
        $model = $this->getNamespace() . '\\' . $this->getModel();

        return new $model;
    }

    /**
     * @return string
     */
    protected function getTable()
    {
        return $this->getModelInstance()->getTable();
    }

    /**
     * @return string
     */
    protected function getNamespace()
    {
        if ($this->option('model-namespace')) {
            return $this->option('model-namespace');
        }

        return self::MODEL_NAMESPACE;
    }

    /**
     * @return string
     */
    protected function getDirectory()
    {
        return base_path('database/migrations');
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return 'Create' . $this->getModel() . 'Table';
    }

    /**
     * @return string
     */
    protected function getFilename()
    {
        return date('Y_m_d_His_') . Str::snake($this->getClass()) . '.php';
    }

    /**
     * @return string
     */
    protected function getColumns()
    {
        $result = [];

        $casts = $this->getModelInstance()->getCasts();
        $attributes = $this->getModelInstance()->getFillable();

        if (empty($attributes)) {
            return '//';
        }

        foreach ($attributes as $attribute) {
            $type = 'string';
            $arguments = '';

            if (isset($casts[$attribute])) {
                $type = $casts[$attribute];
            }

            switch ($type) {
                case 'int':
                case 'integer':
                    $method = 'integer';
                    break;
                case 'float':
                    $method = 'decimal';
                    $arguments = ', 16, 8';
                    break;
                case 'bool':
                case 'boolean':
                    $method = 'boolean';
                    break;
                default:
                    $method = 'string';
                    break;
            }

            $result[] = '$table->' . $method . '(\'' . $attribute . '\'' . $arguments . ');';
        }

        return implode(PHP_EOL . '            ', $result);
    }

    /**
     * @return string
     */
    protected function getPopulatedStub()
    {
        return str_replace([
            'DummyClass',
            'DummyTable',
            'DummyColumns',
        ], [
            $this->getClass(),
            $this->getTable(),
            $this->getColumns(),
        ], $this->getStub());
    }

    public function handle()
    {
        if (!$this->getModel()) {
            $this->output->error('Provide model class');

            return;
        }

        if (!class_exists($this->getNamespace() . '\\' . $this->getModel())) {
            $this->output->error('Model ' . $this->getModel() . ' not exists');

            return;
        }

        $this->files->put(
            $this->getDirectory() . '/' . $this->getFilename(),
            $this->getPopulatedStub()
        );

        $this->output->success('Migration for ' . $this->getModel() . ' has been created');
    }
}