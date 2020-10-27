<?php

namespace App\Libraries;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * Class JsonAnswer
 * @package App\Libraries
 *
 * @property array     $attributes
 * @property integer   $status
 * @property JsonError $error
 */
class JsonAnswer implements \JsonSerializable
{
    public $attributes = [];
    public $status;
    public $error;

    /**
     * @param string     $message
     * @param array|null $details
     * @param array      $headers
     * @return JsonResponse
     */
    public function error($message = null, $details = [], $headers = [])
    {
        if (!is_array($details)) {
            if (is_string($details)) {
                $details = [$details];
            } elseif (is_object($details) && is_a($details, Collection::class)) {
                $details = $details->all();
            } else {
                $details = [];
            }
        }

        $defaultErrorMessages = [
            403 => __('errors.forbidden'),
            404 => __('errors.no_data_found'),
            422 => __('errors.validation_failed'),
        ];

        if (!$message && array_key_exists($this->status, $defaultErrorMessages)) {
            $message = $defaultErrorMessages[$this->status];
        }

        $this->error = new JsonError();
        $this->error->message = $message;
        $this->error->details = $details;
        $this->setDefaultHeaders($headers);
        return response()->json($this, $this->status, $headers);
    }

    /**
     * @param array $data
     * @param array $headers
     * @return JsonResponse
     */
    public function ok(array $data = [], $headers = [])
    {
        foreach ($data as $k => $v) {
            $this->$k = $v;
        }
        $this->setDefaultHeaders($headers);
        return response()->json($this, $this->status, $headers);
    }

    /**
     * @param array $headers
     */
    protected function setDefaultHeaders(array &$headers)
    {
        if (!array_key_exists('Encoding', $headers)) {
            $headers['Encoding'] = 'utf8';
        }
        /*if (!array_key_exists('Access-Control-Allow-Origin', $headers)) {
            $headers['Access-Control-Allow-Origin'] = '*';
        }*/
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set($name, $value)
    {
        $closed = ['status', 'error', 'data', 'api_version'];
        if (!in_array($name, $closed)) {
            $this->attributes[$name] = $value;
        }
    }

    /**
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        $result = [];
        $result['status'] = $this->status;

        if ($this->error && !in_array($this->status, [200, 201, 202])) {
            $result['error'] = $this->error;
        }

        if (is_array($this->attributes) && count($this->attributes) > 0) {
            foreach ($this->attributes as $key => $val) {
                $result[$key] = $val;
            }
        }

        $result['api_version'] = config('version');

        return $result;
    }
}
