# laravel-modules

A simple package for working with template modules.



### Table of contents

* [What is?](#what-is)
  * [Solution](#solution)
  * [Module structure](#module-structure)
  * [Register module](#register-module)
* [Requirements](#requirements)
* [Installation](#installation)
* [Usage](#usage)



### What is?

#### Solution

laravel-modules is a package designed to separate the logic of an individual component from the general logic of the application. It allows to create and manage separate logical blocks (modules). Each module encapsulates methods for managing render logic, access control, caching, etc.

#### Module structure

Each module is standard php class with several optional methods.

```php
namespace App\Modules\TestModule;

class TestModule {
    
   /**
     * Return current module template position label string.
     * If function isn't exists as position label uses lowercase module class 
     *   name.
     * If return callback function then ModulesManager call it to resolve 
     *   module position.
     * 
     * @param void
     * @return string|callable Position label | Callback function.
     */
    public function position() {
        return "module.position";
    }

   /**
     * Return current module sort priority value.
     * This value is used to sort multiples modules registered in one position.
     * If function isn't exists priority sets as zero.
     * If return callback function then ModulesManager call it to resolve 
     *   module priority weight.
     * 
     * @param void
     * @return integer|callable Sort module priority weight | Callback function.
     */ 
    public function priority() {
        return -1;
    }
    
   /**
     * Return current module needs permissions.
     * If return bool value then on true module is rendered, on false none.
     * If return value type is string it is a permission access to render. 
     * If return callback function then ModulesManager call it to resolve 
     *   permissions.
     * If function isn't exists module render always.
     *  
     * @param void
     * @return string|callable Module permissions string | Callback function
     */ 
    public function permission() {
        return "module.permission";
    }
    
   /**
     * Current module caching strategy. 
     * If return bool value then on true module cached always. As a cache key 
     *   uses lowercase module name. On false module newer cached.
     * If return value type is string it is uses as cache key.
     * If return callback function then ModulesManager call it to resolve
     *   caching strategy.
     * 
     * @return bool|string|callable Module cached stategy.
     */ 
    public function cache() {
        return "module.cache_key";
    }
    
   /**
     * Current module cache time.
     * If return bool value then on true module cached forever. On false module 
     *   newer cached.
     * Return value for cache timeout in seconds.
     * If return callback function then ModulesManager call it to resolve
     *   cache time.
     * 
     * @param void
     * @return bool|integer|callable 
     */
    public function cacheTime() {
        return 3600; 
    }

   /**
     * Render current module.
     * If function exists it is uses to render current module.
     * 
     * @param mixing Template render arguments.
     * @return string|serializable Rendered view.
     */ 
    public function render($args = null) {
        return View::make("module.view");
    }
}
```

#### Register module

Each module must be registered before use. You can use several ways for this.

- Set module class object.

```php
public function index(ModulesManager $manager) {
    $manager->registerModule([
        new TestModule($construct_args),
        /* Register as many modules as you need */
    ]);

    return View::make('frontend');
}
```

- Set full module string class name

```php
public function index(ModulesManager $manager) {
    $manager->registerModule([
        TestModule::class,
    ]);

    return View::make('frontend');
}
```

To pass arguments to a constructor, pass an array, the first element of which will be the class name, and the second element will be arguments to the constructor.

```php
public function index(ModulesManager $manager) {
    $manager->registerModule([
        [TestModule::class, $construct_args],
    ]);

    return View::make('frontend');
}
```
If you need to pass several parameters to the constructor, wrap them in an array.

```php
public function index(ModulesManager $manager) {
    $manager->registerModule([
        [TestModule::class, [$construct_arg1, $consruct_arg2]],
    ]);

    return View::make('frontend');
}
```

Add the third element of the array to pass arguments to the render method.

```php
public function index(ModulesManager $manager) {
    $manager->registerModule([
        [TestModule::class, $consruct_args, $render_args],
    ]);

    return View::make('frontend');
}
```



### Requirements

* Laravel 5+
* PHP 5.3.7+



### Installation

Require this package with composer.

```composer
composer require isemenkov/laravel-modules
```



### Usage

```php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\View;
use isemenkov\Modules\ModulesManager;
use App\Modules\TestModule;

class TestController extends Controller {

    public function index(ModulesManager $manager) {
        $manager->registerModule([
            TestModule::class,
        ]);

        return View::make('frontend');
    }
}
```

```html
<!-- frontend.blade.php -->
<!DOCTYPE html>
<html>
  <body>
    <header>
      @module(module.position)    
    </header>
  <body>
</html>
```