# Owi Framework

A modern, lightweight PHP framework designed for speed and simplicity. Built for building robust APIs and MVC applications with a developer-friendly experience.

## Features at a Glance

- **🚀 Modern Architecture**: Dependency Injection Container, Middleware Pipeline, and Event-driven design.
- **💾 Fluent Database**: Expressive Query Builder (`DB::table('users')->get()`) replacing raw SQL.
- **🛡️ Secure by Default**: Built-in Authentication, CSRF protection, and SQL injection prevention.
- **📦 Migrations**: Version-control your database schema.
- **🛠️ Powerful CLI**: Unified `owi` tool for scaffolding and management.
- **📄 Native Docs**: Built-in API documentation viewer at `/api/docs`.
- **🐛 Error Handling**: Beautiful error pages and JSON error responses.

---

## 1. Getting Started

### Requirements

- PHP >= 8.0
- Composer

### Installation

Clone the repository and install dependencies:

```bash
git clone https://github.com/cleave3/owi-framework.git
cd owi-framework
composer install
```

### Configuration

Copy the example environment file and configure your database:

```bash
cp .env.example .env
```

Edit `.env`:

```ini
APP_NAME="Owi App"
APP_URL=http://localhost:8080
APP_DEBUG=true

DB_DRIVER=mysql
DB_HOST=127.0.0.1
DB_NAME=owi_db
DB_USER=root
DB_PASS=password
```

### Supported Databases

The framework supports the following drivers via `DB_TYPE`:

- **MySQL** (`mysql`): Requires `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`. Optional: `DB_PORT`.
- **PostgreSQL** (`pgsql`): Requires `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`. Optional: `DB_PORT`.
- **SQLite** (`sqlite`): Set `DB_TYPE=sqlite` and `DB_NAME` to the absolute path of your database file.
- **SQL Server** (`sqlsrv`): Requires `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`. Optional: `DB_PORT`.

### Usage

Start the development server:

```bash
php owi start
# Or specify a port
php owi start 9000
```
Visit `http://localhost:8080` (or your custom port) in your browser.

---

## 2. Project Structure

Owi Framework follows a familiar MVC structure with clear separation of concerns.

- **owicore/**: Framework core components. **Do not edit.**
    - `console/`: CLI logic.
    - `database/`: Database driver and ORM.
    - `router/`: Router engine.
    - `docs/`: Unified documentation features.
- **src/**: Application code. **Your workspace.**
    - `Controllers/`: HTTP Controllers (e.g., `PostController`).
    - `Models/`: Database Models (e.g., `Post`, `User`).
    - `Services/`: Business logic layer (e.g., `PostService`).
    - `Middleware/`: HTTP middleware.
    - `Views/`: Application views/templates.
    - `Routes/`: Route definition files.
- **migrations/**: Database schema migration files.

---

## 3. Routing

Routes are defined in the `src/Routes/` directory. All PHP files in this directory are automatically loaded by the framework.

### Recommended Convention
- **Web Routes**: Define browser-based routes (returning views) in files ending in `.web.php` (e.g., `post.web.php`).
- **API Routes**: Define API routes (returning JSON) in files ending in `.api.php` (e.g., `posts.api.php`).

### Example (`src/Routes/post.web.php`)

```php
use Owi\router\Route;
use App\Controllers\PostController;

Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/create', [\App\Controllers\PostController::class, 'create']);
```

### Example (`src/Routes/posts.api.php`)

```php
use Owi\router\Route;
use App\Controllers\PostController;

Route::get('/api/posts', [PostController::class, 'apiIndex']);
Route::get('/api/posts/{id}', [PostController::class, 'apiShow']);
```

### API Documentation
The framework includes a built-in API documentation viewer.
Visit **`/api/docs`** to see a list of all registered routes, their methods, and handlers.

---

## 4. Database & Models

### Migrations

Manage your database schema using PHP classes.

```bash
# Create a new migration
php owi make:migration create_posts_table

# Run pending migrations
php owi migrate
```

### Models

Models provide an object-oriented interface to your database tables. They extend `App\Models\Model`.

**Create a Model:**
```bash
php owi make:model Post
```

**Using Models:**

`save()`: Wraps both INSERT and UPDATE logic.
```php
public function up() {
    Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('email');
        $table->string('password');
        $table->timestamps();
    });
}
```

### Models & Relationships

Models provide an object-oriented way to interact with your database tables.

**1. Defining a Model:**
Create a class extending `App\Models\Model`:

```php
$user = (new User())->find(1);
```

**Relationships:**
Define fluent relationships:
```php
class Post extends Model {
    public function author() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
```

### Query Builder

Use the `DB` facade for fluent query construction:

```php
use Owi\utils\DB;

// Fetch all rows
$users = DB::table('users')->get();

// Complex query
$activeUsers = DB::table('users')
    ->where('active', 1)
    ->orderBy('created_at', 'DESC')
    ->limit(10)
    ->get();
```

---

## 5. Controllers & Views

### Controllers

Controllers group request logic. Create them via CLI:
```bash
php owi make:controller PostController
```

### Views Helper

We recommend using the global `view()` helper function to render templates. It automatically extracts data arrays into variables.

*Controller Example:*
```php
public function index() {
    $posts = $this->postService->getAll();
    // Renders src/Views/posts/index.php
    echo view('posts/index', ['posts' => $posts]);
}
```

*View Example (`src/Views/posts/index.php`):*
```php
protected $middlewares = [
    \App\Middleware\AuthMiddleware::class
];
```

---
---

## 6. Utilities

The Owi Framework provides several helper classes in `Owi\utils\` to make development easier.

### Validator
A fluent validation library.

```php
use Owi\utils\Validator;

$emailValidator = Validator::owi($email)->required()->email()->error("Invalid Email")->exec();
$passValidator  = Validator::owi($password)->required()->string()->exec();

$result = Validator::validate([$emailValidator, $passValidator]);

if (!$result['isvalid']) {
    return Response::json(['errors' => $result['errors']], 400);
}
```

### DB Facade
Access the database instance or start transactions.

```php
use Owi\utils\DB;

// Query Builder
$users = DB::table('users')->where('active', 1)->get();

// Raw Query
$results = DB::query("SELECT * FROM users WHERE id = ?", [1])->fetch();

// Transactions
DB::startTransaction();
try {
    // ... operations
    DB::commit();
} catch (\Exception $e) {
    DB::rollback();
}
```

### HTTP Client
Simple wrapper around cURL for making external requests.

```php
use Owi\utils\Http;

// GET Request
$response = Http::get('https://api.example.com/data');

// POST Request
$response = Http::post('https://api.example.com/submit', ['foo' => 'bar'], ['Content-Type: application/x-www-form-urlencoded']);
```

### Security Helpers
Global helper functions for security (available everywhere).

```php
// Generate CSRF Token
$token = csrf_token();

// Generate Hidden Input Field for Forms
echo csrf_field(); 
// Outputs: <input type="hidden" name="_token" value="...">
```

---

## 7. CLI Reference


| Command | Description |
|---------|-------------|
| `php owi start` | Start the development server |
| `php owi make:controller [Name]` | Create a new Controller class |
| `php owi make:model [Name]` | Create a new Model class |
| `php owi make:service [Name]` | Create a new Service class |
| `php owi make:middleware [Name]` | Create a new Middleware class |
| `php owi make:migration [Name]` | Create a new Migration file |
| `php owi migrate` | Run pending database migrations |

---
