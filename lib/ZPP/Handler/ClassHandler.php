<?php

namespace ZPP\Handler;

use ZPP\ZPP;
use ZPP\AbstractHandler;

class ClassHandler extends AbstractHandler {
    protected $isiParentSpecified = false;

    protected $classCache = null;

    public function __construct($isParentSpecified = false) {
        $this->isParentSpecified = (bool) $isParentSpecified;
    }

    public function handle($arg, array $results, &$result_num, &$error) {
        if ($this->isParentSpecified) {
            $parent = $results[$result_num];
            if (is_object($parent)) {
                $parent = get_class($parent);
            }
        }
        switch (gettype($arg)) {
            case ZPP::IS_OBJECT:
                $arg = get_class($arg);
            case ZPP::IS_STRING:
                if (!class_exists($arg)) {
                    break;
                }
                if ($this->isParentSpecified && !$this->extendsFrom($arg, $parent)) {
                    break;
                }
                $results[$result_num] = $arg;
                return;
        }
        if ($this->isParentSpecified) {
            $error = sprintf("to be a class name derived from %s, '%s' given", $class, $arg);
            return true;
        }
        return "valid class";
    }

    public function preRewind(array $results, &$result_num) {
        if ($this->isParentSpecified) {
            $this->classCache = (string) $results[$result_num];
        }
    }

    public function rewind(array $results, &$result_num) {
        if ($this->isParentSpecified) {
            $results[$result_num] = $this->classCache;
        }
    }

    private function extendsFrom($class, $parent) {
        $lowerparent = strtolower($parent);
        if ($lowerparent == strtolower($class)) {
            return true;
        }
        foreach (array_merge(class_implements($class), class_parents($class)) as $test) {
            if ($lowerparent == strtolower($test)) {
                return true;
            }
        }
        return false;
    }

}
