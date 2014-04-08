<?php

namespace ZPP;

abstract class ZPP {

    const IS_STRING = "string";
    const IS_BOOL = "boolean";
    const IS_LONG = "integer";
    const IS_DOUBLE = "double";
    const IS_ARRAY = "array";
    const IS_OBJECT = "object";
    const IS_RESOURCE = "resource";
    const IS_NULL = "NULL";

    protected static $types = [];

    public static function registerType($spec, Handler $handler) {
        static::$types[$spec] = $handler;
    }

    public static function parseParameters($type_spec = "", array $results = []) {
        $bt = debug_backtrace(0, 2);
        if (isset($bt[1]['args']) && is_array($bt[1]['args'])) {
            $arguments = $bt[1]['args'];
        } else {
            throw new \BadFunctionCallException("You must call ZPP from a function context, not globally");
        }
        if (strlen($type_spec) == 0 && !empty($arguments)) {
            static::generate_error("LogicException", "expects 0 parameters, %d given", count($arguments));
        } elseif (isset($type_spec[0]) && $type_spec[0] == '*' || $type_spec[0] == '+') {
            // handle case where var-args are only speced:
            $type_spec = 'z' . $type_spec;
        }
        $spec_len = strlen($type_spec);
        $num_args = count($arguments);
        $have_varargs = false;
        $max_num_args = 0;
        $min_num_args = -1;
        $post_varargs = -1;
        for ($i = 0; $i < $spec_len; $i++) {
            $c = $type_spec[$i];
            if (isset(static::$types[$c])) {
                $max_num_args++;
            } elseif ($c == "|") {
                $min_num_args = $max_num_args;
            } elseif (in_array($c, ["*", "+"])) {
                if ($c == "*") {
                    $max_num_args--;
                }
                if ($have_varargs) {
                    static::generate_error("LogicException", "only one varargs specifier (* or +) is permitted");
                }
                $have_varargs = true;
                $post_varargs = $max_num_args;
            } elseif (!in_array($c, ["/", "!"])) {
                static::generate_error("LogicException", "bad type specifier while parsing parameters %s", $c);
            }
        }

        if ($min_num_args < 0) {
            $min_num_args = $max_num_args;
        }

        if ($have_varargs) {
            $post_varargs = $max_num_args - $post_varargs;
            $max_num_args = -1;
        }

        if ($num_args < $min_num_args || ($num_args > $max_num_args && $max_num_args > 0)) {
            static::generate_error(
                "LogicException",
                "expects %s %d parameter%s, %d given", 
                $min_num_args == $max_num_args ? "exactly" : ($num_args < $min_num_args ? "at least" : "at most"),
                $num_args < $min_num_args ? $min_num_args : $max_num_args,
                ($num_args < $min_num_args ? $min_num_args : $max_num_args) == 1 ? "" : "s",
                $num_args
            );
        }

        $type_key = 0;
        $result_num = 0;

        for ($i = 0; $i < $num_args; $i++, $result_num++) {
            if ($type_spec[$type_key] == '|') {
                $type_key++;
            }
            if ($type_key + 1 < $spec_len && ($type_spec[$type_key + 1] == '*' || $type_spec[$type_key + 1] == '+')) {
                $num_varargs = $num_args - $i - $post_varargs;
                if ($num_varargs > 0) {
                    $var_arg_result = [];
                    $start_type_key = $type_key;
                    $end_type_key = $type_key;
                    $handler = static::$types[$type_spec[$type_key]];

                    while ($num_varargs > 0) {
                        $handler->preRewind($results, $result_num);

                        static::parse_arg($i, $result_num, $arguments, $type_spec, $type_key, $results);

                        $var_arg_result[] = $results[$result_num];
                        $i++;
                        $end_type_key = $type_key;
                        $type_key = $start_type_key;
                        $num_varargs--;

                        $handler->rewind($results, $result_num);
                    }

                    $results[$result_num] = $var_arg_result;
                    // subtract 1 from the arg position, to count for the duplicate shift:
                    $i--;
                    $type_key = $end_type_key + 1;
                    continue;
                } else {
                    $type_key++;
                }
            }
            
            static::parse_arg($i, $result_num, $arguments, $type_spec, $type_key, $results);
        }
    }

    private static function parse_arg($arg_num, &$result_num, array $arguments, $type_spec, &$type_key, array &$results) {
        $severity = E_USER_WARNING;
        $error = null;
        $type = static::parse_arg_impl($arg_num, $result_num, $arguments, $type_spec, $type_key, $results, $severity, $error);
        if ($type) {
            if ($error) {
                static::generate_error("RuntimeException", "expects parameter %d %s", $arg_num, $error);
            } else {
                static::generate_error("RuntimeException", "expects parameter %d to be %s, %s given", $arg_num, $type, gettype($arguments[$arg_num]));
            }
        }
    }

    private static function parse_arg_impl($arg_num, &$result_num, array $arguments, $type_spec, &$type_key, array &$results, &$severity, &$error) {
        $by_ref = false;
        $walk = $type_key + 1;
        $nullable = false;
        while (isset($type_spec[$walk])) {
            if ($type_spec[$walk] == '/') {
                // Todo: Implement this
            } elseif ($type_spec[$walk] == "!") {
                $nullable = true;
            } else {
                break;
            }
            $walk++;
        }

        $c = $type_spec[$type_key];
        $arg = $arguments[$arg_num];
        if (!isset(static::$types[$c])) {
            return "unknown";
        }
        
        $handler = static::$types[$c];
        if ($nullable && is_null($arg)) {
            $ret = $handler->handleNull($results, $result_num, $error);
        } else {
            $ret = $handler->handle($arg, $results, $result_num, $error);
        }
        if ($ret) {
            return $ret;
        }

        $type_key = $walk;
    }

    private static function generate_error($class, $message) {
        $bt = debug_backtrace();
        $callerIsNext = false;
        $caller = PHP_INT_MAX;
        for ($stackCount = 0; $stackCount < count($bt); $stackCount++) {
            if ($callerIsNext) {
                $caller = $stackCount;
                break;
            } elseif ($bt[$stackCount]["file"] != __FILE__) {
                $callerIsNext = true;
            }
        }
        if (!isset($bt[$caller])) {
            // We have a stack error, bail
            throw new \RuntimeException("Invalid stack, something went wrong here");
        }
        if (func_num_args() > 2) {
            $message = vsprintf($message, array_slice(func_get_args(), 2));
        }
        $tmp = sprintf(
            "%s%s%s(): %s in %s on line %d", 
            isset($bt[$caller]['class']) ? $bt[$caller]['class'] : "", 
            isset($bt[$caller]['type']) ? $bt[$caller]['type'] : "", 
            isset($bt[$caller]['function']) ? $bt[$caller]['function'] : "", 
            $message, 
            isset($bt[$caller]['file']) ? $bt[$caller]['file'] : "", 
            isset($bt[$caller]['line']) ? $bt[$caller]['line'] : ""
        );
        throw new $class($tmp);   
    }
}
