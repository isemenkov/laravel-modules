<?php

namespace isemenkov\Modules;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use isemenkov\Modules\ModulesManager;

class ModulesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\isemenkov\Modules\ModulesManager::class, function($app) {
            return new ModulesManager;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('module', function($position) {
            return "<?php echo Module::render('{$position}'); ?>";
        });
    }
}
