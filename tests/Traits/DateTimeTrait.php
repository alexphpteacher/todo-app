<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 17.12.18
 * Time: 16:43
 */

namespace App\Tests\Traits;


trait DateTimeTrait
{
    public static function getTimeCTZ(string $str)
    {
        $a = new \DateTime($str);
        $a->setTimezone(new \DateTimeZone(date_default_timezone_get()));
        return $a->format(\DateTime::ATOM);
    }
}