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
					{
                        "firstName": "Fred",
                        "lastName": "Blogg"
                    }
				]
			}
		}
	}
}
```

You can query this value by calling:

```php
$person = $document->getValue('/path/to/nested/array/0');
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
		{
            "firstName": "Fred",
            "lastName": "Bloggs"
        }
	]
}
```

then delete it with:

```php
$document->deleteValue('/users/0');
```

### Validation

Json-Works includes an implementation of [JSON Schema][schema], version 4. This allows you to validate your data. The following example schema describes an array containing objects whose properties are all required and whose types are defined. 

```
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
Without a schema, any value can be added anywhere.

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

Full usage [documentation][wiki] is available in the Wiki.

<a name="License"></a>
## License

Json-Works is licensed under the MIT License - see the `LICENSE` file for details.

[pointer]: http://tools.ietf.org/html/rfc6901/
[schema]: http://json-schema.org/
[composer]: http://getcomposer.org
[download]: https://github.com/johnstevenson/json-works/archive/master.zip
[wiki]:https://github.com/johnstevenson/json-works/wiki/Home

