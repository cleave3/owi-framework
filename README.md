# Owi Framework

A modern, lightweight PHP framework designed for speed and simplicity. Built for building robust APIs and MVC applications with a developer-friendly experience.

## Features at a Glance

- **🚀 Modern Architecture**: Dependency Injection Container, Middleware Pipeline, and Event-driven design.
- **💾 Fluent Database**: Expressive Query Builder (`DB::table('users')->get()`) replacing raw SQL.
- **🛡️ Secure by Default**: Built-in Authentication, CSRF protection, and SQL injection prevention.
- **📦 Migrations**: Version-control your database schema.
- **🛠️ Powerful CLI**: Unified `owi` tool for scaffolding and management.
- **🐛 Error Handling**: Beautiful error pages for debugging and JSON responses for APIs.

---

## 1. Getting Started

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

Ensure you have the corresponding PHP PDO extension installed.

### Usage

Start the development server:

```bash
php owi start
# Or specify a port
php owi start 9000
```
Visit `http://localhost:8080` (or your custom port) in your browser.

---

## 2. Database & Migrations

### Migrations

Manage your database schema using PHP classes.

**Create a Migration:**
```bash
php owi make:migration create_users_table
```

**Run Migrations:**
```bash
php owi migrate
```

**Example Migration:**
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

**Example Migration:**
```php
public function up() {
    Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('email');
        $table->string('password');
        $table->timestamps();
    });
    
    // Foreign Key Example
    Schema::create('posts', function (Blueprint $table) {
        $table->increments('id');
        $table->integer('user_id')->foreign('user_id')
              ->references('id')->on('users')
              ->onDelete('CASCADE');
        $table->string('title');
    });
}
```

### Models & Relationships

Models provide an object-oriented way to interact with your database tables.

**1. Defining a Model:**
Create a class extending `App\models\Model`:

```php
namespace App\models;

class User extends Model {
    // Table is automatically 'users'
    // Primary key is automatically 'id'
}
```

**2. Relationships:**
Define relationships using fluental methods:

```php
class User extends Model {
    // One-to-One
    public function profile() {
        return $this->hasOne(Profile::class);
    }
    
    // One-to-Many
    public function posts() {
        return $this->hasMany(Post::class);
    }
    
    // Many-to-Many
    public function roles() {
        return $this->belongsToMany(Role::class);
    }
}

class Post extends Model {
    // Inverse Relationship
    public function user() {
        return $this->belongsTo(User::class);
    }
}
```

**3. Using Models:**

```php
// Fetch User and their posts
$user = (new User())->where('id', 1)->first();
$posts = $user->posts();

// Chain query builder methods
$activeUsers = (new User())->where('active', 1)->get();
```

### Query Builder

Interact with your database fluently using the `DB` facade.

```php
use App\utils\DB;

// Get all users
$users = DB::table('users')->get();

// Find one
$user = DB::table('users')->where('id', 1)->first();

// Create
DB::table('users')->insert([
    'email' => 'jane@example.com', 
    'password' => password_hash('secret', PASSWORD_DEFAULT)
]);

// Update
DB::table('users')->where('id', 1)->update(['active' => 1]);

// Delete
DB::table('users')->where('id', 1)->delete();
```

---

## 3. Architecture

### Controllers & Dependency Injection

Controllers allow you to group related request handling logic. Dependencies are automatically injected into the constructor.

**Create a Controller:**
```bash
php owi make:controller UserController
```

**Example:**
```php
namespace App\controllers;

use App\services\UserService;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        parent::__construct();
        $this->userService = $userService;
    }

    public function index()
    {
        return $this->userService->getAll();
    }
}
```

### Middleware

Filter HTTP requests entering your application (e.g., Auth, CORS).

**1. Create Middleware:**
```bash
php owi make:middleware AuthMiddleware
```
This creates `AuthMiddleware.php` in `middleware/`.

```php
namespace App\middleware;

use App\core\Auth;

class AuthMiddleware implements Middleware
{
    public function handle($request, callable $next)
    {
        if (!Auth::check()) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            return;
        }
        return $next($request);
    }
}
```

**2. Register in Controller:**
```php
protected $middlewares = [
    \App\middleware\AuthMiddleware::class
];
```

---

## 4. Security

### Authentication

Manage user sessions with the `Auth` class.

```php
use App\core\Auth;

if (Auth::attempt(['email' => $email, 'password' => $pass])) {
    // Logged in
}

$user = Auth::user();
Auth::logout();
```

### CSRF Protection

Protect your forms from Cross-Site Request Forgery.

```html
<form method="POST" action="/login">
    <?php echo csrf_field(); ?>
    <button type="submit">Login</button>
</form>
```

Ensure `CsrfMiddleware` is enabled for your routes.

---

## 5. CLI Reference

| Command | Description |
|---------|-------------|
| `php owi start` | Start the development server |
| `php owi make:controller [Name]` | Create a new Controller class |
| `php owi make:service [Name]` | Create a new Service class |
| `php owi make:middleware [Name]` | Create a new Middleware class |
| `php owi make:migration [Name]` | Create a new Migration file |
| `php owi migrate` | Run pending database migrations |

---

## folder Structure

- **/config**: Application configuration files.
- **/controllers**: Controller classes.
- **/core**: Framework core components (Auth, Container, Router).
- **/database**: Query Builder, Migrations, and Schema logic.
- **/middleware**: Middleware classes.
- **/migrations**: Database migration files.
- **/public**: Public assets (CSS, JS, Images).
- **/router**: Routing logic.
- **/services**: Business logic classes.
- **/utils**: Helper utilities (Sanitize, Security).
- **/views**: HTML templates.

---
