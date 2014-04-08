<?php

namespace ZPP\Handler;

use ZPP\ZPP;
use ZPP\AbstractHandler;

class Boolean extends AbstractHandler {

    public function handle($arg, array $results, &$result_num, &$error) {
        switch (gettype($arg)) {
            case ZPP::IS_STRING:
            case ZPP::IS_DOUBLE:
            case ZPP::IS_NULL:
            case ZPP::IS_LONG:
            case ZPP::IS_BOOL:
                $results[$result_num] = (boolean) $arg;
                return;
            
        }
        return "boolean";
    }

}
