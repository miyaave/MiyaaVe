<?php


namespace app\controllers;

use core\lib\Response;


class TestController
{
    public function checkData()
    {

        $data = json_decode(file_get_contents("php://input"));

        if ($data == null) {
            echo Response::json([
                "code" => "UC001",
                "message" => "Please try again Later."
            ], '401 Please try again Later.');
            exit();
        }

        return $data;
    }

    public function test()
    {
        $data = self::checkData();

        $res = Response::json([
            "success" => true,
            "data" => $data
        ], '200 Success.');

        echo $res;
        exit();
    }

    public function testGet()
    {
        $res = Response::json([
            "success" => true,
            "message" => "Hello World !"
        ], '200 Success.');
        echo $res;
        exit();
    }

    public function testGetById($data)
    {

        if (isset($data)) {

            $res = Response::json([
                "success" => true,
                "data" => $data['id']
            ], '200 Success.');
        } else {
            $res = Response::json([
                "message" => "Please Check your Inputs."
            ], '401 Please try again Later');
        }

        echo $res;
        exit();
    }
}
