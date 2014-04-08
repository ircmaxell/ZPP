<?php

namespace ZPP\Handler;

use ZPP\ZPP;
use ZPP\AbstractHandler;

class Object extends AbstractHandler {
    protected $isClassSpecified = false;
    protected $classCache;

    public function __construct($isClassSpecified = false) {
        $this->isClassSpecified = (bool) $isClassSpecified;
    }

    public function handle($arg, array $results, &$result_num, &$error) {
        if ($this->isClassSpecified) {
            $class = $results[$result_num];
            if (is_object($class)) {
                $class = get_class($class);
            }
            if (!class_exists($class) && !interface_exists($class)) {
                throw new \LogicException("Provided class does not exist");
            }
        }
        switch (gettype($arg)) {
            case ZPP::IS_OBJECT:
                if ($this->isClassSpecified && !$arg instanceof $class) {
                    break;
                }
                $results[$result_num] = $arg;
                return;
        }
        return $this->isClassSpecified ? $class : "object";
    }

    public function handleNull(array $results, &$result_num, &$error) {
        if ($this->isClassSpecified) {
            $result_num++;
        }
        return parent::handleNull($results, $result_num, $error);
    }

    public function preRewind(array $results, &$result_num) {
        if ($this->isClassSpecified) {
            $this->classCache = (string) $results[$result_num];
        }
    }

    public function rewind(array $results, &$result_num) {
        if ($this->isClassSpecified) {
            $results[$result_num] = $this->classCache;
        }
    }


}
