<?php

namespace App\Libraries;

/**
 * Class JsonError
 * @package App\Libraries
 *
 * @property string $message
 * @property array  $details
 * @property array  $fields
 */
class JsonError
{
    public $message;
    public $details = [];
}
