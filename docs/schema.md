Json-Works includes an implementation of [JSON Schema][schema], version 4. However it differs from the official specification in that it does **not** resolve any external schemas using the `id` keyword. In fact it **ignores** this keyword completely.

It does however support inline references, but only using the keyword `$ref`, and therefore expects to find them within the schema data that has been loaded. The contrived example below shows how elements can reference each other.

```json
{
  "properties": {
    "prop1": {"$ref": "#/definitions/location"},
    "prop2": {"type": "array"},
    "prop3": {"$ref": "#/properties/prop1"}
  },
  "definitions": {
    "location": {
      "properties": {
        "lat": {"type": "number"}, "lon": {"type": "number"}
      }
    }
  }
}
```
Note that [JSON Pointer][pointer] syntax is used to reference other elements, with the required `#` hash symbol denoting the root of the schema. When the schema is loaded, the reference at `prop1` will be replaced with the schema found at `#/definitions/location`. Likewise, the reference at `prop3` will be replaced with the value at `prop1`, resulting in:

```json
{
  "properties": {
    "prop1": {
      "properties": {
        "lat": {"type": "number"}, "lon": {"type": "number"}
      }
    },
    "prop2": {"type": "array"},
    "prop3": {
      "properties": {
        "lat": {"type": "number"}, "lon": {"type": "number"}
      }
    }
  }
}
```

Documentation and examples of creating a schema can be found at [json-schema.org][schema].

## Validating
To validate a json document or structure you must create a new `JohnStevenson\JsonWorks\Document`, load the data and schema and then call the validate function.

```php
<?php
$document = new JohnStevenson\JsonWorks\Document();
$document->loadSchema($schema);
$document->loadData($data);

$result = $document->validate();
```
See more details at [Schema Validation](document.md#schema-validation)

[schema]: http://json-schema.org/
[pointer]: http://tools.ietf.org/html/rfc6901/
