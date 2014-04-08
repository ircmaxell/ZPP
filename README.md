A PHP Clone of `zend_parse_parameters()` in C
============================================

# How does it work?

It's quite simple really. You define the arguments that your function accepts in a "type string". Then you define the variables in an array. It does the rest converting errors into exceptions.

# What types does it support?

 * `z` - **Variable** - Accepts an arbitrary variable
 * `l` (lower-case L) - **Integers (Long)** 
 * `d` - **Floating-Point Numbers (Double)**
 * `s` - **Strings**
 * `p` - **Paths** (strings which disallow null bytes)
 * `b` - **Booleans**
 * `r` - **Resources**
 * `a` - **Arrays** - Just like `array` typehint
 * `A` - **Array-Like** - Accepts arrays **and** `ArrayAccess` objects
 * `i` - **Iterable** - Accepts arrays and `Traversable` objects, always returns an `Iterator`.
 * `f` - **Callable** - Accepts a callable variable (just like `callable` typehint)
 * `o` - **object** - Accepts an arbitrary object
 * `O` - **object** - Accepts an object specified by a parameter class/interface
 * `c` - **class** - Accepts an arbitrary class (not interface)
 * `C` - **class** - Accepts a class that decends from a specified class/interface

# What modifiers exist?

 * `!` - Makes the preceding type nullable (accepts the type or null).
 * `*` - Accepts 0 or more of the preceding type (varargs)
 * `+` - Accepts 1 or more of the preceding type (varargs)

# What other syntax structures exist?

 * `|` - makes everything after optional

# Basic Type Specification:

To specify that a function accepts a string and a boolean, you'd use the type specifier:

    sb

If it accepts a boolean, followed by one or more strings:

    bs+

If it accepts a string, followed by an optional boolean:

    s|b

If it accepts a string, followed by an optional boolean (nullable) and an optional array:

    s|b!a

Etc.

# Type Specification Limitations:

 * Only 1 vararg variable is allowed per function definition
 * Vararg types are not nullable

There are other limitations, but they are not defined at this time.

# Basic Usage:

You pass the parameter type spec to the first parameter, and an array of "results" to the second (note that the parameters must be by-reference).

    function bar() {
    	ZPP::parseParameters("sb", [
    		&$string,
    		&$boolean,
    	]);
    	// Your code here
    }

With varargs:

    function baz() {
    	ZPP::parseParameters("bs+", [
    		&$boolean,
    		&$strings, // <-- will be an array of all the varargs
    	]);
    	// Your code here
    }

# Specifying Classes (`C` type) And Objects (`O` type):

Let's say we want to accept a class that implements `Iterator` (Note, only classes will be accepted. Objects will be cast down to classes via `get_class()`):

    function biz() {
    	$class = "Iterator";
    	ZPP::parseParameters("C", [
    		&$class,
    	]);
    }

You basically "default" the variable to the type you want it to inherit from.

Let's say we want to accept an object that extends `PDO` (or `PDO` itself):

    function buz() {
    	$object = "PDO";
    	ZPP::parseParameters("O", [
    		&$object,
    	]);
    }

# Is the type system extensable?

Yes it is!

You can "register" a new type by calling `ZPP::registerType($specifier, $handler)` where `$specifier` is the single character type specifier and `$handler` is an instance of `ZPP\Handler`. Documentation is limited right now, so check out the existing Handlers for info on how to build them out.
