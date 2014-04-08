<?php

namespace ZPP;

abstract class AbstractHandler implements Handler {
 
    public function handleNull(array $results, &$result_num, &$error) {
        $results[$result_num] = null;
    }

    public function preRewind(array $results, &$result_num) {
    }

    public function rewind(array $results, &$result_num) {
    }

}
