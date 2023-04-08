# JSON Schema

Json-Works includes an implementation of [JSON Schema][schema], version 4. However it differs from
the official specification in that it does **not** resolve any external schemas using the `id`
keyword. In fact it **ignores** this keyword completely.

It does however support inline references to sub-schemas using the keyword `$ref` and expects to
find them within the schema data that has been loaded. The contrived example below shows how a
sub-schema named `location` can be used and referenced.

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

Note that [JSON Pointer][pointer] syntax is used to reference other elements, with the required `#`
hash symbol denoting the root of the schema. When this schema is used, the reference at `prop1` will
be resolved with the schema found at `#/definitions/location`. Likewise, the reference at `prop3`
will be resolved with the value at `prop1`, resulting in an overall scheme of:

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

Sub-schemas are usually placed in a property of the document root, and named either `definitions` or
`$defs`.

Documentation and examples of creating a schema can be found at [json-schema.org][schema].

[schema]: https://json-schema.org/
[pointer]: https://www.rfc-editor.org/rfc/rfc6901
