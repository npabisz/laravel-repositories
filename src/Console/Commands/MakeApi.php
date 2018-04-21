<?php namespace Yorki\Repositories\Console\Commands;

use \Illuminate\Console\Command;
use \Illuminate\Database\Eloquent\Model;
use \Illuminate\Filesystem\Filesystem;
use \Illuminate\Support\Str;

class MakeApi extends Command
{
    const MODEL_NAMESPACE = 'App\Models';
    const API_NAMESPACE = 'App\Http\Controllers\Api';

    /**
     * @var string
     */
    protected $signature = 'make:repository-api {model} {--model-namespace=} {--api-namespace=} {--api-repository-contract=}';

    /**
     * @var string
     */
    protected $description = 'Create new api controller for model class';

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
        return $this->files->get(__DIR__ . '/Stubs/Api.stub');
    }

    /**
     * @return string
     */
    protected function getRepositoryStub()
    {
        return $this->files->get(__DIR__ . '/Stubs/Api.repository.stub');
    }

    /**
     * @return bool
     */
    protected function useRepository()
    {
        return $this->option('api-repository-contract') !== null;
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
        $model = $this->getModelNamespace() . '\\' . $this->getModel();

        return new $model;
    }

    /**
     * @return string
     */
    protected function getNamespace()
    {
        if ($this->option('api-namespace')) {
            return $this->option('api-namespace');
        }

        return self::API_NAMESPACE;
    }

    /**
     * @return string
     */
    protected function getRepositoryNamespace()
    {
        return $this->option('api-repository-contract');
    }

    /**
     * @return string
     */
    protected function getRepositoryContract()
    {
        $namespace = explode('\\', $this->getRepositoryNamespace());

        return end($namespace);
    }

    /**
     * @return string
     */
    protected function getRepository()
    {
        $contract = $this->getRepositoryContract();
        $contractPos = strpos($contract, 'Contract');

        if ($contractPos !== false) {
            return lcfirst(substr($contract, 0, $contractPos));
        }

        return lcfirst($contract);
    }

    /**
     * @return string
     */
    protected function getModelNamespace()
    {
        if ($this->option('model-namespace')) {
            return $this->option('model-namespace');
        }

        return self::MODEL_NAMESPACE;
    }

    /**
     * @return string
     */
    protected function getClass()
    {
        return $this->getModel() . 'Controller';
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
    protected function getFilename()
    {
        return $this->getClass() . '.php';
    }

    /**
     * @return string
     */
    protected function getModelValidateArray($optional = false)
    {
        $result = [];
        $attributes = $this->getModelInstance()->getFillable();
        $casts = $this->getModelInstance()->getCasts();

        if (empty($attributes)) {
            return '//';
        }

        $required = $optional
            ? 'optional'
            : 'required';

        foreach ($attributes as $attribute) {
            $type = '';

            if (isset($casts[$attribute])) {
                switch ($casts[$attribute]) {
                    case 'int':
                    case 'integer':
                        $type = 'integer';
                        break;
                    case 'float':
                        $type = 'numeric';
                        break;
                    default:
                        $type = 'string';
                        break;
                }

                $type = '|' . $type;
            }

            $result[] = '\'' . $attribute . '\' => \'' . $required . $type . '\'';
        }

        return implode(',' . PHP_EOL . '            ', $result) . ',';
    }

    /**
     * @return string
     */
    protected function getModelCreateArray()
    {
        $result = [];
        $attributes = $this->getModelInstance()->getFillable();

        if (empty($attributes)) {
            return '//';
        }

        foreach ($attributes as $attribute) {
            $result[] = '\'' . $attribute . '\' => $data[\'' . $attribute . '\']';
        }

        return implode(',' . PHP_EOL . '            ', $result) . ',';
    }

    /**
     * @return string
     */
    protected function getPopulatedStub()
    {
        return str_replace([
            'DummyNamespace',
            'DummyClass',
            'DummyModelNamespace',
            'DummyModelClass',
            'DummyModelValidateArray',
            'DummyModelValidateOptionalArray',
            'DummyModelCreateArray',
        ], [
            $this->getNamespace(),
            $this->getClass(),
            $this->getModelNamespace() . '\\' . $this->getModel(),
            $this->getModel(),
            $this->getModelValidateArray(),
            $this->getModelValidateArray(true),
            $this->getModelCreateArray()
        ], $this->getStub());
    }

    /**
     * @return string
     */
    protected function getPopulatedRepositoryStub()
    {
        return str_replace([
            'DummyNamespace',
            'DummyClass',
            'DummyModelNamespace',
            'DummyModelClass',
            'DummyModelValidateArray',
            'DummyModelValidateOptionalArray',
            'DummyModelCreateArray',
            'DummyRepositoryNamespace',
            'DummyRepositoryContract',
            'DummyRepository'
        ], [
            $this->getNamespace(),
            $this->getClass(),
            $this->getModelNamespace() . '\\' . $this->getModel(),
            $this->getModel(),
            $this->getModelValidateArray(),
            $this->getModelValidateArray(true),
            $this->getModelCreateArray(),
            $this->getRepositoryNamespace(),
            $this->getRepositoryContract(),
            $this->getRepository(),
        ], $this->getRepositoryStub());
    }

    public function handle()
    {
        if (!$this->getModel()) {
            $this->output->error('Provide model class');

            return;
        }

        if (!class_exists($this->getModelNamespace() . '\\' . $this->getModel())) {
            $this->output->error('Model ' . $this->getModel() . ' not exists');

            return;
        }

        if (!file_exists($this->getDirectory())) {
            $this->files->makeDirectory($this->getDirectory(), 0755, true);
        }

        $this->files->put(
            $this->getDirectory() . '/' . $this->getFilename(),
            $this->useRepository()
                ? $this->getPopulatedRepositoryStub()
                : $this->getPopulatedStub()
        );

        $this->output->success('Api controller for ' . $this->getModel() . ' has been created');
    }
}