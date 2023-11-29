<?php

const ROOT = __DIR__;

define("MAINTENANCE_MODE", require(ROOT . "/maintenance.php"));

if (MAINTENANCE_MODE) {
    require(ROOT . "/templates/maintenance.php");
} else {
    $connection = require_once(ROOT . "/persistence/connection.php");
    (require(ROOT . "/persistence/migrate.php"))($connection);

    $repositories = (require_once(ROOT . "/persistence/Repositories.php"))($connection);

    $router = require(ROOT . "/router/Router.php");
    (require(ROOT . "/controllers/routes.php"))($router);

    require_once(ROOT . "/context.php");
    $context = [
        DB_CONNECTION => $connection,
        REPOSITORIES => $repositories,
    ];

    $router->execute($context);
}
