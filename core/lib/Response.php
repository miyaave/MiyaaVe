<?php


namespace core\lib;


class Response
{

    public static function json($Response = [], $headersCodeAndMessage = "200")
    {

        header('HTTP/1.0 ' . $headersCodeAndMessage);
        return json_encode(
            $Response
        );

    }

    public static function error($errorCode = "", $message = "Please try again Later.", $headersCode = "401")
    {

        if ($errorCode == "") {
            $errorCode = uniqid();
        }

        header('HTTP/1.0 ' . $headersCode . '' . $message);
        return json_encode(
            ["code" => "$errorCode",
                "message" => "$message"
            ]
        );

    }

}