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

The library is intended to be used with deeply-nested json structures. Or any place where it easier to do something like this:

```php
<?php
$document = new JohnStevenson\JsonWorks\Document();

$document->addValue('/path/to/nested/array/-', array('firstName'=> 'Fred', 'lastName' => 'Blogg'));
$json = $document->toJson(true);
```

which will give you the following json:

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

You can get to this value by calling:

```php
$fred = $document->getValue('/path/to/nested/array/0');
```

or update it with:

```php
$document->addValue('/path/to/nested/array/0/lastName', 'Bloggs');
```
or move it with:

```php
$document->moveValue('/path/to/nested/array/0', '/users/-');
$json = $document->toJson(true);
```

to get:

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

The `/path/to/value` notation shown above is [JSON Pointer][pointer] syntax, which identifies specific json elements by following a path from the root of the document. Each token is prefixed with a `/` and references a matching property name for objects or an index for arrays, for example `/3`. Arrays also use the special `/-` token, which points to the non-existent member after the last item and indicates that the value should be added.

Json-Works can build json structures by using these references, but it can sometimes get it wrong. What if your object contains numeric property names: `"3": {"name": "Bloggs"}`? What if you need to check that the value of `"name"` is always a string? This is where validation can be useful.



### Validation

```json
{
	"properties": {
		"users": {
			"type": "array",
			"items": {
				"firstName": {"type": "string"},
				"lastName": {"type": "string"},
				"required": ["firstName", "lastName"]
			}
		}
	}
}
```


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

Then include `require 'vendor/autoload.php'` somewhere in your bootstrap code. Alternatively, you can [download][download] and extract it (or clone this repo) and point a PSR-0 autoloader to the `src` directory.

<a name="Usage"></a>
## Usage

Full usage [documentation][wiki] can be found in the Wiki.

<a name="License"></a>
## License

Json-Works is licensed under the MIT License - see the `LICENSE` file for details.

[pointer]: http://tools.ietf.org/html/rfc6901/
[composer]: http://getcomposer.org
[download]: https://github.com/johnstevenson/json-works/archive/master.zip
[wiki]:https://github.com/johnstevenson/json-works/wiki/Home

