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
     * public function position();
     */
    
    /**
     * Return current module sort priority value.
     * If function isn't exists priority sets as zero.
     * 
     * @param null
     * @return Integer Sort module priority weight.
     * public function priority(); 
     */

    /**
     * Return current module needs permissions. 
     * You can set callback function as parameter which ModulesManager may call 
     * to accepted permissions.
     *  
     * @param null
     * @return String|Callable Module permissions string | Callback function
     * public function permission();
     */
    
    /**
     * Render current module to html.
     * @param mixing Template render args.
     * @return String Html view.
     */
    public function render($args = null);
}