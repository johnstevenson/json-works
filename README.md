Json-Works
==========

[![Build Status](https://travis-ci.org/johnstevenson/json-works.png?branch=master)](https://travis-ci.org/johnstevenson/json-works)

A PHP library to create, edit, query and validate [JSON](http://www.json.org/).

## Contents
* [About](#About)
* [Installation](#Installation)
* [Usage](#Usage)
* [License](#License)

<a name="About"></a>
## About

The library is intended to be used with nested json structures, or with json data that needs validation. Or in any situation where it is easier to do something like this:

```php
<?php
$document = new JohnStevenson\JsonWorks\Document();

$document->addValue('/path/to/nested/array/-', array('firstName'=> 'Fred', 'lastName' => 'Blogg'));

# prettyPrint
$json = $document->toJson(true);
```

which will give us the following json:

```json
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

We can get to this value by calling:

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

```json
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

Json-Works builds json structures by using these references, but it can sometimes get it wrong. What if our object contains numeric property names: `"3": {"name": "Bloggs", "age": 42}`? What if we need to check that the value of `"age"` is always a number? This is where validation can be useful.



### Validation

Json-Works includes an implementation of [JSON Schema][schema], version 4. This allows us to validate our data and can also help Json-Works build the document. The following example schema describes a simple structure comprising an object with a single property named `"users"`, which is an array containing objects whose properties are all required and whose types are defined. 

```json
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
Now when we try to add values, Json-Works will only do so if they are valid. So we have to check.

```php
$result = $document->addValue('/date', date('Y-m-d');
# false, no additionalProperties are allowed

$result = $document->addValue('/users/-', array('firstName'=> 'Fred', 'lastName' => 'Bloggs'));
# true

$result = $document->addValue('/users/-', array('firstName'=> 'Fred', 'lastName' => 3));
# false, lastName is not a string

$result = $document->addValue('/users/0', array('firstName'=> 'Fred'));
# true, required values are not checked when we are building

# but are if we validate directly
$result = $document->validate();
# false - lastName is missing
```
Without a schema, any value can be added anywhere.


<a name="Installation"></a>
## Installation
The easiest way is [through composer][composer]. Just create a `composer.json` file and run `php composer.phar install` to install it:

```json
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

