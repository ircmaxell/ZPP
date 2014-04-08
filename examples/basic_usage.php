<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use ZPP\ZPP;

/**
 * No Parameters
 */
function bar() {
    // Calling it without parameters, means the function will accept no parameters
    ZPP::parseParameters();
}

bar(); // Works fine
try {
    bar(1, 2);
} catch (\LogicException $e) {
    // bar(): expects 0 parameters, 2 given
}

/**
 * 3 Static Parameters
 */
function foo() {
    ZPP::parseParameters("bls", [
        &$bool,
        &$integer,
        &$string
    ]);
}

try {
    foo();
} catch (\LogicException $e) {
    // foo(): expects 3 paramters, 0 given
}

try {
    foo(true, 1, array(1));
} catch (\RuntimeException $e) {
    // foo(): expects parameter 2 to be string, array given
}

foo(1.5, 2.5, 3.5); // Works fine, because all 3 are cleanly castable to the respective types

/**
 * 2 static, one optional parameter
 */
function baz() {
    ZPP::parseParameters("bl|s", [
        &$bool,
        &$integer,
        &$string,
    ]);
}

baz(1, 2); // works, string is null
baz(1, 2, 3); // works, string is "3"

/**
 * Nullable Parameter
 */
function nullable() {
    ZPP::parseParameters("s!", [
        &$string,
    ]);
}
try {
    nullable();
} catch (\LogicException $e) {
    // It's still required
}
nullable(null); // but it can be null in addition to the type

/**
 * Varargs
 */
function varargs() {
    ZPP::parseParameters("s+", [
        &$args
    ]);
}
try {
    varargs();
} catch (\LogicException $e) {
    // We still require at least one arg due to the "+"
}
varargs("a", "b", "c"); // args is ["a", "b", "c"]

/**
 * Varargs with prefix AND suffix
 */
function varargsPrefixed() {
    ZPP::parseParameters("bs+b", [
        $bool1,
        $strings,
        $bool2,
    ]);
}
varargsPrefixed(true, "a", "b", false); // true, ["a", "b"], false

