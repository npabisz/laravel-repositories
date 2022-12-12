<?php

namespace Npabisz\Repositories\Console\Commands;

use \Illuminate\Console\Command;
use \Illuminate\Filesystem\Filesystem;
use \Illuminate\Support\Str;

class MakeModel extends Command
{
    const MODEL_NAMESPACE = 'App\Models';

    /**
     * @var string
     */
    protected $signature = 'make:repository-model {model} {--model-namespace=} {--attributes=}';

    /**
     * @var string
     */
    protected $description = 'Create new model class';

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
        return $this->files->get(__DIR__ . '/Stubs/Model.stub');
    }

    /**
     * @return string
     */
    protected function getModel()
    {
        return $this->argument('model');
    }

    /**
     * @return string
     */
    protected function getTable()
    {
        return Str::snake(class_basename($this->getModel()));
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
        return base_path(lcfirst(str_replace('\\', '/', $this->getNamespace())));
    }

    /**
     * @return string
     */
    protected function getFillable()
    {
        $result = [];
        $attributes = explode(',', (string) $this->option('attributes'));

        if (empty($attributes)) {
            return '//';
        }

        foreach ($attributes as $attribute) {
            $attr = explode('=', $attribute);
            $result[] = '\'' . $attr[0] . '\'';
        }

        return implode(',' . PHP_EOL . '        ', $result) . ',';
    }

    /**
     * @return string
     */
    protected function getCasts()
    {
        $result = [];
        $attributes = explode(',', (string) $this->option('attributes'));

        if (empty($attributes)) {
            return '//';
        }

        foreach ($attributes as $attribute) {
            $attr = explode('=', $attribute);

            if (isset($attr[1])) {
                $result[] = '\'' . $attr[0] . '\' => \'' . $attr[1] . '\'';
            }
        }

        if (empty($result)) {
            return '//';
        }

        return implode(',' . PHP_EOL . '        ', $result) . ',';
    }

    /**
     * @return string
     */
    protected function getPopulatedStub()
    {
        return str_replace([
            'DummyNamespace',
            'DummyClass',
            'DummyTable',
            'DummyFillable',
            'DummyCasts',
        ], [
            $this->getNamespace(),
            $this->getModel(),
            $this->getTable(),
            $this->getFillable(),
            $this->getCasts(),
        ], $this->getStub());
    }

    public function handle()
    {
        if (!$this->getModel()) {
            $this->output->error('Provide model class');

            return;
        }

        if (file_exists($this->getDirectory() . '/' . $this->getModel() . '.php')) {
            $this->output->error('Model ' . $this->getModel() . ' already exists');

            return;
        }

        if (!file_exists($this->getDirectory())) {
            $this->files->makeDirectory($this->getDirectory(), 0755, true);
        }

        $this->files->put(
            $this->getDirectory() . '/' . $this->getModel() . '.php',
            $this->getPopulatedStub()
        );

        $this->output->success('Model ' . $this->getModel() . ' has been created');
    }
}