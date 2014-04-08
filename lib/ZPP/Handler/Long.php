<?php

namespace ZPP\Handler;

use ZPP\ZPP;
use ZPP\AbstractHandler;

class Long extends AbstractHandler {

    public function handle($arg, array $results, &$result_num, &$error) {
        switch (gettype($arg)) {
            case ZPP::IS_STRING:
                if (!is_numeric($arg)) {
                    return "long";
                }
                // Necessary to keep overflow behavior
                $arg = (double) $arg;
                // Fall through intentional
            case ZPP::IS_DOUBLE:
                // Thanks to HHVM, we must do overflow check ourselves
                if ($arg > PHP_INT_MAX) {
                    $arg = PHP_INT_MAX;
                } elseif ($arg < ~PHP_INT_MAX) {
                    $arg = ~PHP_INT_MAX;
                }
            case ZPP::IS_NULL:
            case ZPP::IS_LONG:
            case ZPP::IS_BOOL:
                $results[$result_num] = (int) $arg;
                return;
            
        }
        return "long";
    }

}
