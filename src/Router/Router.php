<?php

namespace  Source\Router;

class Router extends Dispatch{

    /**
     * @param string $url
     * @param string $separator
     * @return void
     */
    public static function init(string $url, string $separator = '@')
    {
        Dispatch::initDispatch($url, $separator);
    }

    /**
     * @param string $route
     * @param $handler
     * @param string|null $name
     * @return void
     */
    public static function get(string $route, $handler, ?string $name = null)
    {
        Dispatch::addRoute("GET", $route, $handler, $name);
    }

    public static function post(string $route, $handler, ?string $name = null)
    {
        Dispatch::addRoute("POST", $route, $handler, $name);
    }

    public static function atribbutes()
    {
        var_dump(Dispatch::$url, Dispatch::$path, Dispatch::$separator, Dispatch::$httpMethod);
    }


}