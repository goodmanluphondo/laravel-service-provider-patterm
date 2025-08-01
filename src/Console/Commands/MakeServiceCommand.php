<?php

namespace GoodmanLuphondo\LaravelServiceRepositoryPattern\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service {name} {--R|repository}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service class. If the repository (--R|repository) flag is passed, the name provided must be of a valid model.';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
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
     * Execute the console command.
     */
    public function handle()
    {
        if (! $this->files->exists($this->laravel->basePath('app/Interfaces/BaseInterface.php'))) {
            $this->error('The BaseInterface does not exist. Please create app/Interfaces/BaseInterface.php');
            return 1;
        }

        if (! $this->files->exists($this->laravel->basePath('app/Repositories/Repository.php'))) {
            $this->error('The Base Repository does not exist. Please create app/Repositories/Repository.php');
            return 1;
        }

        if ($this->option('repository')) {
            $this->createWithRepository();
        } else {
            $this->createPlainService();
        }
    }

    protected function createWithRepository()
    {
        $modelName = Str::studly($this->argument('name'));
        $modelPathData = $this->findModel($modelName);

        if (!$modelPathData) {
            $this->error("Model [{$modelName}] not found.");
            return 1;
        }

        $subNamespace = $modelPathData['sub_namespace'];
        $modelNamePlural = $subNamespace ?: Str::plural($modelName);
        $modelNameSingular = $modelName;
        $modelNameCamel = Str::camel($modelNameSingular);

        $interfacePath = $subNamespace 
            ? "app/Interfaces/{$subNamespace}/{$modelNameSingular}RepositoryInterface.php"
            : "app/Interfaces/{$modelNameSingular}RepositoryInterface.php";
        
        $interfaceNamespace = $subNamespace 
            ? "App\\Interfaces\\{$subNamespace}"
            : "App\\Interfaces";
        
        $repositoryPath = $subNamespace 
            ? "app/Repositories/{$subNamespace}/{$modelNameSingular}Repository.php"
            : "app/Repositories/{$modelNameSingular}Repository.php";
            
        $repositoryNamespace = $subNamespace 
            ? "App\\Repositories\\{$subNamespace}"
            : "App\\Repositories";
            
        $servicePath = $subNamespace 
            ? "app/Services/{$subNamespace}/{$modelNameSingular}Service.php"
            : "app/Services/{$modelNameSingular}Service.php";
            
        $serviceNamespace = $subNamespace 
            ? "App\\Services\\{$subNamespace}"
            : "App\\Services";
            
        $modelNamespace = $subNamespace 
            ? "App\\Models\\{$subNamespace}\\{$modelNameSingular}"
            : "App\\Models\\{$modelNameSingular}";

        $interfaceName = "{$modelNameSingular}RepositoryInterface";
        $this->createFromStub('repository.interface.stub', $interfacePath, [
            '{{namespace}}' => $interfaceNamespace,
            '{{baseInterfaceNamespace}}' => 'App\\Interfaces\\BaseInterface',
            '{{class}}' => $interfaceName,
            '{{baseInterface}}' => 'BaseInterface',
        ]);

        $repositoryName = "{$modelNameSingular}Repository";
        $this->createFromStub('repository.stub', $repositoryPath, [
            '{{namespace}}' => $repositoryNamespace,
            '{{interfaceNamespace}}' => "{$interfaceNamespace}\\{$interfaceName}",
            '{{modelNamespace}}' => $modelNamespace,
            '{{baseRepository}}' => 'App\\Repositories\\Repository',
            '{{class}}' => $repositoryName,
            '{{baseRepositoryClass}}' => 'Repository',
            '{{inferface}}' => $interfaceName,
            '{{ModelName}}' => $modelNameSingular,
            '{{modelName}}' => $modelNameCamel,
        ]);

        $this->createFromStub('service.stub', $servicePath, [
            '{{namespace}}' => $serviceNamespace,
            '{{ModelNamePlural}}' => $modelNamePlural,
            '{{ModelName}}' => $modelNameSingular,
            '{{modelName}}' => $modelNameCamel,
            '{{interfaceNamespace}}' => "{$interfaceNamespace}\\{$interfaceName}",
        ]);
        
        $this->updateRepositoryServiceProvider($modelNameSingular, $subNamespace);
    }

    protected function createPlainService()
    {
        $name = $this->argument('name');
        $parts = explode('\\', $name);
        $className = Str::studly(array_pop($parts)) . 'Service';
        $subNamespace = implode('\\', array_map([Str::class, 'studly'], $parts));

        $namespace = 'App\\Services';
        if ($subNamespace) {
            $namespace .= '\\' . $subNamespace;
        }

        $path = "app/Services/" . ($subNamespace ? str_replace('\\', '/', $subNamespace) . '/' : '') . "{$className}.php";

        $this->createFromStub('service.plain.stub', $path, [
            '{{namespace}}' => $namespace,
            '{{className}}' => $className,
        ]);
    }

    protected function findModel(string $modelName)
    {
        $rootPath = $this->laravel->basePath("app/Models/{$modelName}.php");
        if ($this->files->exists($rootPath)) {
            return [
                'path' => $rootPath,
                'sub_namespace' => '',
            ];
        }

        $modelDirectories = $this->files->directories($this->laravel->basePath('app/Models'));
        foreach ($modelDirectories as $dir) {
            $path = "{$dir}/{$modelName}.php";
            if ($this->files->exists($path)) {
                return [
                    'path' => $path,
                    'sub_namespace' => basename($dir),
                ];
            }
        }
        
        return null;
    }
    
    protected function createFromStub(string $stubName, string $path, array $replacements)
    {
        $stub = $this->files->get($this->laravel->basePath("stubs/{$stubName}")); 

        foreach ($replacements as $key => $value) {
            $stub = str_replace($key, $value, $stub);
        }

        $this->makeDirectory(dirname($this->laravel->basePath($path)));

        $this->files->put($this->laravel->basePath($path), $stub);
        $this->info(class_basename($path) . " created successfully.");
    }

    protected function makeDirectory($path)
    {
        if (!$this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0755, true, true);
        }
    }

    protected function updateRepositoryServiceProvider(string $modelName, string $subNamespace)
    {
        $providerPath = $this->laravel->basePath('app/Providers/RepositoryServiceProvider.php');
        $content = $this->files->get($providerPath);

        $interface = "{$modelName}RepositoryInterface";
        $repository = "{$modelName}Repository";
        
        $interfaceNamespace = $subNamespace 
            ? "App\\Interfaces\\{$subNamespace}\\{$interface}"
            : "App\\Interfaces\\{$interface}";
            
        $repositoryNamespace = $subNamespace 
            ? "App\\Repositories\\{$subNamespace}\\{$repository}"
            : "App\\Repositories\\{$repository}";

        $useStatements = "use {$interfaceNamespace};\nuse {$repositoryNamespace};";
        
        $content = preg_replace(
            '/(namespace App\\\Providers;)/', 
            "$1\n{$useStatements}", 
            $content
        );

        $binding = "\$this->app->bind({$interface}::class, {$repository}::class);";
        $content = preg_replace('/(public function boot\(\): void\n\s*\{\n)/', "$1        {$binding}\n", $content);

        $this->files->put($providerPath, $content);
        $this->info('RepositoryServiceProvider updated successfully.');
    }
}