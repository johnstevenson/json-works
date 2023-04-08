### Contents
* [Overview](#overview)
* [Data functions](#data-functions)
  * [dataCopy()](#data-copy)
  * [dataOrder()](#dataorder)
  * [dataPrune()](#dataprune)
  * [dataToJson()](#datatojson)
* [Path functions](#path-functions)
  * [pathAdd()](#pathadd)
  * [pathDecode()](#pathdecode)
  * [pathEncode()](#pathencode)
  * [pathEncodeKey()](#pathencodekey)
* [Utility functions](#utility-functions)
  * [checkType()](#checktype)
  * [equals()](#equals)
  * [get()](#get)
  * [uniqueArray()](#uniqueArray)


# Overview
The static Utils class contains functions used internally by the library. Some of these can be useful when you are using Json-Works, most notably the [Path functions][path], so they are all listed in full. Since it is a static class, usage is:

```php
<?php
namespace JohnStevenson\JsonWorks\Utils

Utils::method($params);
```

# Data functions
Apart for *dataToJson()*, these functions take an object or array and return an unreferenced copy with associative arrays converted to objects.

* [dataCopy()](#datacopy)
* [dataOrder()](#dataorder)
* [dataPrune()](#dataprune)
* [dataToJson()](#datatojson)

## dataCopy
mixed **dataCopy** ( mixed `$data`, [ callback `$callback` ] )

Returns an unreferenced copy of *$data*, having applied an optional *$callback* function.

```php
<?php
$data = array('firstName' => 'Fred', 'lastName' => 'Bloggs');

$result = Utils::dataCopy($data);

$fred = $result->firstName;
$bloggs = $result->lastName;
```

If a callback function is used it will be passed a single parameter ( mixed `$data` ) and it must **return** either this or a new value.

*Back to:* [Data functions](#data-functions) / [Contents](#contents)

## dataOrder
mixed **dataOrder** ( mixed `$data`, stdClass `$schema` )

Returns an unreferenced copy of *$data*, with object properties re-ordered using the order found in *$schema*. This is illustrated in the example below, which uses json-notation for PHP objects.

```
# schema
{
  "type": "object",
  "properties": {
    "prop1": {},
    "prop2": {},
    "prop3": {}
  }
}

# data before
{
  "prop3": "value 3",
  "prop2": {},
  "prop1": "value 1"
}

# data after
{
  "prop1": "value 1",
  "prop2": {},
  "prop3": "value 3"
}
```

Note that the ordering is fairly simplistic. Only the *properties* and *items* keywords are searched in the schema, and only the property names listed are ordered. This means that property names appearing within an *anyOf* schema, for example, will not be discovered or ordered. Any property names not discovered or listed in the schema will be positioned after any ordered elements.

*Back to:* [Data functions](#data-functions) / [Contents](#contents)

## dataPrune
mixed **dataPrune** ( mixed `$data` )

Returns an unreferenced copy of *$data*, having removed any empty object properties or arrays. This is illustrated in the example below, which uses json-notation for PHP objects.

```
# data before
{
  "prop1": "value 1",
  "prop2": {},
  "prop3": "value 3"
  "prop4": [],
  "prop5": 5
}

# data after
{
  "prop1": "value 1",
  "prop3": "value 3",
  "prop5": 5
}
```

*Back to:* [Data functions](#data-functions) / [Contents](#contents)

## dataToJson
mixed **dataToJson** ( mixed `$data`, boolean `$pretty` )

Returns a json-encoded string of *$data*. Forward-slashes are not escaped and UTF-8 characters are not encoded. If *$pretty* is true, the output will be *pretty-printed*. If the PHP version is 5.4+ then this function is equivalent to (and calls) `json_encode` with the following options:
* JSON_UNESCAPED_SLASHES
* JSON_UNESCAPED_UNICODE
* JSON_PRETTY_PRINT`

*Back to:* [Data functions](#data-functions) / [Contents](#contents)

# Path functions
These function are used for creating [JSON Pointers][pointer] to reference the elements within the json data.

* [pathAdd()](#pathadd)
* [pathDecode()](#pathdecode)
* [pathEncode()](#pathencode)
* [pathEncodeKey()](#pathencodekey)

## pathAdd
string **pathAdd** ( string `$path`, string `$key` )

Returns a new JSON Pointer by concatenating *$path* with `/` plus the encoded value of *$key*. The function is useful for building paths. Note that it will not alter *$path* if passed an empty *$key*. Uses [pathEncodeKey()](#pathencodekey) internally.

```php
<?php
$result = Utils::pathAdd('', 'keyname');
# /keyname

$result = Utils::pathAdd('/prop1', 'name/with/slash');
# /prop1/name~1with~1slash

$result = Utils::pathAdd('/prop1', 'name~with~tilde');
# /prop1/name~0with~0tilde

$result = Utils::pathAdd('/prop1/prop2', '');
# /prop1/prop2
```

*Back to:* [Path functions](#path-functions) / [Contents](#contents)

## pathDecode
array **pathDecode** ( string `$path` )

Returns an array of decoded elements from an encoded JSON Pointer *$path*. Each element is decoded by replacing all `~1` sequences with a forward-slash, then replacing all `~0` sequences with a tilde.

```php
<?php
$result = Utils::pathDecode('/keyname');
# array('keyname')

$result = Utils::pathDecode('/prop1/name~1with~1slash');
# array('prop1', 'name/with/slash')

$result = Utils::pathDecode('/prop1/name~0with~0tilde');
# array('prop1', 'name~with~tilde')

$result = Utils::pathDecode('');
# array()
```

*Back to:* [Path functions](#path-functions) / [Contents](#contents)

## pathEncode
string **pathEncode** ( string | array `$path` )

Returns an encoded JSON Pointer from *$path*, which must either be a single string element, or an array of path elements. Uses [pathAdd()](#pathadd) internally.

```php
<?php
$result = Utils::pathEncode('keyname');
# /keyname

$result = Utils::pathEncode(array('prop1', 'prop2', 'name/with/slash'));
# /prop1/prop2/name~1with~1slash
```

*Back to:* [Path functions](#path-functions) / [Contents](#contents)

## pathEncodeKey
string **pathEncodeKey** ( string `$key` )

Encodes and returns *$key*. All tilde characters are replaced with `~0`, then all forward-slashes are replaced with `~1`.

```php
<?php
$result = Utils::pathEncodeKey('keyname');
# keyname

$result = Utils::pathEncodeKey('name/with/slash');
# name~1with~1slash

$result = Utils::pathEncodeKey('name~with~tilde');
# name~0with~0tilde
```

*Back to:* [Path functions](#path-functions) / [Contents](#contents)


# Utility functions
General purpose functions.

* [checkType()](#checktype)
* [equals()](#equals)
* [get()](#get)
* [uniqueArray()](#uniquearray)

## checkType
boolean **checkType** ( string `$type`, mixed `$value` )

Checks that *$value* is of the type specified by *$type*. Returns *true* or *false*. All PHP types are supported plus the additional type of `number` which returns true for floats and integers. To test for a float value *$type* must be `float` and not `double`.

*Back to:* [Utility functions](#utility-functions) / [Contents](#contents)

## equals
boolean **equals** ( mixed `$var1`,  mixed `$var2` )

Returns true if *$var1* equals *$var2* based on the following JSON Schema definition of equality:

* both are nulls; or
* both are booleans, and have the same value; or
* both are strings, and have the same value; or
* both are numbers, and have the same mathematical value; or
* both are arrays, and:
  * have the same number of items; and
  * items at the same index are equal according to this definition; or
* both are objects, and:
  * have the same set of property names; and
  * values for a same property name are equal according to this definition.

*Back to:* [Utility functions](#utility-functions) / [Contents](#contents)

## get
mixed **get** ( mixed `$container`, string `$key`, [ mixed `$default` = null ] )

Returns the object/array value referenced by *$key*, or *$default* if either the key is not found or the *$container* is not an object/array.

```php
<?php
$data = (object) array('firstName' => 'Fred', 'lastName' => 'Bloggs'));

$result = Utils::get($data, 'firstName');
# Fred

$result = Utils::get($data, 'surname');
# null

$result = Utils::get($data, 'surname', 'Bloggs');
# Bloggs

$array = array(1, 2, 3);
$result = Utils::get($array, 2);
# 3

$scalar = 9;
$result = Utils::get($scalar, 'firstName');
# null
```

*Back to:* [Utility functions](#utility-functions) / [Contents](#contents)

## uniqueArray
mixed **uniqueArray** ( array `$data`, [ boolean `$check` = false ] )

Returns either a copy of *$data* with duplicate values removed or, when *$check* is true, a boolean indicating that all values in the array are unique. Unlike PHP's `array_unique()`, this function works with nested object values.

*Back to:* [Utility functions](#utility-functions) / [Contents](#contents)

[pointer]: http://tools.ietf.org/html/rfc6901/
