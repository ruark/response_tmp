<?php

namespace App;

use App\Libraries\JsonAnswer;

if (!function_exists('json')) {
    /**
     * @param int $status
     * @return JsonAnswer
     */
    function json($status = 200)
    {
        $o = new JsonAnswer();
        $o->status = (int)$status;
        return $o;
    }
}
