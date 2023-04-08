### Contents
* [Overview](#overview)
* [Json Building and Query functions](#json-building-and-query-functions)
  * [addValue()](#addvalue)
  * [copyValue()](#copyvalue)
  * [deleteValue()](#deletevalue)
  * [getValue()](#getvalue)
  * [hasValue()](#hasvalue)
  * [moveValue()](#movevalue)
* [Json Format functions](#json-format-functions)
  * [tidy()](#tidy)
  * [toJson()](#tojson)
* [Load Data functions](#load-data-functions)
  * [loadData()](#loaddata)
  * [loadSchema()](#loadschema)
* [Schema Validation](#schema-validation)
  * [validate()](#validate)

# Overview
The Document class is the foundation of Json-Works, allowing you to load, create, query, edit, validate and output json data. All examples on this page refer to `$document`, as instantiated here:

```php
<?php
$document = new JohnStevenson\JsonWorks\Document();
```

### Paths and Pointers
In order to reference an element in a json structure we use [JSON Pointer][pointer] syntax, which takes the form `/path/to/item`. Starting from the root of the data, each token in the path is prefixed with a `/` and points to a matching property-name for objects or a numeric index for arrays.

##### Forward-slashes in property names
Because the forward-slash is used as a delimiter, it must be *encoded* if it appears in a property name. This is done by replacing it with `~1`. However, the tilde `~` may also be in the property name so this must be encoded first, by replacing it with `~0`.

Json-Works has functions for encoding/decoding paths in the [Utils class][utilsClass] (see the [Path functions][pathFunctions]).

##### Pointers to array values
Note that a pointer to an array value is either a digit or sequence of digits (leading-zeros are not allowed), or the special-case `/-` token, which indicates that a value should be added to the end.

### Input data
The data that you pass to functions that require it, or even the document itself, can either be an object, an array or a class with accessible properties. The data is always copied, using [Utils::dataCopy()][dataCopy], which will break any references and transform associative arrays to objects.

```php
<?php
# using an object ...
$data = new stdClass();
$data->firstName = 'Fred';
$data->lastName = 'Bloggs';

# or using an associative array ...
$data = array('firstName' => 'Fred', 'lastName' => 'Bloggs');

# or using a class ...
$data = new PersonClass('Fred', 'Bloggs');

# now add the data ...
$document->addValue($path, $data);
# the data in the document is now an unreferenced stdClass object

# or load it into the document
$document->loadData($data);
# $document->data is now an unreferenced stdClass object
```

# Json Building and Query functions
These functions all take at least one *$path* parameter (see [Paths And Pointers][path]) which can either be an array or a string.

* if it is an array, all items are treated as un-encoded and will be built into a single encoded path.
* if it is a string, the value is treated as a single path that has already been encoded.

By using an array you can leave the encoding to Json-Works. In the example below we want to reference a nested element named `with/slash`, which obviously needs encoding.

```php
<?php
# we can encode it ourselves
$path = Utils::pathAdd('prop1', 'with/slash');

# or we can let the library do it
$path = array('prop1', 'with/slash');

$document->addValue($path, ...);
```

All functions, except *getValue()*, return a boolean to indicate if they were successful. In failure cases the error is usually accessible from the `document->lastError` property.

* [addValue()](#addvalue)
* [copyValue()](#copyvalue)
* [deleteValue()](#deletevalue)
* [getValue()](#getvalue)
* [hasValue()](#hasvalue)
* [moveValue()](#movevalue)


## addValue
boolean **addValue** ( mixed `$path`, mixed `$value` )

Returns true if the value is added to *$path*. If *$path* does not exists, Json-Works will try and create it, returning false if it is unable to do so with the error in `$document->lastError`.

If you wish to create objects that have array-like keys (digits or `-`) then you must provide a schema defining the property otherwise an array will be created or an error will occur.

```php
<?php
$document->addValue('prop1/0', 'myValue');
# success, an array is created at prop1: {"prop1": [ "myValue" ] }

$document->addValue('prop2/123', 'myValue');
# fails because the next index of the array created at prop2 is 0, not 123
```

The function will also return false if you are using a schema and the data you are adding does not validate. The following example schema describes a simple structure which is an array containing objects whose properties are all required and whose types are defined.

```json
{
    "items": {
        "properties": {
            "firstName": {"type": "string"},
            "lastName": {"type": "string"}
        },
        "required": ["firstName", "lastName"]
    }
}
```
Now when you try to add values, Json-Works will only do so if they are valid. So you have to check.

```php
<?php
$document->loadSchema($schema);

$result = $document->addValue('/-', array('firstName'=> 'Fred', 'lastName' => 'Bloggs'));
# true

$result = $document->addValue('/-', array('firstName'=> 'Fred', 'lastName' => 3));
# false, lastName is not a string

$result = $document->addValue('/0', array('firstName'=> 'Fred'));
# true, required values are not checked when we are building

# but are checked if we validate directly
$result = $document->validate();
# false - required lastName is missing
```

*Back to:* [Json Building functions](#json-building-and-query-functions) / [Contents](#contents)

## copyValue
boolean **copyValue** ( mixed `$fromPath`, mixed `$toPath` )

Returns true if the value at *$fromPath* is copied to *$toPath*.

*Back to:* [Json Building functions](#json-building-and-query-functions) / [Contents](#contents)

## deleteValue
boolean **deleteValue** ( mixed `$path` )

Returns true if the value at *$path* is deleted.

*Back to:* [Json Building functions](#json-building-and-query-functions) / [Contents](#contents)

## getValue
mixed **getValue** ( mixed `$path`, [ mixed `$default` = null ] )

Returns the value found at *$path*, or *$default*.

*Back to:* [Json Building functions](#json-building-and-query-functions) / [Contents](#contents)

## hasValue
boolean **hasValue** ( mixed `$path`, mixed `&$value` )

Returns true if a value is found at *$path*, placing it in *$value*. Note that *$value* will be null if the functions returns false.

*Back to:* [Json Building functions](#json-building-and-query-functions) / [Contents](#contents)

## moveValue
boolean **moveValue** ( mixed `$fromPath`, mixed `$toPath` )

Returns true if the value at *$fromPath* is firstly copied to *$toPath*, then deleted from *$fromPath*.

*Back to:* [Json Building functions](#json-building-and-query-functions) / [Contents](#contents)

# Json Format functions
These function format `$document->data`. Json-Works does not call them automatically so their usage is left to the discretion of the implementation.

* [tidy()](#tidy)
* [toJson()](#tojson)

## tidy
void **tidy** ( [ boolean `$order` = false ] )

Removes empty objects and arrays from `$document->data` by passing it to [Utils::dataPrune()][dataPrune]. If *$order* is true and a schema has been loaded the function calls [Utils::dataOrder()][dataOrder] to re-order the data using the schema content.

*Back to:* [Json Format functions](#json-format-functions) / [Contents](#contents)

## toJson
string **toJson** ( boolean `$pretty`, [ boolean `$tabs` = false ] )

Returns a json-encoded string of `$document->data` by passing it to [Utils::dataToJson()][dataToJson]. If *$pretty* is true, the output will be pretty-printed. If *$tabs* is true, pretty-printed indentation is converted to tabs. Note that *$tabs* has no effect unless *$pretty* is true.

*Back to:* [Json Format functions](#json-format-functions) / [Contents](#contents)

# Load Data functions
These functions load either json data or a json schema. The first parameter passed to the functions can either be:

* a json string, requiring a successful call to `json_decode`.
* a filename, requiring a successful call to `file_get_contents` then `json_decode`.
* a PHP object or, for json data, a class, array or null.

If these conditions are not satisfied, a `RuntimeException` will be thrown. Alternatively, if the optional `$noException` parameter is passed the specific function will return false, with the error available in the `$document->lastError` property.

* [loadData()](#loaddata)
* [loadSchema()](#loadschema)

## loadData
boolean **loadData** ( mixed `$data`, [ boolean `$noException` = false ] )

Fails if *$data* does not result in a PHP object, class, array or null, as described in [Load Data functions][load]. On success the *$data* is copied and stored in the `$document->data` property. See [Input data][input] for more details.

*Back to:* [Load Data functions](#load-data-functions) / [Contents](#contents)

## loadSchema
boolean **loadSchema** ( mixed `$schema`, [ boolean `$noException` = false ] )

Fails if *$schema* does not result in a PHP object, as described in [Load Data functions][load], or if the schema contains any inline references that cannot be resolved (see [Schema][schema] for more details). On success the schema is stored as a `JohnStevenson\JsonWorks\Schema\Model` in the `$document->schema` property.

*Back to:* [Load Data functions](#load-data-functions) / [Contents](#contents)

# Schema Validation
The Json-Works implementation of JSON Schema differs slightly from the specification. Please read [Schema][schema] for more details. To use a schema you must load it first using [loadSchema()](#loadschema), which will fail if there are any inline reference errors.

To validate your data against a schema you call the [validate()](#validate) function. Json-Works does not do this automatically (except when it is building data) so its usage is left to the discretion of the implementation.

## validate
boolean **validate** ( [ boolean `$lax` = false ] )

Returns the result of validating the data against the loaded schema. If false is returned the error will be in the `$document->lastError` property. If no schema has been loaded this function will always return true.

If *$lax* is true, then the validator will not check the following keywords:

* minProperties - for objects
* required - for objects
* minItems - for arrays

Json-Works calls the function internally like this when it is [building](#json-building-and-query-functions) data.

*Back to:* [Load Data functions](#load-data-functions) / [Contents](#contents)

[pointer]: http://tools.ietf.org/html/rfc6901/
[schema]: schema.md
[dataCopy]: utils.md#datacopy
[dataOrder]: utils.md#dataorder
[dataPrune]: utils.md#dataprune
[dataToJson]: utils.md#datatojson
[utilsClass]: utils.md
[pathFunctions]: utils.md#path-functions
