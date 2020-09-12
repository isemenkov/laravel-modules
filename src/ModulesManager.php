<?php

namespace isemenkov\Modules;

use isemenkov\Modules\Module;
use Illuminate\Support\Facades\Config;

class ModulesManager {
    /**
     * Registered modules list.
     */
    protected $modules = [];

    /**
     * Store true if modules are sorted.
     */
    protected $sorted = false;

    /**
     * Register new template module.
     * Set isemenkov\Modules\Module object or array of modules.
     * 
     * @param isemenkov\Modules\Module|array| $module 
     * Registered module object | Array with modules objects 
     * @return null
     */
    public function registerModule($module, $args = null) {
        if(is_array($module)) {
            foreach($module as $module_object) {
                if(is_array($module_object)) {
                    $this->registerModule($module_object[0], $module_object[1]);    
                } else {
                    $this->registerModule($module_object);
                }
            }
            return;
        }

        $position = method_exists($module, 'position') ?
            $module->position() :
            str_replace('module', '',
                strtolower((new \ReflectionClass($module))->getShortName())
            );

        $priority = method_exists($module, 'priority') ?
            $module->priority() :
            0;

        $this->modules[$position][] = [
            'priority'  => $priority,
            'module'    => $module,
            'args'      => $args,
        ];

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
    }

    /**
     * Return $position rendered modules.
     * @param String $position Modules position to render.
     * @return String All renderer modules as $position as html.
     */
    public function render($position) {
        if (!$this->sorted) {
            $this->sortModules();
            $this->sorted = true;
        }
        
        if (isset($this->modules[$position]) ) {
            $html = '';
            foreach($this->modules[$position] as $module) {
                $html .= call_user_func_array([$module["module"], 'render'],
                    !is_null($module['args']) ? [$module['args']] : []);
            }
            return $html;
        }  
        return '';
    }
}