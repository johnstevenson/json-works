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

The library is intended to be used with complex or deeply-nested json structures. Or any place where it easier to do something like the following:

```php
<?php
$document = new JohnStevenson\JsonWorks\Document();
$document->addValue('/path/to/nested/array/-', array('firstName'=> 'Fred', 'lastName' => 'Blogg'));
$json = $document->toJson(true);
```

which will output the following json:

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

You can get this value by calling:

```php
$fred = $document->getValue('/path/to/nested/array/0')
```

And you can update it with:

```php
$document->addValue('/path/to/nested/array/0/lastName', 'Bloggs');
```

or delete it with:

```php
$document->deleteValue('/path/to/nested/array/0');
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

Json-Works is licensed under the MIT License - see the `LICENSE` file for details

  [composer]: http://getcomposer.org
  [download]: https://github.com/johnstevenson/json-works/archive/master.zip
  [wiki]:https://github.com/johnstevenson/json-works/wiki/Home

