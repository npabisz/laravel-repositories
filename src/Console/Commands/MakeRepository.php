<?php namespace Yorki\Repositories\Console\Commands;

use \Illuminate\Console\Command;
use \Illuminate\Filesystem\Filesystem;

class MakeRepository extends Command
{
    const RELATIVE_MODEL_NAMESPACE = 'App\Models';
    const REPOSITORIES_NAMESPACE = 'App\Repositories';
    const REPOSITORIES_CONTRACTS_NAMESPACE = 'App\Repositories\Contracts';
    const REPOSITORY_PARENT_CLASS = 'Repository';
    const REPOSITORY_PARENT_INTERFACE = 'RepositoryContract';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository {model}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Makes new repository class';

    /**
     * Create a new migration creator instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * @param bool $isClass
     *
     * @return string
     */
    protected function getStub($isClass)
    {
        $type = $isClass
            ? 'class'
            : 'interface';

        return $this->files->get(__DIR__ . '/Stubs/Repository.' . $type . '.stub');
    }

    /**
     * @param bool $isClass
     *
     * @return string
     */
    protected function getNamespacedStub($isClass)
    {
        $type = $isClass
            ? 'class'
            : 'interface';

        return $this->files->get(__DIR__ . '/Stubs/Repository.namespaced.' . $type . '.stub');
    }

    /**
     * @return bool
     */
    protected function useNamespacedRepository()
    {
        return count(explode('\\', $this->getModel())) > 1;
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
    protected function getModelClass()
    {
        $model = explode('\\', $this->getModel());

        return end($model);
    }

    /**
     * @return string
     */
    protected function getModelNamespace($full = true, $includeClass = true)
    {
        $namespace = explode('\\', $this->getModel());

        if (!$includeClass) {
            $namespace = array_slice($namespace, 0, count($namespace) - 1);
        }

        $namespace = implode('\\', $namespace);

        if ($full) {
            return self::RELATIVE_MODEL_NAMESPACE . ($namespace ? '\\' : '') . $namespace;
        }

        return $namespace;
    }

    /**
     * @return string
     */
    protected function getRepositoryClass()
    {
        return $this->getModelClass() . 'Repository';
    }

    /**
     * @return string
     */
    protected function getRepositoryInterface()
    {
        return $this->getModelClass() . 'RepositoryContract';
    }

    /**
     * @param bool $full
     * @param bool $includeClass
     *
     * @return string
     */
    protected function getRepositoryClassNamespace($full = true, $includeClass = true)
    {
        $namespace = explode('\\', $this->getModelNamespace(false));
        $namespace = array_slice($namespace, 0, count($namespace) - 1);
        $namespace = implode('\\', $namespace);

        if ($includeClass) {
            $namespace .= $namespace ? '\\' : '';
            $namespace .= $this->getRepositoryClass();
        }

        if ($full) {
            return self::REPOSITORIES_NAMESPACE . ($namespace ? '\\' : '') . $namespace;
        }

        return $namespace;
    }

    /**
     * @param bool $full
     * @param bool $includeClass
     *
     * @return string
     */
    protected function getRepositoryInterfaceNamespace($full = true, $includeClass = true)
    {
        $namespace = explode('\\', $this->getModelNamespace(false));
        $namespace = array_slice($namespace, 0, count($namespace) - 1);
        $namespace = implode('\\', $namespace);

        if ($includeClass) {
            $namespace .= $namespace ? '\\' : '';
            $namespace .= $this->getRepositoryInterface();
        }

        if ($full) {
            return self::REPOSITORIES_CONTRACTS_NAMESPACE . ($namespace ? '\\' : '') . $namespace;
        }

        return $namespace;
    }

    /**
     * @return string
     */
    protected function getPopulatedNamespacedClassStub()
    {
        return str_replace([
            'DummyNamespace',
            'DummyModelNamespace',
            'DummyModelClass',
            'DummyClass',
            'DummyRepositoryParentClass',
            'DummyInterfaceNamespace',
            'DummyInterface',
            'DummyRepositoryParentNamespace',
        ], [
            $this->getRepositoryClassNamespace(true, false),
            $this->getModelNamespace(),
            $this->getModelClass(),
            $this->getRepositoryClass(),
            self::REPOSITORY_PARENT_CLASS,
            $this->getRepositoryInterfaceNamespace(),
            $this->getRepositoryInterface(),
            self::REPOSITORIES_NAMESPACE . '\\' . self::REPOSITORY_PARENT_CLASS,
        ], $this->getNamespacedStub(true));
    }

    /**
     * @return string
     */
    protected function getPopulatedNamespacedInterfaceStub()
    {
        return str_replace([
            'DummyNamespace',
            'DummyRepositoryParentInterface',
            'DummyInterface',
            'DummyRepositoryParentNamespace',
        ], [
            $this->getRepositoryInterfaceNamespace(true, false),
            self::REPOSITORY_PARENT_INTERFACE,
            $this->getRepositoryInterface(),
            self::REPOSITORIES_CONTRACTS_NAMESPACE . '\\' . self::REPOSITORY_PARENT_INTERFACE,
        ], $this->getNamespacedStub(false));
    }

    /**
     * @return string
     */
    protected function getPopulatedClassStub()
    {
        return str_replace([
            'DummyNamespace',
            'DummyModelNamespace',
            'DummyModelClass',
            'DummyClass',
            'DummyRepositoryParentClass',
            'DummyInterfaceNamespace',
            'DummyInterface',
        ], [
            $this->getRepositoryClassNamespace(true, false),
            $this->getModelNamespace(),
            $this->getModelClass(),
            $this->getRepositoryClass(),
            self::REPOSITORY_PARENT_CLASS,
            $this->getRepositoryInterfaceNamespace(),
            $this->getRepositoryInterface(),
        ], $this->getStub(true));
    }

    /**
     * @return string
     */
    protected function getPopulatedInterfaceStub()
    {
        return str_replace([
            'DummyNamespace',
            'DummyRepositoryParentInterface',
            'DummyInterface',
        ], [
            $this->getRepositoryInterfaceNamespace(true, false),
            self::REPOSITORY_PARENT_INTERFACE,
            $this->getRepositoryInterface(),
        ], $this->getStub(false));
    }

    /**
     * @return string
     */
    protected function getRepositoryClassDirectory()
    {
        return dirname(base_path(lcfirst(str_replace('\\', '/', $this->getRepositoryClassNamespace()))));
    }

    /**
     * @return string
     */
    protected function getRepositoryInterfaceDirectory()
    {
        return dirname(base_path(lcfirst(str_replace('\\', '/', $this->getRepositoryInterfaceNamespace()))));
    }

    public function handle()
    {
        if (!$this->getModelClass()) {
            $this->output->error('Provide model class');

            return;
        }

        if (class_exists($this->getRepositoryClassNamespace())) {
            $this->output->error('Repository for ' . $this->getModelClass() . ' already exists');

            return;
        }

        if (!file_exists($this->getRepositoryClassDirectory())) {
            $this->files->makeDirectory($this->getRepositoryClassDirectory(), 0755, true);
        }

        $this->files->put(
            $this->getRepositoryClassDirectory() . '/' . $this->getRepositoryClass() . '.php',
            $this->useNamespacedRepository()
                ? $this->getPopulatedNamespacedClassStub()
                : $this->getPopulatedClassStub()
        );

        if (!file_exists($this->getRepositoryInterfaceDirectory())) {
            $this->files->makeDirectory($this->getRepositoryInterfaceDirectory(), 0755, true);
        }

        $this->files->put(
            $this->getRepositoryInterfaceDirectory() . '/' . $this->getRepositoryInterface() . '.php',
            $this->useNamespacedRepository()
                ? $this->getPopulatedNamespacedInterfaceStub()
                : $this->getPopulatedInterfaceStub()
        );

        if ($this->confirm('Generate entry in bootstrap/app.php file?', false)) {
            $bind = '//' . $this->getModelClass() . ' repository resolver' . PHP_EOL;
            $bind .= '$app->bind(\\' . $this->getRepositoryInterfaceNamespace() . '::class, \\' . $this->getRepositoryClassNamespace() . '::class);';
            $appPhp = $this->files->get(base_path('bootstrap/app.php'));
            $appPhp = str_replace('return $app;', $bind . PHP_EOL . PHP_EOL . 'return $app;', $appPhp);

            $this->files->put(
                base_path('bootstrap/app.php'),
                $appPhp
            );
        }

        $this->output->success('Repository for ' . $this->getModelNamespace(false) . ' has been created');
    }
}
