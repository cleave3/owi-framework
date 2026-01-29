<?php

namespace Owi\console;

class Console
{
    public function run($argv)
    {
        $command = $argv[1] ?? 'help';
        $name = $argv[2] ?? null;

        switch ($command) {
            case 'make:controller':
                $this->makeController($name);
                break;
            case 'make:service':
                $this->makeService($name);
                break;
            case 'make:migration':
                $this->makeMigration($name);
                break;
            case 'make:middleware':
                $this->makeMiddleware($name);
                break;
            case 'make:model':
                $this->makeModel($name);
                break;
            case 'migrate':
                $this->migrate();
                break;
            case 'start':
                $this->start($name);
                break;
            default:
                $this->showHelp();
                break;
        }
    }

    protected function makeController($name)
    {
        if (!$name) {
            $name = readline("Enter controller name e.g TestController: ");
        }

        $names = explode(" ", $name);
        $output = "";

        foreach ($names as $controllerName) {
            if (!preg_match('/^[A-Za-z]+$/', $controllerName)) {
                $output .= "\e[31m$controllerName is invalid. Operation unsuccessful\n";
                continue;
            }

            $controllerName = trim(preg_replace('/controller/i', '', ucwords($controllerName)));
            $className = $controllerName . "Controller";

            $dir = getcwd() . "/src/Controllers";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $fileUserPath = "./src/Controllers/{$className}.php";
            
            if (file_exists($fileUserPath)) {
                 $output .= "\e[31m$className already exists.\n";
                 continue;
            }

            $file = fopen($fileUserPath, "w");

            $code = "<?php\n\nnamespace App\Controllers;\n\nuse App\Controllers\Controller;\n\nclass {$className} extends Controller\n{\n\n\tpublic function index()\n\t{\n\t}\n\n\tpublic function add()\n\t{\n\t}\n\n\tpublic function edit()\n\t{\n\t}\n\n\tpublic function delete()\n\t{\n\t}\n}\n";
            fwrite($file, $code);
            fclose($file);

            $output .= "\e[92m src/Controllers/{$className}.php created successfully\n";
        }
        print $output;
    }

    protected function makeService($name)
    {
        if (!$name) {
            $name = readline("Enter service name e.g TestService: ");
        }

        $names = explode(" ", $name);
        $output = "";

        foreach ($names as $serviceName) {
            if (!preg_match('/^[A-Za-z]+$/', $serviceName)) {
                $output .= "\e[31m$serviceName is invalid. Operation unsuccessful\n";
                continue;
            }

            $serviceName = trim(preg_replace('/service/i', '', ucwords($serviceName)));
            $className = $serviceName . "Service";

            $dir = getcwd() . "/src/Services";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            $fileUserPath = "./src/Services/{$className}.php";

            if (file_exists($fileUserPath)) {
                $output .= "\e[31m$className already exists.\n";
                continue;
            }

            $file = fopen($fileUserPath, "w");

            $code = "<?php\n\nnamespace App\Services;\n\nclass {$className}\n{\n}\n";
            fwrite($file, $code);
            fclose($file);

            $output .= "\e[92m src/Services/{$className}.php created successfully\n";
        }
        print $output;
    }

    protected function makeModel($name)
    {
        if (!$name) {
            $name = readline("Enter model name e.g User: ");
        }

        $names = explode(" ", $name);
        $output = "";

        foreach ($names as $modelName) {
            if (!preg_match('/^[A-Za-z]+$/', $modelName)) {
                $output .= "\e[31m$modelName is invalid. Operation unsuccessful\n";
                continue;
            }

            $modelName = ucwords($modelName);
            $className = $modelName;

            $dir = getcwd() . "/src/Models";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            
            $fileUserPath = "./src/Models/{$className}.php";

            if (file_exists($fileUserPath)) {
                $output .= "\e[31m$className already exists.\n";
                continue;
            }

            $file = fopen($fileUserPath, "w");

            // Simple boiler plate for model.
            // Users should extend the App\Models\Model base class which extends Owi\database\Model
            
            $code = "<?php\n\nnamespace App\Models;\n\nuse App\Models\Model;\n\nclass {$className} extends Model\n{\n    protected \$table = '" . strtolower($modelName) . "s';\n}\n";
            
            fwrite($file, $code);
            fclose($file);

            $output .= "\e[92m src/Models/{$className}.php created successfully\n";
        }
        print $output;
    }

    protected function makeMigration($name)
    {
        if (!$name) {
            $name = readline("Enter migration name e.g create_users_table: ");
        }

        // Convert camelCase or studlyCase to snake_case if needed, but let's assume valid name for now
        // Actually best practice for migration files: YYYY_MM_DD_HHMMSS_name.php
        $timestamp = date('Y_m_d_His');
        $fileName = $timestamp . "_" . $name;
        
        // Class name should be StudlyCase
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));

        $dir = getcwd() . "/migrations";
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $fileUserPath = "$dir/{$fileName}.php";

        $file = fopen($fileUserPath, "w");

        // Migration logic needs to use Owi\database classes
        $code = "<?php\n\nuse Owi\database\Migration;\nuse Owi\database\Blueprint;\nuse Owi\database\Schema;\n\nclass {$className} extends Migration\n{\n    public function up()\n    {\n        Schema::create('example_table', function (Blueprint \$table) {\n            \$table->increments('id');\n            \$table->timestamps();\n        });\n    }\n\n    public function down()\n    {\n        Schema::drop('example_table');\n    }\n}\n";
        
        fwrite($file, $code);
        fclose($file);

        echo "\e[92m migrations/{$fileName}.php created successfully\n";
    }

    protected function migrate()
    {
        require_once getcwd() . '/vendor/autoload.php';
        (new \Owi\config\DotEnv(getcwd() . '/.env'))->load();
        
        $migrator = new \Owi\database\Migrator();
        $migrator->run();
    }

    protected function makeMiddleware($name)
    {
        if (!$name) {
            $name = readline("Enter middleware name e.g AuthMiddleware: ");
        }

        $names = explode(" ", $name);
        $output = "";

        foreach ($names as $middlewareName) {
            if (!preg_match('/^[A-Za-z]+$/', $middlewareName)) {
                $output .= "\e[31m$middlewareName is invalid. Operation unsuccessful\n";
                continue;
            }

            $middlewareName = trim(preg_replace('/middleware/i', '', ucwords($middlewareName)));
            $className = $middlewareName . "Middleware";

            $dir = getcwd() . "/src/Middleware";
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $fileUserPath = "./src/Middleware/{$className}.php";

            if (file_exists($fileUserPath)) {
                $output .= "\e[31m$className already exists.\n";
                continue;
            }

            $file = fopen($fileUserPath, "w");

            // Middleware interface is now in Owi\middleware or similar
            $code = "<?php\n\nnamespace App\Middleware;\n\nuse Owi\middleware\Middleware;\n\nclass {$className} implements Middleware\n{\n    public function handle(\$request, callable \$next)\n    {\n        return \$next(\$request);\n    }\n}\n";
            fwrite($file, $code);
            fclose($file);

            $output .= "\e[92m src/Middleware/{$className}.php created successfully\n";
        }
        print $output;
    }

    protected function start($port)
    {
        $host = 'localhost';
        $port = $port ?: 8080;

        if (!ctype_digit((string)$port)) {
            echo "\e[31mInvalid port number: $port\n";
            exit(1);
        }

        $originalPort = $port;
        
        while (!$this->isPortAvailable($host, $port)) {
            echo "\e[33mPort $port is in use.\n";
            $port++;
        }

        if ($port != $originalPort) {
            echo "\e[33mSwitching to next available port: $port\n";
        }

        $command = sprintf('php -S %s:%d', $host, $port);
        
        echo "\e[92mOwi Framework Development Server started at http://{$host}:{$port}\n";
        echo "\e[39mPress Ctrl+C to stop.\n";
        
        passthru($command);
    }

    protected function isPortAvailable($host, $port)
    {
        $connection = @fsockopen($host, $port);
        if (is_resource($connection)) {
            fclose($connection);
            return false;
        }
        return true;
    }

    protected function showHelp()
    {
        echo "Owi Framework CLI\n";
        echo "Usage:\n";
        echo "  php owi make:controller [Name]   Create a new controller\n";
        echo "  php owi make:model [Name]        Create a new model\n";
        echo "  php owi make:service [Name]      Create a new service\n";
        echo "  php owi make:middleware [Name]   Create a new middleware\n";
        echo "  php owi make:migration [Name]    Create a new migration\n";
        echo "  php owi migrate                  Run pending migrations\n";
        echo "  php owi start [Port]             Start the development server (default: 8080)\n";
    }
}
