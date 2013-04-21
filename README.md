Json-Works
==========

[![Build Status](https://travis-ci.org/johnstevenson/json-works.png?branch=master)](https://travis-ci.org/johnstevenson/json-works)

A PHP library to create, edit, query and validate [JSON](http://www.json.org/).

## Contents
* [About](#About)
    * [Validation](#Validation)
    * [Features](#Features)
* [Installation](#Installation)
* [Usage](#Usage)
* [License](#License)

<a name="About"></a>
## About

The library is intended to be used with nested json structures, or with json data that needs validation. Or in any situation where you would find it is easier to do something like this:

```php
<?php
$document = new JohnStevenson\JsonWorks\Document();

$document->addValue('/path/to/nested/array/-', array('firstName'=> 'Fred', 'lastName' => 'Blogg'));

# prettyPrint
$json = $document->toJson(true);
```

which will give you the following json:

```
{
	"path": {
		"to": {
			"nested": {
				"array": [
					{"firstName": "Fred", "lastName": "Blogg"}
				]
			}
		}
	}
}
```

You can query this value by calling:

```php
$fred = $document->getValue('/path/to/nested/array/0');
```

and update it with:

```php
$document->addValue('/path/to/nested/array/0/lastName', 'Bloggs');
```
and move it with:

```php
$document->moveValue('/path/to/nested/array/0', '/users/-');

$document->tidy();
$json = $document->toJson(true);
```

to end up with:

```
{
	"users": [
		{"firstName": "Fred", "lastName": "Bloggs"}
	]
}
```

then delete it with:

```php
$document->deleteValue('/users/0');
```

The `/path/to/value` notation shown above is [JSON Pointer][pointer] syntax, which identifies specific json elements by following a path from the root of the document. Each token in the path is prefixed with a `/` and points to a matching property-name for objects or a numeric index for arrays. Arrays also use the special `/-` token, which indicates that a value should be added to the end of the array.

Json-Works builds json structures by using these references, but it will get it wrong if your object contains array-like property names: ```"3": {"name": "Bloggs", "age": 42}```. This is where validation can be useful.


<a name="Validation"></a>
### Validation

Json-Works includes an implementation of [JSON Schema][schema], version 4. This allows you to validate your data. The following example schema describes a simple structure comprising an object with a single property named `"users"`, which is an array containing objects whose properties are all required and whose types are defined. 

```
{
    "$schema": "http://json-schema.org/draft-04/schema#",
    "type": "object",
	"properties": {
		"users": {
			"type": "array",
			"items": {
                "properties": {
				    "firstName": {"type": "string"},
				    "lastName": {"type": "string"}				    
                },
                "required": ["firstName", "lastName"]
			}
		},
    "additionalProperties": false
	}
}
```
Now when you try to add values, Json-Works will only do so if they are valid. So you have to check.

```php
$document->loadSchema($schema);

$result = $document->addValue('/date', date('Y-m-d');
# false, no additionalProperties are allowed

$result = $document->addValue('/users/-', array('firstName'=> 'Fred', 'lastName' => 'Bloggs'));
# true

$result = $document->addValue('/users/-', array('firstName'=> 'Fred', 'lastName' => 3));
# false, lastName is not a string

$result = $document->addValue('/users/0', array('firstName'=> 'Fred'));
# true, required values are not checked when we are building

# but are checked if we validate directly
$result = $document->validate();
# false - required lastName is missing
```
Without a schema, any value can be added anywhere.

<a name="Features"></a>
### Features

The examples above show some useful features of the library:

* When you are adding values, Json-Works uses an unreferenced copy of your input. It copies all properties from objects and classes, as well as casting associative arrays to objects.

* You can use the ```tidy()``` method to remove empty objects and arrays.

In addition, if you call ```$document->tidy(true)```, Json-Works will re-order your data, using the order found in the schema. For example:

```
# data before
{
    "prop3": "value 3",
    "prop2": {},
    "prop1": "value 1"
}

# data after
{
    "prop1": "value 1",
    "prop3": "value 3"
}
```

<a name="Installation"></a>
## Installation
The easiest way is [through composer][composer]. Just create a `composer.json` file and run `php composer.phar install` to install it:

```
{
	"require": {
		"johnstevenson/json-works": "1.0.*"
	}
}
```

Then include `require 'vendor/autoload.php'` somewhere in your bootstrap code. Alternatively, you can [download][download] and extract it then point a PSR-0 autoloader to the `src` directory.

<a name="Usage"></a>
## Usage

Full usage [documentation][wiki] is currently being written and will be available in the Wiki.

<a name="License"></a>
## License

Json-Works is licensed under the MIT License - see the `LICENSE` file for details.

[pointer]: http://tools.ietf.org/html/rfc6901/
[schema]: http://json-schema.org/
[composer]: http://getcomposer.org
[download]: https://github.com/johnstevenson/json-works/archive/master.zip
[wiki]:https://github.com/johnstevenson/json-works/wiki/Home

