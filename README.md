Json-Works
==========

A PHP library to create, edit, query and validate [JSON](http://www.json.org/).

## Contents
* [About](#about)
* [Installation](#installation)
* [Usage](#usage)
* [License](#license)


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

## Installation
This package is available via [Composer][composer] as `johnstevenson/json-works`.
Either run the following command in your project directory:

```
composer require "johnstevenson/json-works=1.1.*"
```

or add the requirement to your `composer.json` file:

```
{
    "require": {
        "johnstevenson/json-works": "1.1.*"
    }
}
```

## Usage

See the [documentation](docs/home.md)

## License

Json-Works is licensed under the MIT License - see the `LICENSE` file for details.

[pointer]: https://www.rfc-editor.org/rfc/rfc6901
[schema]: https://json-schema.org/
[composer]: https://getcomposer.org
