<?php

namespace ZPP\Handler;

use ZPP\ZPP;
use ZPP\AbstractHandler;

class Callback extends AbstractHandler {

    public function handle($arg, array $results, &$result_num, &$error) {
        if (!is_callable($arg)) {
            return "valid callback";
        }
        $results[$result_num] = $arg;
    }

}
