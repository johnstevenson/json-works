<?php
/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks\Helpers;

/**
* A class for manipulating array, object or json data
*/
class Formatter
{
    /**
    * Encodes data into JSON
    *
    * @param mixed $data The data to be encoded
    * @param boolean $pretty Format the output
    * @return string Encoded json
    */
    public function toJson($data, $pretty)
    {
        $options = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE;
        $options |= $pretty ? JSON_PRETTY_PRINT : 0;

        $json = json_encode($data, $options);

        if ($pretty) {
            # collapse empty {} and []
            $json = preg_replace_callback('#(\{\s+\})|(\[\s+\])#', function ($match) {
                return $match[1] ? '{}' : '[]';
            }, $json);

            $json .= chr(10);
        }

        return $json;
    }
}
