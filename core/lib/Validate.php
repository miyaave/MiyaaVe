<?php


namespace core\lib;


class Validate
{
    public static function email($email)
    {
        $checkValue = filter_var($email, FILTER_VALIDATE_EMAIL);
        if ($checkValue == null) return false;
        else return true;
    }

    public static function phone($phoneNumber)
    {
        $checkValue = preg_match('/^[0-9]{10}+$/', $phoneNumber);
        if ($checkValue == null) return false;
        else return true;
    }
}