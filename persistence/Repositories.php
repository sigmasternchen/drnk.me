<?php

require_once(__DIR__ . "/repositories/URLs.php");

class Repositories {
    private PDO $connection;

    private array $repositoryClasses = [
        "urls" => URLs::class,
    ];

    private array $repositoryCache = [];

    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    public function __get(string $name) {
        $repository = $this->repositoryCache[$name] ?? null;

        if (!$repository) {
            $repository = new ($this->repositoryClasses[$name])($this->connection);
            $this->repositoryCache[$name] = $repository;
        }

        return $repository;
    }

}

return function(PDO $connection): Repositories {
    return new Repositories($connection);
};