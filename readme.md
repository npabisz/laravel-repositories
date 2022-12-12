# Model repositories

Repositories and more for Laravel

## Installation
```shell
composer require npabisz/laravel-repositories
```

### Creating repository
```shell
php artisan make:repository Example
```

```shell
php artisan make:repository Example --model-namespace=Some\\Namespace
```

### Creating model
`php artisan make:repository-model MyModel`

`php artisan make:repository-model MyModel --model-namespace=App\Models --attributes="user_id=integer,name=string"`

### Creating migration
`php artisan make:repository-migration MyModel`

`php artisan make:repository-migration MyModel --model-namespace=App\Models"`

### Creating API controller
`php artisan make:repository-api MyModel`

`php artisan make:repository-api MyModel --model-namespace=App\Models" --api-namespace="App\Http\Controllers\Api" --api-repository-contract="App\Repositories\Contracts\MyModelRepositoryContract"`

### Namespace

Case scenario for model `App\Models\User\Image`, provide namespace excluding `App\Models`

`php artisan make:repository --namespace=User`