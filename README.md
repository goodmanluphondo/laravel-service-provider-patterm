# Laravel Service-Repository Pattern

[![Latest Stable Version](https://poser.pugx.org/goodmanluphondo/laravel-service-repository-pattern/v/stable)](https://packagist.org/packages/goodmanluphondo/laravel-service-repository-pattern)
[![Total Downloads](https://poser.pugx.org/goodmanluphondo/laravel-service-repository-pattern/downloads)](https://packagist.org/packages/goodmanluphondo/laravel-service-repository-pattern)
[![License](https://poser.pugx.org/goodmanluphondo/laravel-service-repository-pattern/license)](https://packagist.org/packages/goodmanluphondo/laravel-service-repository-pattern)

A Laravel package that helps you implement the Service-Repository pattern in your Laravel applications. This package provides base classes and Artisan commands to quickly scaffold repositories, interfaces, and services following clean architecture principles.

> **Note:** This package enforces separation of concerns between data access and business logic. Use it to maintain clean, testable, and maintainable code architecture.

This package includes the following features:

- **Scaffolding Commands**: Generate services, repositories, and interfaces with a single command
- **Base Classes**: Pre-built base repository and service classes with common CRUD operations
- **Clean Architecture**: Enforces separation of concerns between data access and business logic
- **Namespace Support**: Supports sub-namespaces for better organization
- **Automatic Binding**: Automatically registers repository interfaces with their implementations

## Installation

Require this package with composer:

```bash
composer require goodmanluphondo/laravel-service-repository-pattern
```

Laravel uses Package Auto-Discovery, so doesn't require you to manually add the ServiceProvider.

### Laravel without auto-discovery:

If you don't use auto-discovery, add the ServiceProvider to the providers list. For Laravel 11 or newer, add the ServiceProvider in `bootstrap/providers.php`. For Laravel 10 or older, add the ServiceProvider in `config/app.php`.

```php
// config/app.php (Laravel 10 and older)
'providers' => [
    // Other providers...
    GoodmanLuphondo\LaravelServiceRepositoryPattern\Providers\ServiceRepositoryPatternServiceProvider::class,
],

// bootstrap/providers.php (Laravel 11+)
<?php

return [
    App\Providers\AppServiceProvider::class,
    GoodmanLuphondo\LaravelServiceRepositoryPattern\Providers\ServiceRepositoryPatternServiceProvider::class,
];
```

### Publish the base files

After installation, publish the base files to your application:

```bash
php artisan vendor:publish --tag=service-repository-pattern
```

This will publish:

- `app/Interfaces/BaseInterface.php` - Base interface for all repositories
- `app/Repositories/Repository.php` - Base repository implementation
- `app/Providers/RepositoryServiceProvider.php` - Service provider for binding interfaces

### Register the Repository Service Provider

After publishing, add the `RepositoryServiceProvider` (this is the published file that binds your repository interfaces) to your `config/app.php` (for Laravel 10 and older) or `bootstrap/providers.php` (for Laravel 11+):

```php
// config/app.php (Laravel 10 and older)
'providers' => [
    // Other providers...
    App\Providers\RepositoryServiceProvider::class,
],

// bootstrap/providers.php (Laravel 11+)
<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,
];
```

## Usage

### Creating a Simple Service

Use the `make:service` command to create a new service class:

```bash
php artisan make:service UserService
```

This creates `app/Services/UserService.php`.

### Creating a Service with Repository Pattern

For services tied to a model, use the `-R` (or `--repository`) flag to scaffold the complete repository structure:

```bash
php artisan make:service Post -R
```

This command will:

1. Create `app/Interfaces/PostRepositoryInterface.php` (or `app/Interfaces/Posts/PostRepositoryInterface.php` for sub-namespaced models)
2. Create `app/Repositories/PostRepository.php` (or `app/Repositories/Posts/PostRepository.php` for sub-namespaced models)
3. Create `app/Services/PostService.php` (or `app/Services/Posts/PostService.php` for sub-namespaced models)
4. Automatically bind the interface to the repository in `RepositoryServiceProvider`

> **Important:** The model must exist before running this command. The command supports both root models (`app/Models/User.php`) and sub-namespaced models (`app/Models/Blog/Post.php`).

### Using Sub-namespaces

For services not tied to a model that need sub-namespace organization:

```bash
php artisan make:service Integrations\\PaymentService
```

This creates `app/Services/Integrations/PaymentServiceService.php`.

## Generated Structure

When using the `-R` flag, the generated files follow this structure:

### Repository Interface

```php
<?php

namespace App\Interfaces;

use App\Interfaces\BaseInterface;

interface PostRepositoryInterface extends BaseInterface
{
    //
}
```

### Repository Implementation

```php
<?php

namespace App\Repositories;

use App\Interfaces\PostRepositoryInterface;
use App\Models\Post;
use App\Repositories\Repository;

class PostRepository extends Repository implements PostRepositoryInterface
{
    public function __construct(Post $post)
    {
        parent::__construct($post);
    }
}
```

### Service

```php
<?php

namespace App\Services;

use App\Interfaces\PostRepositoryInterface;

class PostService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository,
    ) {}
}
```

## Available Repository Methods

The base repository provides these methods out of the box:

| Method                                               | Description                                        |
| ---------------------------------------------------- | -------------------------------------------------- |
| `query()`                                            | Get a query builder instance                       |
| `find($id)`                                          | Find a model by ID (throws exception if not found) |
| `findAll()`                                          | Get all models                                     |
| `create(array $data)`                                | Create a new model                                 |
| `update($id, array $data)`                           | Update a model by ID                               |
| `delete($id)`                                        | Delete a model by ID                               |
| `firstOrCreate(array $data)`                         | Get first matching model or create new one         |
| `firstWhere(string $column, $value)`                 | Get first model matching condition                 |
| `where(string $column, $value)`                      | Add where condition to query                       |
| `orderBy(string $column, string $direction = 'asc')` | Add order by to query                              |

## Example Usage in Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\PostService;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function __construct(
        protected PostService $postService
    ) {}

    public function index()
    {
        $posts = $this->postService->getAllPosts();
        return view('posts.index', compact('posts'));
    }

    public function store(Request $request)
    {
        $post = $this->postService->createPost($request->validated());
        return redirect()->route('posts.show', $post);
    }
}
```

## Requirements

- PHP 8.1 or higher
- Laravel 11.0 or higher

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.
