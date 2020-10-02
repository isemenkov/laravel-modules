<?php

namespace isemenkov\Modules;

use isemenkov\Modules\Module;
use Illuminate\Support\Facades\Cache;

final class ModulesManager {

    /**
     * Registered modules.
     * 
     * @var array
     */
    private $modules = [];

    /**
     * Default module weight.
     * 
     * @var integer
     */
    private $moduleDefaultWeight;

    /**
     * Default module cache time.
     * 
     * @var integer
     */
    private $moduleDefaultCacheTime;

    /**
     * Store true if modules was sort.
     * 
     * @var boolean
     */
    private $sorted = false;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->getConfigData();
    }

    /**
     * Load configuration.
     * 
     * @param void
     * @return void
     */
    private function getConfigData() {
        $this->moduleDefaultWeight = config('modules.default_priority', 0);
        $this->moduleDefaultCacheTime = config('modules.default_cache_time', 
            3600);
    }

    /**
     * Resolve callbacks args.
     * 
     * @param stdClass $module
     * @param string $method Get module method
     * @return mixed
     */
    private function getValue($module, $method) {
        
        // Check if module has position method.
        if(method_exists($module, $method)) {
            $result = $module->$method();

            // Check if it is function and resolve it.
            if(is_callable($result)) {
                $result = call_user_func($result);
            }

            return $result;
        }

        // Value isn't.
        return null;
    }

    /**
     * Sort registered modules by its priority.
     * 
     * @return void
     */
    private function sortModules() {

        // Get all module positions.
        $modulePositions = array_keys($this->modules);

        // Modules sorting by its weight in concerete position.
        foreach($modulePositions as $position) {
            uasort($this->modules[$position], [$this, 'compareModules']);
        }

        // Set all modules sorted.
        $this->sorted = true;
    }

    /**
     * Get module template position name.
     * 
     * @param stdClass $module
     * @return string Module position name
     */
    private function getModulePosition($module) {

        // Check if module has position method.
        $result = $this->getValue($module, 'position'); 

        if(! is_null($result)) {
            return $result;
        }

        // Return lower case module short class name without 'module' substring 
        //   inside.
        $class = (new \ReflectionClass($module))->getShortName();
        return str_replace('module', '', strtolower($class));
    }

    /**
     * Get module priority weight.
     * 
     * @param stdClass $module
     * @return integer Module priority weight.
     */
    private function getModulePriority($module) {

        // Check if module has priority method.
        $result = $this->getValue($module, 'priority');

        if(! is_null($result)) {
            return $result;
        }

        // Return default module weight.
        return $this->moduleDefaultWeight;
    }

    /**
     * Get module cache time.
     * 
     * @param stdClass $module
     * @return integer|void
     */
    public function getModuleCacheTime($module) {

        // Check if module has cacheTime method.
        $result = $this->getValue($module, 'cacheTime');

        if(is_bool($result) && $result) {
            // Cache forever.
            return null;
        } else if(is_integer($result)) {
            return $result;
        }

        // Return default value.
        return $this->moduleDefaultCacheTime;
    }

    /**
     * Store concrete module.
     * 
     * @param stdClass $module
     * @param mixed $args Module render function arguments.
     * @return void
     */
    private function storeModule($module, $args) {

        // Get module render position value.
        $position = $this->getModulePosition($module);

        // Get module position in render queue.
        $priority = $this->getModulePriority($module);

        // Store module in array.
        $this->modules[$position][] = [
            'priority'  => $priority,
            'module'    => $module,
            'args'      => $args,
        ];

        // Registered new module(s), not sorted yet.
        $this->sorted = false;
    }

    /**
     * Get module cache key.
     * 
     * @param stdClass $module
     * @return string
     */
    private function getModuleCacheKey($module) {
        $result = null;
        
        // Check if module has cache method.
        $result = $this->getValue($module, 'cache');
        
        if(is_string($result) && !empty($result)) {
            return $result;
        }

        if(is_bool($result) && $result) {
            return (new \ReflectionClass($module))->getShortName();
        }
        
        return '';
    }

    /**
     * Register new template module.
     * 
     * @param isemenkov\Modules\Module|string|array $module Register module 
     *  object | Register module full name | Array with modules objects or 
     *  strings
     * @param mixed $construct_args If $module is string this param used for
     *  module constructor.
     * @param mixed $render_args Module render arguments.
     * @return void
     */
    public function registerModule($module, $construct_args = null, 
        $render_args = null) {
        
        // Check if $module is a concrete module class name
        if(is_string($module)) {
            $module = new $module($construct_args);
        }
        
        // Check if input is array of modules.
        if(is_array($module)) {

            // For each module.
            foreach($module as $module_item) {
                
                // Check if item is a module class name
                if (is_string($module_item)) {
                    $module_item = new $module_item($construct_args);
                }
                
                // Check if it is array of modules.
                if(is_array($module_item)) {
                    call_user_func_array([$this, 'registerModule'], 
                        $module_item);
                    continue;
                }

                // Store current module.
                $this->storeModule($module_item, null);
            }
            return;
        }

        // Store current module.
        $this->storeModule($module, $render_args);
    }

    /**
     * Return all registered modules positions.
     * 
     * @param void
     * @return array Modules positions.
     */
    public function modulesPositions() {
        return array_keys($this->modules);
    }

    /**
     * Return 0 if compare modules priority as equals, -1 if first modules less,
     * 1 if second module priority less.
     * 
     * @param array $module1 First compared module.
     * @param array $module2 Second compared module.
     * @return integer Compare result.
     */
    protected function compareModules(array $module1, array $module2) {
        if ($module1['priority'] === $module2['priority']) {
            return 0;
        }

        if ($module1['priority'] < $module2['priority']) {
            return 1;
        }

        return -1;
    }

    /**
     * Return $position rendered modules.
     * 
     * @param string $position Modules position to render.
     * @return string All renderer modules as $position as html.
     */
    public function render($position) {
        
        // Sort modules by priority.
        if (!$this->sorted) {
            $this->sortModules();
        }
        
        // Render module by $position if exists.
        if (isset($this->modules[$position]) ) {
            $html = '';

            // Foreach module in current render position.
            foreach($this->modules[$position] as $module) {

                // Try to get module cache.
                $module_cache_key = $this->getModuleCacheKey($module['module']);
                if(!empty($module_cache_key)) {

                    // If module view cached return it.
                    if(!is_null($module_cache_key) && 
                        Cache::has($module_cache_key)) {
                        
                            $html .= Cache::get($module_cache_key);
                            continue;
                    }
                }
                
                // Try to find module method render.
                if(!method_exists($module['module'], 
                    'render')) {
                    
                    continue;
                }
                
                // Render module.
                $module_view = (String)call_user_func_array(
                    [$module["module"], 'render'], 
                    !is_null($module['args']) ? [$module['args']] : []);
                
                // Cached module render result.
                if(is_string($module_cache_key) || 
                    (is_bool($module_cache_key) && $module_cache_key)) {

                    // Get module cache time.    
                    $cacheTime = $this->getModuleCacheTime($module['module']);

                    if(! is_null($cacheTime)) {
                        Cache::put($module_cache_key, $module_view, $cacheTime);
                    } else {
                        Cache::forever($module_cache_key, $module_view);
                    }
                }
                
                $html .= $module_view;
            }
            return $html;
        }  
        return '';
    }
}