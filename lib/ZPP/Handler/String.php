<?php

namespace ZPP\Handler;

use ZPP\ZPP;
use ZPP\AbstractHandler;

class String extends AbstractHandler {

    protected $isPath = false;

    public function __construct($isPath = false) {
        $this->isPath = (bool) $isPath;
    }

    public function handle($arg, array $results, &$result_num, &$error) {
        switch (gettype($arg)) {
            case ZPP::IS_OBJECT:
                if (!is_callable([$arg, "__toString"])) {
                    break;
                }
                // Fall-through intentional
            case ZPP::IS_STRING:
            case ZPP::IS_DOUBLE:
            case ZPP::IS_NULL:
            case ZPP::IS_LONG:
            case ZPP::IS_BOOL:
                $arg = (string) $arg;
                if ($this->isPath && strpos($arg, "\0") !== false) {
                    break;
                }
                $results[$result_num] = $arg;
                return;
        }
        return $this->isPath ? "a valid path" : "string";
    }

}
