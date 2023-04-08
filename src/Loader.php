<?php declare(strict_types=1);

/*
 * This file is part of the Json-Works package.
 *
 * (c) John Stevenson <john-stevenson@blueyonder.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace JohnStevenson\JsonWorks;

use \stdClass;

use JohnStevenson\JsonWorks\Helpers\Error;
use JohnStevenson\JsonWorks\Helpers\Utils;

/**
 * A class for loading input data.
 * @api
 */
class Loader
{
    /**
    * Processes input to be used as a document
    *
    * The input can be:
    *   - a json string, passed to json_decode
    *   - a .json filename, passed to file_get_contents then json_decode
    *   - an object, class, array
    *
    * @param mixed $input
    * @return mixed
    */
    public function getData($input)
    {
        $result = $this->processInput($input);
        $dataType = gettype($result);

        if ($dataType !== 'resource') {
            return $result;
        }

        throw new \RuntimeException($this->getInputError($dataType));
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
    * @param mixed $input
    * @return array<mixed>
    */
    public function getPatch($input): array
    {
        $result = $this->processInput($input);

        if (is_array($result)) {
            return $result;
        }

        $dataType = gettype($result);
        throw new \RuntimeException($this->getInputError($dataType));
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
    * @param mixed $input
    * @return stdClass
    */
    public function getSchema($input)
    {
        $result = $this->processInput($input);

        if ($result instanceof stdClass) {
            return $result;
        }

        $dataType = gettype($result);
        throw new \RuntimeException($this->getInputError($dataType));
    }

    /**
    * The main input processing method
    *
    * @param mixed $data
    * @return mixed
    */
    private function processInput($data)
    {
        if (is_string($data)) {
            $data = $this->processStringInput($data);
        } else {
            $formatter = new Formatter();
            $data = $formatter->copy($data);
        }

        return $data;
    }

    /**
    * Processes a file or raw json
    *
    * @return mixed
    */
    private function processStringInput(string $input)
    {
        if ($this->isFile($input)) {
            $input = $this->getDataFromFile($input);
        }

        return $this->decodeJson($input);
    }

    private function isFile(string $input): bool
    {
        return pathinfo($input, PATHINFO_EXTENSION) === 'json';
    }

    /**
    * Returns the contents of a file
    *
    * @throws \RuntimeException
    */
    private function getDataFromFile(string $filename): string
    {
        $json = @file_get_contents($filename);

        if ($json === false) {
            $error = new Error();
            throw new \RuntimeException($error->get(Error::ERR_NOT_FOUND, $filename));
        }

        return $json;
    }

    /**
    * Decodes a json string
    *
    * This function allows a JSON text as per RFC 8259, except for an empty string
    *
    * @param string $value
    * @return mixed
    * @throws \RuntimeException
    */
    private function decodeJson(string $value)
    {
        $result = $value;
        $errorMsg = null;

        if ($this->checkJson($value, $errorMsg)) {
            $result = json_decode($value);

            if (json_last_error() > 0) {
                $errorMsg = json_last_error_msg();
            }
        }

        if ($errorMsg !== null) {
            throw new \RuntimeException($this->getJsonError($errorMsg));
        }

        return $result;
    }

    private function checkJson(string &$value, ?string &$errorMsg): bool
    {
        $value = trim($value);

        if (Utils::stringIsJson($value)) {
            return true;
        }

        if (Utils::stringIsEmpty($value)) {
            $errorMsg = 'Syntax error';
        }

        return false;
    }

    private function getInputError(string $dataType): string
    {
        $error = new Error();

        return $error->get(Error::ERR_BAD_INPUT, $dataType);
    }

    /**
    * Returns a formatted json error message
    *
    */
    private function getJsonError(string $errorMsg): string
    {
        $error = new Error();
        $msg = sprintf('json error: %s', $errorMsg);
        return $error->get(Error::ERR_BAD_INPUT, $msg);
    }
}
