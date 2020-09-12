<?php

namespace isemenkov\Modules;

abstract class Module {
    /**
     * Return module position.
     * @param nothing
     * @return String Position label.
     */
    abstract public function position();

    /**
     * Return module sort priority value.
     * @param nothing
     * @return Integer Sort module priority weight.
     */
    abstract public function priority();

    /**
     * Return module needs permission.
     * @param nothing
     * @return String Module permissions.
     */
    abstract public function permission();

    /**
     * Render current module to html.
     * @param mixing Template render args.
     * @return String Html view.
     */
    abstract public function render($args = null); 
}