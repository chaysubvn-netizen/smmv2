<?php

namespace App;

class VersionHelper
{
    public static function get()
    {
        return config('app.version');
    }
}
