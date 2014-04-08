<?php

namespace ZPP\Handler;

use ZPP\ZPP;
use ZPP\AbstractHandler;

class Variable extends AbstractHandler {

    public function handle($arg, array $results, &$result_num, &$error) {
        $results[$result_num] = $arg;
    }

}
