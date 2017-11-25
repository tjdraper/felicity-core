<?php

/**
 * @author TJ Draper <tj@buzzingpixel.com>
 * @copyright 2017 BuzzingPixel, LLC
 * @license Apache-2.0
 */

namespace felicity\core\exceptions;

use Exception;
use Throwable;
use felicity\core\FelicityCore;
use felicity\core\services\config\Config;

/**
 * Class HttpException
 */
class HttpException extends Exception
{
    /**
     * HttpException constructor.
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Renders the error message
     * @throws self
     */
    public function render()
    {
        ob_clean();

        ob_start();

        http_response_code($this->getCode());

        $showErrors = Config::getItem('showErrors');

        if ($showErrors && $this->getCode() !== 404) {
            throw $this;
        }

        $customErrorMethod = Config::getItem(
            "customErrorMethod{$this->getCode()}"
        );

        if (\is_callable($customErrorMethod)) {
            echo($customErrorMethod($this));
            ob_end_flush();
            return;
        }

        $customErrorMethod = Config::getItem('customErrorMethod');

        if (\is_callable($customErrorMethod)) {
            echo($customErrorMethod($this));
            ob_end_flush();
            return;
        }

        include FelicityCore::getCoreSrcDir() . '/templates/HttpException.php';
        ob_end_flush();
    }
}
