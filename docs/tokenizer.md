# Tokenizer

This class provides methods to create [JSON Pointers][pointer] which reference the elements within
json data.

```php
<?php
namespace JohnStevenson\JsonWorks;

$tokenizer = new Tokenizer();
```

## Methods

* [add()](#add)
* [decode()](decode)
* [encode()](#encode)
* [encodekey()](#encodekey)

### add
string **add** ( string `$path`, string `$key` )

Returns a new JSON Pointer by concatenating *$path* with `/` plus the encoded value of *$key*. The
method is useful for building paths. Uses [encodeKey()](#encodekey) internally.

```php
<?php
$result = $tokenizer->add('', 'keyname');
# /keyname

$result = $tokenizer->add('/prop1', 'name/with/slash');
# /prop1/name~1with~1slash

$result = $tokenizer->add('/prop1', 'name~with~tilde');
# /prop1/name~0with~0tilde

$result = $tokenizer->add('/prop1/prop2', '');
# /prop1/prop2/ (this represents an empty key property of prop2)
```

### decode
array **decode** ( string `$path` )

Returns an array of decoded elements from an encoded JSON Pointer *$path*. Each element is decoded
by replacing all `~1` sequences with a forward-slash, then replacing all `~0` sequences with a
tilde.

```php
<?php
$result = $tokenizer->decode('/keyname');
# ['keyname']

$result = $tokenizer->decode('/prop1/name~1with~1slash');
# ['prop1', 'name/with/slash']

$result = $tokenizer->decode('/prop1/name~0with~0tilde');
# ['prop1', 'name~with~tilde']

$result = $tokenizer->decode('/prop1/prop2/');
# ['prop1', 'prop2', '']
```

### encode
string **encode** ( string | array `$path` )

Returns an encoded JSON Pointer from *$path*, which must either be a single string element, or an
array of path elements. Uses [add()](#add) internally.

```php
<?php
$result = $tokenizer->encode('keyname');
# /keyname

$result = $tokenizer->encode(['prop1', 'prop2', 'name/with/slash']);
# /prop1/prop2/name~1with~1slash
```

### encodeKey
string **encodeKey** ( string `$key` )

Encodes and returns *$key*. All tilde characters are replaced with `~0`, then all forward-slashes
are replaced with `~1`.

```php
<?php
$result = $tokenizer->encodeKey('keyname');
# keyname

$result = $tokenizer->encodeKey('name/with/slash');
# name~1with~1slash

$result = $tokenizer->encodeKey('name~with~tilde');
# name~0with~0tilde
```

[pointer]: https://www.rfc-editor.org/rfc/rfc6901
