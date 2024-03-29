## [Unreleased]

## [2.1.0] - 2023-04-17
* BC Break: `document->toJson()` can now return null on failure
* Fixed: JSON encode exceptions are now caught and the error captured
* Fixed: Invalid schema exceptions are now caught and the error captured
* Fixed: Issue when adding a new schema and not resetting the validator

## [2.0.0] - 2023-04-10
* Added: Major refactor for PHP 7.4 upwards

## [1.1.0] - 2016-01-05
* Updated test version to php7 and fixed failing json-decoding test
* Updated structure to use PSR4 autoloader

## [1.0.2] = 2016-01-04
* Updated test versions to PHP 5.6 and HHVM
* Multiple code-style fixes
* Fixed bug validating against arbitrarily large integers
([PR2](https://github.com/johnstevenson/json-works/pull/2)).
Thanks [aoberoi](https://github.com/aoberoi)
* Improved string handling in Document load functions and added tests

## [1.0.1] - 2013-04-25
* Fixed newline/format bugs in Utils:dataToJson and added tests
* Added JohnStevenson\JsonWorks\Schema\ValidationException

## [1.0.0] - 2013-04-22

* Initial stable release.
