<?php

namespace isemenkov\Modules;

interface Module {
    
    /**
     * Return current module template position label string.
     * If function isn't exists as position label uses lowercase module class 
     *   name.
     * If return callback function then ModulesManager call it to resolve 
     *   module position.
     * 
     * @param void
     * @return string|callable Position label | Callback function.
     * 
     * public function position() {
     *     return "module.position";
     * }
     */
    
    /**
     * Return current module sort priority value.
     * This value is used to sort multiples modules registered in one position.
     * If function isn't exists priority sets as zero.
     * If return callback function then ModulesManager call it to resolve 
     *   module priority weight.
     * 
     * @param void
     * @return integer|callable Sort module priority weight | Callback function.
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
     * @param void
     * @return string|callable Module permissions string | Callback function
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
     * If return callback function then ModulesManager call it to resolve
     *   caching strategy.
     * 
     * @return bool|string|callable Module cached stategy.
     * 
     * public function cache() {
     *     return "module.cache_key";
     * }
     */

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
     *
     * public function cacheTime() {
     *     return 3600; 
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