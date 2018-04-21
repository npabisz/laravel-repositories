# Model repositories

Repositories and more for Laravel

## Installation
`composer require yorki/model-repository`

## Usage
### Creating model
`php artisan make:repository-model MyModel`

`php artisan make:repository-model MyModel --model-namespace=App\Models --attributes="user_id=integer,name=string"`

### Creating migration
`php artisan make:repository-migration MyModel`

`php artisan make:repository-migration MyModel --model-namespace=App\Models"`

### Creating repository
`php artisan make:repository MyModel`

`php artisan make:repository MyModel --model-namespace=App\Models"`

### Creating API controller
`php artisan make:repository-api MyModel`

`php artisan make:repository-api MyModel --model-namespace=App\Models" --api-namespace="App\Http\Controllers\Api" --api-repository-contract="App\Repositories\Contracts\MyModelRepositoryContract"`