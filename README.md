# Laravel Service-Repository Pattern

A Laravel package that helps you implement the Service-Repository pattern in your Laravel applications. This package provides base classes and Artisan commands to quickly scaffold repositories, interfaces, and services following clean architecture principles.

## Features

- 🏗️ **Scaffolding Commands**: Generate services, repositories, and interfaces with a single command
- 🔧 **Base Classes**: Pre-built base repository and service classes with common CRUD operations
- 🎯 **Clean Architecture**: Enforces separation of concerns between data access and business logic
- 📁 **Namespace Support**: Supports sub-namespaces for better organization
- 🔗 **Automatic Binding**: Automatically registers repository interfaces with their implementations

## Installation

Install the package via Composer:

```bash
composer require goodmanluphondo/laravel-service-repository-pattern
```

## Setup

After installation, publish the base files to your application:

```bash
php artisan vendor:publish --tag=service-repository
```

This will publish:

- `app/Interfaces/BaseInterface.php` - Base interface for all repositories
- `app/Repositories/Repository.php` - Base repository implementation
- `app/Providers/RepositoryServiceProvider.php` - Service provider for binding interfaces

Register the `RepositoryServiceProvider` in your `config/app.php`:

```php
'providers' => [
    // Other providers...
    App\Providers\RepositoryServiceProvider::class,
],
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

1. Create `app/Interfaces/Posts/PostRepositoryInterface.php`
2. Create `app/Repositories/Posts/PostRepository.php`
3. Create `app/Services/Posts/PostService.php`
4. Automatically bind the interface to the repository in `RepositoryServiceProvider`

**Note**: The model (`app/Models/Posts/Post.php`) must exist before running this command.

### Using Sub-namespaces

For services not tied to a model that need sub-namespace organization:

```bash
php artisan make:service Integrations\\Integration
```

This creates `app/Services/Integrations/IntegrationService.php`.

## Generated Structure

When using the `-R` flag, the generated files follow this structure:

### Repository Interface

```php
<?php

namespace App\Interfaces\Posts;

use App\Interfaces\BaseInterface;

interface PostRepositoryInterface extends BaseInterface
{
    //
}
```

### Repository Implementation

```php
<?php

namespace App\Repositories\Posts;

use App\Interfaces\Posts\PostRepositoryInterface;
use App\Models\Posts\Post;
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

namespace App\Services\Posts;

use App\Interfaces\Posts\PostRepositoryInterface;

class PostService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository,
    ) {}
}
```

## Available Repository Methods

The base repository provides these methods out of the box:

- `query()` - Get a query builder instance
- `find($id)` - Find a model by ID (throws exception if not found)
- `findAll()` - Get all models
- `create(array $data)` - Create a new model
- `update($id, array $data)` - Update a model by ID
- `delete($id)` - Delete a model by ID
- `firstOrCreate(array $data)` - Get first matching model or create new one
- `firstWhere(string $column, $value)` - Get first model matching condition
- `where(string $column, $value)` - Add where condition to query
- `orderBy(string $column, string $direction = 'asc')` - Add order by to query

## Example Usage in Controller

```php
<?php

namespace App\Http\Controllers;

use App\Services\Posts\PostService;
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
