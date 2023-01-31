<?php

require __DIR__ . "/vendor/autoload.php";

use Source\Router\Router;

/**
 * [Index Route]
 */

$url = "http://localhost/router";
Router::init($url, ":");

Router::namespaces('Source\App');
Router::get("/", 'Web:index');
Router::post("/post", 'Web:post');
Router::get('/login', function($teste){
    var_dump($teste);
});


/** errors */
Router::namespaces('Source\App');
Router::group('/ops');
Router::get('/error','Web@error');


Router::dispatch();

if(Router::error()){
    var_dump(Router::error());
}


