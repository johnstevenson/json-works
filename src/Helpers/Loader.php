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

use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Formatter;

/**
* A class for loading input data*
*/
class Loader
{
    const TYPE_DOCUMENT = 0;
    const TYPE_SCHEMA = 1;
    const TYPE_PATCH = 2;

    /**
    * Processes input to be used as a document
    *
    * The input can be:
    *   - a json string, passed to json_decode
    *   - a .json filename, passed to file_get_contents then json_decode
    *   - an object, class, array or null
    *
    * @api
    * @param mixed $input
    * @return mixed
    */
    public function loadData($input)
    {
        $data = $this->processInput($input, self::TYPE_DOCUMENT);

        if (!is_string($input)) {
            $formatter = new Formatter();
            $data = $formatter->copy($input);
        }

        return $data;
    }

    /**
    * Processes input to be used as a JSON Patch
    *
    * The input can be:
    *   - a json string, passed to json_decode
    *   - a .json filename, passed to file_get_contents then json_decode
    *   - an array
    *
    * The resulting data must be an array.
    *
    * @api
    * @param mixed $input
    * @return array
    */
    public function loadPatch($input)
    {
        return $this->processInput($input, self::TYPE_PATCH);
    }

    /**
    * Processes input to be used as a schema
    *
    * The input can be:
    *   - a json string, passed to json_decode
    *   - a .json filename, passed to file_get_contents then json_decode
    *   - an object
    *
    * The resulting data must be an object.
    *
    * @api
    * @param mixed $input
    * @return object
    */
    public function loadSchema($input)
    {
        return $this->processInput($input, self::TYPE_SCHEMA);
    }

    /**
    * The main input processing method
    *
    * @param mixed $data
    * @param integer $type
    * @return mixed
    */
    protected function processInput($data, $type)
    {
        if (is_string($data)) {
            $data = $this->processStringInput($data);
        }

        $this->checkData($data, $type);

        return $data;
    }

    /**
    * Processes a file or raw json
    *
    * @param string $input
    * @return mixed
    */
    protected function processStringInput($input)
    {
        if (pathinfo($input, PATHINFO_EXTENSION) === 'json') {
            $input = $this->getDataFromFile($input);
        }

        return $this->decodeJson($input);
    }

    /**
    * Returns the contents of a file
    *
    * @param string $filename
    * @throws \RuntimeException
    */
    protected function getDataFromFile($filename)
    {
        $json = @file_get_contents($filename);

        if ($json === false) {
            $error = new Error();
            throw new \RuntimeException($error->get(Error::ERR_NOT_FOUND, $filename));
        }

        return $json;
    }

    /**
    * Checks that data is valid for type
    *
    * @param mixed $data
    * @param integer $type
    * @throws \RuntimeException
    */
    protected function checkData($data, $type)
    {
        $dataType = gettype($data);

        switch ($type) {
            case self::TYPE_SCHEMA:
                $valid = $dataType === 'object';
                break;
            case self::TYPE_PATCH:
                $valid = $dataType === 'array';
                break;
            default:
                $valid = in_array($dataType, ['object', 'array', 'NULL']);
        }

        if (!$valid) {
            $error = new Error();
            throw new \RuntimeException($error->get(Error::ERR_BAD_INPUT, $dataType));
        }
    }

    /**
    * Decodes a json string
    *
    * This function normalizes pre PHP7 behaviour to report an error with
    * an empty string
    *
    * @param string $data
    * @return mixed
    * @throws \RuntimeException
    */
    protected function decodeJson($json)
    {
        $result = null;

        if (!strlen($json)) {
            $code = JSON_ERROR_SYNTAX;
        } else {
            $result = json_decode($json);
            $code = json_last_error();
        }

        if ($code) {
            throw new \RuntimeException($this->getJsonError($code));
        }

        return $result;
    }

    /**
    * Returns a formatted json error message
    *
    * @param integer $code
    * @return string
    */
    protected function getJsonError($code)
    {
        $msg = $code;

        if (function_exists('json_last_error_msg')) {
            $msg = json_last_error_msg();
        }

        $error = new Error();

        return $error->get(Error::ERR_BAD_INPUT, sprintf('json error: %s', $msg));
    }
}
