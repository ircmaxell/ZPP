<?php

namespace ZPP\Handler;

use ZPP\ZPP;
use ZPP\AbstractHandler;

class Iterable extends AbstractHandler {

    public function handle($arg, array $results, &$result_num, &$error) {
        switch (gettype($arg)) {
            case ZPP::IS_OBJECT:
                if (!$arg instanceof \Traversable) {
                    break;
                }
                if ($arg instanceof \IteratorAggregate) {
                    $arg = new \IteratorIterator($arg);
                }
                $results[$result_num] = $arg;
                return;
            case ZPP::IS_ARRAY:
                $results[$result_num] = new \ArrayIterator($arg);
                return;
            
        }
        return "iterable";
    }

}
