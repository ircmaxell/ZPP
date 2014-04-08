<?php

namespace ZPP\Handler;

use ZPP\ZPP;
use ZPP\AbstractHandler;

class ArrayHandler extends AbstractHandler {

    protected $acceptsArrayLike = false;

    public function __construct($acceptsArrayLike = false) {
        $this->acceptsArrayLike = (bool) $acceptsArrayLike;
    }

    public function handle($arg, array $results, &$result_num, &$error) {
        switch (gettype($arg)) {
            case ZPP::IS_OBJECT:
                if (!($this->acceptsArrayLike && $arg instanceof \ArrayAccess)) {
                    break;
                }
            case ZPP::IS_ARRAY:
                $results[$result_num] = $arg;
                return;
            
        }
        return $this->acceptsArrayLike ? "array or object implementing ArrayAccess" : "array";
    }

}
