<?php

require_once(ROOT . "/router/Router.php");

function fromController(string $path, string $endpoint = null) {
    return function(array &$context) use ($path, $endpoint) {
        if ($endpoint)
            $context[ENDPOINT] = $endpoint;

        return (require(ROOT . "/controllers/" . $path . ".php"))($context);
    };
}

return function(Router $router) {
    $router->addRoute(GET, "/", fromController("/GET"));
    $router->addRoute(POST, "/manage", fromController("/manage/POST"));

    $router->addRoute(GET, "/.*", fromController("/slug/GET"));
};
