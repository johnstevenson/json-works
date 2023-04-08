# Formatter

This class provides methods to manipulate array, object or json data.

```php
<?php
namespace JohnStevenson\JsonWorks;

$formatter = new Formatter();
```

## Methods

* [copy()](#copy)
* [order()](#order)
* [prune()](#prune)
* [toJson()](#tojson)

### copy
mixed **copy** ( mixed `$data` )

Returns an unreferenced copy of *$data*.

```php
<?php
$data = ['firstName' => 'Fred', 'lastName' => 'Bloggs'];

$result = $formatter->copy($data);

$fred = $result->firstName;
$bloggs = $result->lastName;
```

### order
mixed **order** ( mixed `$data`, stdClass `$schema` )

Returns an unreferenced copy of *$data*, with object properties re-ordered using the order found in
*$schema*. This is illustrated in the example below, which uses json-notation for PHP objects.

```php
<?php
# $schema
{
  "type": "object",
  "properties": {
    "prop1": {},
    "prop2": {},
    "prop3": {}
  }
}

# $data
{
  "prop3": "value 3",
  "prop2": {},
  "prop1": "value 1"
}

$data = $formatter->order($data, $schema);

# ordered $data
{
  "prop1": "value 1",
  "prop2": {},
  "prop3": "value 3"
}
```

Note that the ordering is fairly simplistic. Only the *properties* and *items* keywords are searched
in the schema, and only the property names listed are ordered. This means that property names
appearing within an *anyOf* schema, for example, will not be discovered or ordered. Any property
names not discovered or listed in the schema will be positioned after any ordered elements.

### prune
mixed **prune** ( mixed `$data` )

Returns an unreferenced copy of *$data*, having removed any empty object properties or arrays. This
is illustrated in the example below, which uses json-notation for PHP objects.

```php
<?php
# $data
{
  "prop1": "value 1",
  "prop2": {},
  "prop3": "value 3"
  "prop4": [],
  "prop5": 5
}

$data = $formatter->prune($data);

# pruned $data
{
  "prop1": "value 1",
  "prop3": "value 3",
  "prop5": 5
}
```

### toJson
mixed **toJson** ( mixed `$data`, bool `$pretty` )

Returns a json-encoded string of *$data*. Forward-slashes are not escaped and UTF-8 characters are
not encoded. If *$pretty* is true, the output will be *pretty-printed*.
