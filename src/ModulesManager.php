<?php

namespace isemenkov\Modules;

use isemenkov\Modules\Module;
use Illuminate\Support\Facades\Cache;

class ModulesManager {

    /**
     * Registered modules list.
     */
    protected $modules = [];

    /**
     * Store true if modules was sort.
     */
    protected $sorted = false;

    /**
     * Register new template module.
     * 
     * @param isemenkov\Modules\Module|array| $module 
     * Register module object | Array with modules objects 
     * @return null
     */
    public function registerModule($module, $args = null) {
        if(is_array($module)) {
            foreach($module as $module_object) {
                if(is_array($module_object)) {
                    $this->registerModule($module_object[0], 
                        count($module_object) > 1 ? $module_object[1] : null);    
                } else {
                    $this->registerModule($module_object);
                }
            }
            return;
        }

        // Get module render position value.
        $position = method_exists($module, 'position') ?
            $module->position() :
            str_replace('module', '',
                strtolower((new \ReflectionClass($module))->getShortName())
            );

        // Get module position in render queue.
        $priority = method_exists($module, 'priority') ?
            $module->priority() :
            0;

        // Store module in list.
        $this->modules[$position][] = [
            'priority'  => $priority,
            'module'    => $module,
            'args'      => $args,
        ];

        // Registered new module(s), not sorted yet.
        $this->sorted = false;
    }

    /**
     * Return all registered modules positions.
     * 
     * @param nothing
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
     * @return Integer Compare result.
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
     * Sort registered modules by priority.
     * 
     * @param nothing
     * @return nothing
     */
    protected function sortModules() {
        $modulePositions = array_keys($this->modules);
        foreach($modulePositions as $position) {
            uasort($this->modules[$position], [$this, 'compareModules']);
        }
        $this->sorted = true;
    }

    /**
     * Return $position rendered modules.
     * 
     * @param String $position Modules position to render.
     * @return String All renderer modules as $position as html.
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

                // Try to find module method cache.
                $module_cache_key = null;
                if(method_exists($module['module'], 'cache')) {
                    $cache = call_user_func([$module['module'], 'cache']);
                    
                    // If result is function.
                    if(is_callable($cache)) {
                        $cache = $cache();
                    }

                    // Get module cache key.
                    if(is_bool($cache) && $cache) {
                        $module_cache_key = 
                            (new \ReflectionClass($module['module']))
                            ->getShortName();
                    } else if(is_string($cache)) {
                        $module_cache_key = $cache;    
                    };

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

                    Cache::put($module_cache_key, $module_view);
                }
                
                $html .= $module_view;
            }
            return $html;
        }  
        return '';
    }
}