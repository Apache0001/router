<?php

namespace  Source\Router;

use Source\Router\Request;

abstract class Dispatch
{
    use RouterTrait;

    /** @var string $url */
    protected static $url;

    /** @var string $path */
    protected static $path;

    /** @var string $separator */
    protected static $separator;

    /** @var string $httpMethod */
    protected static $httpMethod;

    /** @var string $group */
    protected static $group = null;

    /** @var string $namespace */
    protected static $namespace;

    /** @var string $handler */
    protected static $handler;

    /** @var string $action */
    protected static $action;

    /** @var array $data */
    public static $data;

    /** @var array $routes */
    protected static $routes;

    /** @var array|null */
    protected static $route ;

    /** @var Request||null $request */
    protected static $request ;

    /** @var null|int $error */
    protected static $error ;

    /** @var int $METHOD_NOT_ALLOWD */
    protected static $METHOD_NOT_ALLOWD = 1;

    /** @var int $BAD_REQUEST */
    protected static $BAD_REQUEST = 400;

    /** @var int $NOT_FOUND */
    protected static $NOT_FOUND = 404;

    /** @var int $NOT_IMPLEMENTED */
    protected static $NOT_IMPLEMENTED = 500;

    /**
     * @param string $url
     * @param string $separator
     * @return void
     */
    public static function initDispatch(string $url, string $separator)
    {
        self::$url = (substr($url, -1)) == "/" ? substr($url, 0, -1) : $url;
        self::$path = rtrim((filter_input(INPUT_GET, "route", FILTER_DEFAULT) ?? "/"), "/");
        self::$separator = ($separator ?? "@");
        self::$httpMethod = $_SERVER["REQUEST_METHOD"];
        self::$request = new Request();
    }

    /**
     * @param string $namespace
     * @return void
     */
    public static function namespaces(string $namespace): void
    {
        self::$namespace = ($namespace ? ucwords($namespace) : null);
    }

    public static function getGroup()
    {
        return self::$group;
    }


    /**
     * @param string|null $group
     * @return void
     */
    public static function group(?string $group = null)
    {
        self::$group = ($group ? trim($group, "/") : null);
    }


    /**
     * @return bool
     */
    public static function dispatch(): bool
    {
        if(empty(self::$routes) || empty(self::$routes[self::$httpMethod])){
            self::$error = self::$NOT_IMPLEMENTED;
            return false;
        }

        self::$route = null;

        foreach (self::$routes[self::$httpMethod] as $key => $route) {
            if (preg_match("~^" . $key . "$~", self::$path, $found)) {
                self::$route = $route;
            }
        }

        return static::execute();
    }

    /**
     * @return int|null
     */
    public static function error(): ?int
    {
        return self::$error;
    }

    /**
     * @param string $key
     * @param $value
     * @return void
     */
    public static function data( $key, $value)
    {
          self::$data[$key] = $value;
    }

    public static function getData()
    {
        return self::$data;
    }


}
