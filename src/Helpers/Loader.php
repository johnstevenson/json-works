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
* A class for loading data
*/
class Loader
{
    public function load($input, $isSchema)
    {
        if (is_string($input)) {
            $this->checkData($input, $isSchema);
            return $this->processStringInput($input);
        }

        $this->checkData($input, $isSchema);

        if ($isSchema) {
            return $input;
        }

        $formatter = new Formatter();
        return $formatter->copy($input);
    }

    protected function processStringInput($input)
    {
        if (!preg_match('/^(\{|\[)/', $input)) {
            $input = $this->getDataFromFile($input);
        }

        $json = json_decode($input);

        if (!json_last_error()) {
            return $json;
        }

        $error = new Error();
        throw new \RuntimeException($error->get(Error::ERR_BAD_INPUT, 'json'));
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

    protected function checkData($data, $isSchema)
    {
        if (is_resource($data)) {
            $error = new Error();
            throw new \RuntimeException($error->get(Error::ERR_BAD_INPUT, 'resource'));
        }

        if ($isSchema && !is_object($data)) {
            $error = new Error();
            throw new \RuntimeException($error->get(Error::ERR_BAD_INPUT, gettype($data)));
        }
    }
}
