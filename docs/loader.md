# Loader

This class provides methods for processing input data.


```php
<?php
namespace JohnStevenson\JsonWorks;

$loader = new Loader();
```

## Methods

* [getData()](#getdata)
* [getSchema()](#getschema)

### getData
mixed **getData** ( mixed `$data` )

The `$data` can be:
* a json string, passed to _json_decode_.
* a .json filename, passed to _file_get_contents_ then _json_decode_.
* a PHP object, class, array or scalar.

Returns a _stdClass_ object containing the dereferenced data, or the scalar value. Throws a
*RuntimeException* if the data is a resource.

### getSchema
void **getSchema** ( mixed `$schema` )

The `$schema` can be:
* a json string, passed to _json_decode_.
* a .json filename, passed to _file_get_contents_ then _json_decode_.
* a PHP object (or class or associative array).

Returns a _stdClass_ object containing the dereferenced data. Throws a *RuntimeException* on
failure.
