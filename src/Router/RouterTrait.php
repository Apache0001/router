<?php

namespace Source\Router;

/**
 * [Router Trait]
 * @package Source\Router
 * @author Pablo O. Mesquita < pablo_omesquita@hotmail.com >
 */
trait  RouterTrait
{
    /**
     * @param string $method
     * @param string $route
     * @param $handler
     * @param string|null $name
     * @return void
     */
    protected static function addRoute(
        string $method,
        string $route,
        $handler,
        ?string $name = null
    ):void {

        $route = rtrim($route,"/");

        $removeGroupFromPath = Dispatch::$group ? str_replace(Dispatch::$group,"", Dispatch::$path) : Dispatch::$path;
        $pathAssoc = trim($removeGroupFromPath, "/");
        $routeAssoc = trim($route, "/");

        preg_match_all("~\{\s* ([a-zA-Z_][a-zA-Z0-9_-]*) \}~x", $routeAssoc, $keys, PREG_SET_ORDER);
        $routeDiff = array_values(array_diff_assoc(explode("/", $pathAssoc), explode("/", $routeAssoc)));

        self::RequestForm();

        $offset = 0 ;
        if(!empty($keys)){
            foreach($keys as $key){
                Dispatch::data($key[1], $routeDiff[$offset++] ?? null);
            }

        }

        $route = (!Dispatch::$group ? $route : "/".Dispatch::$group."{$route}");

        $namespace = Dispatch::$namespace;
        $data = Dispatch::$data;

        $router = function() use($method, $handler, $data, $route, $name, $namespace){
            return [
                "route" => $route,
                "name" => $name,
                "method" => $method,
                "handler" => static::handler($handler, $namespace),
                "action" => static::action($handler),
                "data" => $data
            ];
        };

        $route = preg_replace("~{([^}]*)}~", "([^/]+)", $route);
        Dispatch::$routes[$method][$route] = $router();

    }

    /**
     * @return bool
     */
    private static function execute(): bool
    {
        if(Dispatch::$route){

            if(is_callable(Dispatch::$route["handler"])){
                call_user_func(Dispatch::$route["handler"], (Dispatch::$route["data"] ?? []));
                return true;
            }

            $controller = Dispatch::$route["handler"];
            $method = Dispatch::$route["action"];

            if(class_exists($controller)){
                $newController = new $controller();

                if(method_exists($controller, $method)){
                    $newController->$method(Dispatch::$route["data"] ?? []);
                    return true;

                }

                Dispatch::$error = Dispatch::$METHOD_NOT_ALLOWD;
                return false;
            }

            Dispatch::$error = Dispatch::$BAD_REQUEST;
            return false;
        }

        Dispatch::$error = Dispatch::$NOT_FOUND;
        return false;
    }

    /**
     * @return void
     */
    protected static function requestForm()
    {
        if(Dispatch::$httpMethod == 'POST'){
            Dispatch::$data = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            return;
        }

        if(in_array(Dispatch::$httpMethod, ['PUT', 'PATCH', 'DELETE']) && !empty($_SERVER['CONTENT_LENGTH'])){
            parse_str(file_get_contents('php://input',false, null, 0, $_SERVER['CONTENT_LENGTH']),$putPatch);
            Dispatch::$data = $putPatch;
            return;
        }

        Dispatch::$data = [];
    }

    /**
     * @param $handler
     * @param string|null $namespace
     * @return mixed|string
     */
    private static function handler($handler, ?string $namespace)
    {
        return (!is_string($handler) ? $handler : "{$namespace}\\".explode(Dispatch::$separator, $handler)[0]);

    }

    /**
     * @param $handler
     * @return bool|mixed|string|null
     */
    private static function action($handler)
    {
        return (!is_string($handler) ?: (explode(Dispatch::$separator, $handler)[1] ?? null));
    }




}
