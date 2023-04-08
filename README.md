# Json-Works

A PHP library to create, edit, query and validate [JSON][json].

## Installation

Install the latest version with:

```bash
$ composer require johnstevenson/json-works
```

## Requirements

* PHP 7.4 minimum, although using the latest PHP version is highly recommended.

## Usage

The library is intended to be used with complex json structures, or with json data that needs
validation. Full usage information is available in the [documentation](docs/doc.md).

* [Overview](#overview)
* [Validation](#validation)

### Overview
Json-Works allows you to create, edit and query json data using [JSON Pointer][pointer] syntax. For
example:

```php
<?php
$document = new JohnStevenson\JsonWorks\Document();

$document->addValue('/path/to/array/-', ['firstName'=> 'Fred', 'lastName' => 'Blogg']);

// prettyPrint
$json = $document->toJson(true);
```

which will give you the following json:

```json
{
    "path": {
        "to": {
            "array": [
                {
                  "firstName": "Fred",
                  "lastName": "Blogg"
                }
            ]
        }
    }
}
```

You can query this value by calling:

```php
$person = $document->getValue('/path/to/array/0');
```

and update it with:

```php
$document->addValue('/path/to/array/0/lastName', 'Bloggs');
```
and move it with:

```php
$document->moveValue('/path/to/array/0', '/users/-');

$document->tidy();
$json = $document->toJson(true);
```

to end up with:

```json
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

Json-Works includes an implementation of [JSON Schema][schema], version 4, which allows you to
validate json data. If the document contains invalid or missing value data, the validation will fail
with the error in `$document->getError()`.

```php
$document = new JohnStevenson\JsonWorks\Document();

$document->loadData('path/to/data.json');
$document->loadScheme('path/to/schema.json');

if (!$document->validate()) {
    $error = $document->getError();
}
```

You can also validate data whilst building a document. The following example schema describes an
array containing objects whose properties are all required and whose types are defined.

```json
// schemas can be very simple
{
    "items": {
        "properties": {
            "firstName": { "type": "string" },
            "lastName": { "type": "string" }
        },
        "required": [ "firstName", "lastName" ]
    }
}
```

Now you can check if your data is valid:

```php
$document->loadSchema($schema);
$document->addValue('/-', ['firstName'=> 'Fred']);

if (!$document->validate()) {
    $error = $document->getError();
    # "Property: '/0'. Error: is missing required property 'lastName'"
}
```

Without a schema, any value can be added anywhere.

## License

Json-Works is licensed under the MIT License - see the `LICENSE` file for details.

[json]: https://www.rfc-editor.org/rfc/rfc8259
[pointer]: https://www.rfc-editor.org/rfc/rfc6901
[schema]: https://json-schema.org/
[composer]: https://getcomposer.org
