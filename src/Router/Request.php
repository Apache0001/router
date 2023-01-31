<?php

namespace  Source\Router;

/**
 * [ Class Request ]
 * @package Source\Router
 * @author Pablo O. Mesquita
 */
class Request
{
    /** @var string */
    protected $httpMethod;

    /** @var array|null */
    protected $queryString;

    /** @var array */
    protected $data = [];

    /**
     * construct
     */
    public function __construct()
    {
        $this->httpMethod = $_SERVER["REQUEST_METHOD"];
        parse_str($_SERVER["QUERY_STRING"], $this->queryString);

    }

    /**
     * @return null|array
     */
    public function data() :?array
    {
        return $this->data;
    }

    /**
     * @return void
     */
    public function request(): ?Request
    {
        if($this->httpMethod == 'POST'){
            $this->data = array_merge(filter_input_array(INPUT_POST, FILTER_DEFAULT), $this->queryString);
            return $this;
        }

        if(in_array($this->httpMethod, ['PUT', 'PATCH', 'DELETE']) && !empty($_SERVER['CONTENT_LENGTH'])){
            parse_str(file_get_contents('php://input',false, null, 0, $_SERVER['CONTENT_LENGTH']),$putPatch);
            $this->data = array_merge($putPatch, $this->data);
            return $this;
        }

    }

    /**
     * @param  $response
     * @return void
     */
    public function responseJson($response): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($response);
    }

}


