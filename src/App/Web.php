<?php


namespace  Source\App;

use Source\Router\Request;

class Web
{
    public function __construct()
    {

    }

    public function index($data)
    {
        $request = new Request();

    }

    public function post(): void
    {$request = (new Request())->request();
      $request->responseJson($request->data());
       return;
    }

    public function error()
    {
        var_dump('error');
    }


}
