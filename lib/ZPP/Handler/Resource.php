<?php

namespace ZPP\Handler;

use ZPP\ZPP;
use ZPP\AbstractHandler;

class Resource extends AbstractHandler {

    public function handle($arg, array $results, &$result_num, &$error) {
        if (is_resource($arg)) {
            $results[$result_num] = $arg;
            return;
        }
        return "resource";
    }

}
