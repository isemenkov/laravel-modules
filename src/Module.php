<?php

namespace isemenkov\Modules;

interface Module {
    
    /**
     * Return current module template position label string.
     * If function isn't exists as position label uses lowercase module class 
     * name.
     * 
     * @param null
     * @return String Position label.
     * 
     * public function position() {
     *     return "module.position";
     * }
     */
    
    /**
     * Return current module sort priority value.
     * This value is used to sort multiples modules registered in one position.
     * If function isn't exists priority sets as zero.
     * 
     * @param null
     * @return Integer Sort module priority weight.
     * 
     * public function priority() {
     *     return -1;
     * } 
     */

    /**
     * Return current module needs permissions.
     * If return bool value then on true module is rendered, on false none.
     * If return value type is string it is a permission access to render. 
     * If return callback function then ModulesManager call it to resolve 
     *   permissions.
     * If function isn't exists module render always.
     *  
     * @param null
     * @return String|Callable Module permissions string | Callback function
     * 
     * public function permission() {
     *     return "module.permission";
     * }
     */
    
    /**
     * Current module caching strategy. 
     * If return bool value then on true module cached always. As a cache key 
     *   uses lowercase module name. On false module newer cached.
     * If return value type is string it is uses as cache key.
     * If return callback function then ModulesManager call it to rsolve
     *   caching strategy.
     * 
     * @return Bool|String|Callable Module cached stategy.
     * 
     * public function cache() {
     *     return "module.cache_key";
     * }
     */

    /**
     * Render current module.
     * If function exists it is uses to render current module.
     * 
     * @param mixing Template render arguments.
     * @return String|Serializable Rendered view.
     * 
     * public function render($args = null) {
     *     return View::make("module.view");
     * }
     */
}