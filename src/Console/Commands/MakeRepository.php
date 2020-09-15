<?php namespace Yorki\Repositories\Console\Commands;

use \Illuminate\Console\Command;
use \Illuminate\Filesystem\Filesystem;
use Yorki\Repositories\Contracts\ModelContract;

class MakeRepository extends Command
{
    const RELATIVE_MODEL_NAMESPACE = 'App\Models';
    const REPOSITORIES_NAMESPACE = 'App\Repositories';
    const REPOSITORIES_CONTRACTS_NAMESPACE = 'App\Repositories\Contracts';
    const REPOSITORY_PARENT_CLASS = 'Repository';
    const REPOSITORY_PARENT_CLASS_NAMESPACE = 'Yorki\Repositories';
    const REPOSITORY_PARENT_INTERFACE = 'RepositoryContract';
    const REPOSITORY_PARENT_INTERFACE_NAMESPACE = 'Yorki\Repositories\Contracts';

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
    protected $signature = 'make:repository {model} {--model-namespace=} {--namespace=}';

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

        if ($this->option('namespace')) {
            $namespace = explode('\\', $this->option('namespace') . "\\" . $this->getModel());
        }

        if (!$includeClass) {
            $namespace = array_slice($namespace, 0, count($namespace) - 1);
        }

        $namespace = implode('\\', $namespace);

        if ($full) {
            $modelNamespace = self::RELATIVE_MODEL_NAMESPACE;

            if ($this->option('model-namespace')) {
                $modelNamespace = $this->option('model-namespace');
            }

            if ($this->option('namespace')) {
                $modelNamespace = "App\\Models";
                $namespace = str_replace("App\\Models\\", '', $namespace);//$this->option('namespace');
            }

            return $modelNamespace . ($namespace ? '\\' : '') . $namespace;
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

        if ($this->option('namespace')) {
            $namespace = $this->option('namespace');
        }

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

        if ($this->option('namespace')) {
            $namespace = $this->option('namespace');
        }

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
            'DummyClass',
            'DummyRepositoryParentClass',
            'DummyInterfaceNamespace',
            'DummyInterface',
            'DummyRepositoryParentNamespace',
        ], [
            $this->getRepositoryClassNamespace(true, false),
            $this->getRepositoryClass(),
            self::REPOSITORY_PARENT_CLASS,
            $this->getRepositoryInterfaceNamespace(),
            $this->getRepositoryInterface(),
            self::REPOSITORY_PARENT_CLASS_NAMESPACE . '\\' . self::REPOSITORY_PARENT_CLASS,
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
            self::REPOSITORY_PARENT_INTERFACE_NAMESPACE . '\\' . self::REPOSITORY_PARENT_INTERFACE,
        ], $this->getNamespacedStub(false));
    }

    /**
     * @return string
     */
    protected function getPopulatedClassStub()
    {
        return str_replace([
            'DummyNamespace',
            'DummyClass',
            'DummyRepositoryParentClass',
            'DummyInterfaceNamespace',
            'DummyInterface',
        ], [
            $this->getRepositoryClassNamespace(true, false),
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
    protected function getPopulatedServiceStub()
    {
        $namespace = $this->getModelNamespace(false, false);

        return str_replace([
            'DummyNamespace',
            'DummyRepositoryInterfaceNamespace',
            'DummyRepositoryInterface',
            'DummyService',
        ], [
            "App\\Services" . ($namespace ? "\\" . $namespace : ''),
            $this->getRepositoryInterfaceNamespace(),
            $this->getRepositoryInterface(),
            $this->getModelClass() . 'Service',
        ], $this->files->get(__DIR__ . '/Stubs/RepositoryService.class.stub'));
    }

    /**
     * @return string
     */
    protected function getPopulatedFacadeStub()
    {
        return str_replace([
            'DummyFacade',
            'DummyService',
        ], [
            $this->getModelClass() . 'Facade',
            $this->getModelClass() . 'Service',
        ], $this->files->get(__DIR__ . '/Stubs/RepositoryFacade.class.stub'));
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

        if (!class_exists($this->getModelNamespace())) {
            $this->output->error('Model ' . $this->getModelNamespace() . ' does not exists');

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

        if (!file_exists(base_path('app/Services'))){
            $this->files->makeDirectory(base_path('app/Services'), 0755, true);
        }

        $this->files->put(
            base_path('app/Services/' . implode('/', explode('\\', $this->getModelNamespace(false))) . 'Service.php'),
            $this->getPopulatedServiceStub()
        );

        if ($this->confirm('Generate entry in service provider?', false)) {
            if (!file_exists(base_path('app/Providers/RepositoryServiceProvider.php'))) {
                if (!file_exists(base_path('app/Providers'))){
                    $this->files->makeDirectory(base_path('app/Providers'), 0755, true);
                }

                $this->files->put(base_path('app/Providers/RepositoryServiceProvider.php'), $this->files->get(__DIR__ . '/Stubs/RepositoryServiceProvider.class.stub'));
            }

            $serviceProvider = $this->files->get(base_path('app/Providers/RepositoryServiceProvider.php'));

            if (!strpos($serviceProvider, $this->getRepositoryInterfaceNamespace())) {
                $methodBegins = strpos($serviceProvider, 'public function register');

                if ($methodBegins === false) {
                    $this->output->error('Failed to add entry in repository service!');

                    return;
                }

                $resolver = "\t\t# Auto generated repository for \"" . $this->getModelClass() . "\"" . PHP_EOL;
                $resolver .= "\t\t\$this->app->bind(\\" . $this->getRepositoryInterfaceNamespace() . "::class, function (\$app) {" . PHP_EOL;
                $resolver .= "\t\t\treturn new \\" . $this->getRepositoryClassNamespace() . "(\\" . $this->getModelNamespace() . "::class);" . PHP_EOL;
                $resolver .= "\t\t});" . PHP_EOL . PHP_EOL;

                $serviceNamespace = $this->getModelNamespace(false, false);

                $resolver .= "\t\t# Auto generated service for \"" . $this->getModelClass() . "\"" . PHP_EOL;
                $resolver .= "\t\t\$this->app->bind(\App\Services\\" . ($serviceNamespace ? $serviceNamespace . "\\" . $this->getModelClass() : '') . "Service::class, function (\$app) {" . PHP_EOL;
                $resolver .= "\t\t\treturn new \App\Services\\" . ($serviceNamespace ? $serviceNamespace . "\\" . $this->getModelClass() : '') . "Service(\$app->make('" . $this->getRepositoryInterfaceNamespace() . "'));" . PHP_EOL;
                $resolver .= "\t\t});" . PHP_EOL;

                $serviceProviderTmp = substr($serviceProvider, 0, $methodBegins);
                $serviceProviderRest = substr($serviceProvider, $methodBegins);
                $methodBody = strpos($serviceProviderRest, '{') + 1;
                $serviceProviderTmp .= substr($serviceProviderRest, 0, $methodBody) . PHP_EOL . $resolver . substr($serviceProviderRest, $methodBody);

                $this->files->put(
                    base_path('app/Providers/RepositoryServiceProvider.php'),
                    $serviceProviderTmp
                );
            } else {
                $this->output->notice('Looks like this repository is already added in service provider');
            }
        }

        $this->output->success('Repository for ' . $this->getModelNamespace(false) . ' has been created');
        $this->output->success('Remember to add App\Providers\RepositoryServiceProvider::class to providers array in your config/app.php file');
    }
}
