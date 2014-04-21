<?php

namespace ZPP;

interface Handler {

    public function handle($arg, array $results, &$result_num, &$error);
    public function handleNull(array $results, &$result_num, &$error);

    public function preRewind(array $results, &$result_num);
    public function rewind(array $results, &$result_num);

}
