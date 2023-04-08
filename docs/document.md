# Document

This class is the foundation of Json-Works, allowing you to load, create, query, edit, validate and
output json data.

```php
<?php
namespace JohnStevenson\JsonWorks;

$document = new JohnStevenson\JsonWorks\Document();
```

### Contents
* [Overview](#overview)
* [Json Build and Query](#json-build-and-query)
* [Json Format](#json-format)
* [Load Data](#load-data)
* [Schema Validation](#schema-validation)

## Overview

### Paths and Pointers
In order to reference an element in a json structure [JSON Pointer][pointer] syntax is used, which
takes the form `/path/to/item`. Starting from the root of the data, each token in the path is
prefixed with a `/` and points to a matching property-name for objects or a numeric index for
arrays.

#### Forward-slashes in property names
Because the forward-slash is used as a delimiter, it must be *encoded* if it appears in a property
name. This is done by replacing it with `~1`. However, the tilde `~` may also be in the property
name so this must be encoded first, by replacing it with `~0`.

Json-Works has methods for encoding/decoding paths in the [Tokenizer class][tokenizer].

#### Pointers to array values
Note that a pointer to an array value is either a digit or sequence of digits (leading-zeros are not
allowed), or the special-case `/-` token, which indicates that a value should be added to the end.

#### Pointers to empty property keys
An empty `""` property key is valid (if somewhat unusual), and is represented by appending a `/`
to the element. For example, `prop1/` references the number 10 in `{ "prop": { "": 10 } }`.

### Input data
The data that you pass to methods that require it, or even the document itself, can either be an
object, an array or a class with accessible non-static properties. The data is always copied, which
will break any references and transform associative arrays to objects.

```php
<?php
# using an object ...
$data = new stdClass();
$data->firstName = 'Fred';
$data->lastName = 'Bloggs';

# or using an array ...
$data = ['firstName' => 'Fred', 'lastName' => 'Bloggs'];

# or using a class
$data = new PersonClass('Fred', 'Bloggs');

# Add the data to an existing document ...
$document->addValue($path, $data);

# or load it into a new document
$document->loadData($data);

# $document->getData() returns an unreferenced stdClass object
```

## Json Build and Query
These methods all take at least one *$path* parameter (see
[Paths And Pointers](#paths-and-pointers)) which can either be an array or a string.

* if it is an array, all items are treated as un-encoded and will be built into a single encoded
path.
* if it is a string, the value is treated as a single path that has already been encoded.

By using an array you can leave the encoding to Json-Works. In the example below we want to
reference a nested element named `with/slash`, which obviously needs encoding.

```php
<?php
# we can encode it ourselves
$tokenizer = new JohnStevenson\JsonWorks\Tokenizer()
$path = $tokenizer::add('prop1', 'with/slash');

# or we can let the library do it
$path = ['prop1', 'with/slash'];

$document->addValue($path, ...);
```

All methods, except *getValue()*, return a bool to indicate if they were successful. In failure
cases the error is in `$document->getError()`.

* [addValue()](#addvalue)
* [copyValue()](#copyvalue)
* [deleteValue()](#deletevalue)
* [getValue()](#getvalue)
* [hasValue()](#hasvalue)
* [moveValue()](#movevalue)


### addValue
bool **addValue** ( mixed `$path`, mixed `$value` )

Returns true if the value is added to *$path*. If *$path* does not exists, Json-Works will try and
create it, returning false if it is unable to do so with the error in `$document->getError()`.

If you wish to create objects that have array-like keys (digits or `-`) then you must create the
base object first (or it must already exist) otherwise an array will be created or an error will
occur.

```php
<?php
$document->addValue('prop1/0', 'myValue');
# creates an array at prop1: [ "myValue" ]

$document->addValue('prop1', new \stdClass());
$document->addValue('prop1/0', 'myValue');
# creates an object at prop1: { "0": "myValue" }
```

### copyValue
bool **copyValue** ( mixed `$fromPath`, mixed `$toPath` )

Returns true if the value at *$fromPath* is copied to *$toPath*.

### deleteValue
bool **deleteValue** ( mixed `$path` )

Returns true if the value at *$path* is deleted.

### getValue
mixed **getValue** ( mixed `$path`, [ mixed `$default` = null ] )

Returns the value found at *$path*, or *$default*.

### hasValue
bool **hasValue** ( mixed `$path`, mixed `&$value` )

Returns true if a value is found at *$path*, placing it in *$value*. Note that *$value* will be null
if the function returns false.

### moveValue
bool **moveValue** ( mixed `$fromPath`, mixed `$toPath` )

Returns true if the value at *$fromPath* is firstly copied to *$toPath*, then deleted from
*$fromPath*.

*Back to:* [Contents](#contents)

## Json Format
These methods format the document data. Json-Works does not call them automatically so their usage
is left to the discretion of the implementation. The [Formatter class][formatter] is used
internally.

* [tidy()](#tidy)
* [toJson()](#tojson)

### tidy
void **tidy** ( [ bool `$order` = false ] )

Removes empty objects and arrays from the data. If *$order* is true and a schema has been loaded the
function re-orders the data using the schema content.

### toJson
string **toJson** ( bool `$pretty` )

Returns a json-encoded string of `$document->data`. If *$pretty* is true, the output will be
pretty-printed.

*Back to:* [Contents](#contents)

## Load Data
These methods load either json data or a json schema. The [Loader class][loader] is used internally.
Input can either be:

* a json string, requiring a successful call to _json_decode_.
* a filename, requiring a successful call to _file_get_contents_ then _json_decode_.
* a PHP data type specific to the method.

* [loadData()](#loaddata)
* [loadSchema()](#loadschema)

### loadData
void **loadData** ( mixed `$data` )

Accepts most PHP data types because a JSON text is not technically restricted to objects and arrays.
On success the *$data* is copied and stored internally. It is accessible using
`$document->getData()`. Throws a *RuntimeException* if the data is a resource.

### loadSchema
void **loadSchema** ( mixed `$schema` )

Throws a `RuntimeException` if *$schema* does not result in a PHP object when processed.

*Back to:* [Contents](#contents)

## Schema Validation
Json-Works provides an implementation of JSON Schema version 4. Please read [Schema][schema] for
more details.

### validate
bool **validate** ()

Returns the result of validating the data against the loaded schema. If false is returned the error
will be in `$document->getError()`. If no schema has been loaded this method will always return
true.


```php
$document->loadData('path/to/data.json');
$document->loadScheme('path/to/schema.json');

if (!$document->validate()) {
    $error = $document->getError();
}
```

*Back to:* [Contents](#contents)

[pointer]: https://www.rfc-editor.org/rfc/rfc6901
[formatter]: formatter.md
[loader]: loader.md
[schema]: schema.md
[tokenizer]: tokenizer.md
