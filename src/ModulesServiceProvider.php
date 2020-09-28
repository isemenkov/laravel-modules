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
        $this->app->singleton(\isemenkov\Modules\ModulesManager::class, 
            function($app) {
            
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
        // Publish config.
        $this->publishes([
            __DIR__ . '/config/modules.php' => config_path('modules.php'),
        ]);
        
        // Register new blade directive.
        Blade::directive('module', function($position) {
            // Remove all posible quotes.
            $position = str_replace("'\"", "", $position);
            
            return "<?php echo Modules::render('{$position}'); ?>";
        });
    }
}
