<?php

// has to be variable instead of const because PHP sucks
$MIGRATION_TABLE = "dm_migrations";

function ensureDbStructureForMigrations(PDO $connection) {
    global $MIGRATION_TABLE;

    require(ROOT . "/credentials.php");

    if ($connection->query(<<<EOF
            SELECT 
                TABLE_NAME
            FROM 
            information_schema.TABLES 
            WHERE 
                TABLE_SCHEMA LIKE '${DB_SCHEMA}' AND 
                TABLE_TYPE LIKE 'BASE TABLE' AND
                TABLE_NAME = '${MIGRATION_TABLE}'
        EOF)->rowCount() == 0
    ) {
        if ($connection->exec(<<<EOF
                CREATE TABLE `${MIGRATION_TABLE}` (
                    `id` INTEGER PRIMARY KEY,
                    `file` VARCHAR(255),
                    `applied` DATETIME DEFAULT CURRENT_TIMESTAMP
                );
            EOF) === false
        ) {
            die("unable to bootstrap migrations: " . $connection->errorCode());
        }
    }
}

function getAllMigrations() {
    $files = scandir(ROOT . "/persistence/migrations/");
    $files = array_values(array_filter($files, fn($f) => $f[0] != "."));
    sort($files);

    $migrations = [];

    foreach ($files as $file) {
        $delimiterPos = strpos($file, "_");
        $migrations[intval(substr($file, 0, $delimiterPos))] = $file;
    }

    return $migrations;
}

function getAppliedMigrations(PDO $connection) {
    global $MIGRATION_TABLE;

    $result = $connection->query(<<<EOF
        SELECT * FROM `${MIGRATION_TABLE}`
    EOF);

    $migrations = [];
    foreach ($result->fetchAll() as $row) {
        $migrations[$row["id"]] = $row["file"];
    }

    return $migrations;
}

function getMigrationsToApply(PDO $connection) {
    $all = getAllMigrations();
    $applied = getAppliedMigrations($connection);

    foreach ($applied as $id => $file) {
        unset($all[$id]);
    }

    return $all;
}

function executeSqlScript(PDO $connection, string $sql, string $file) {
    if ($connection->exec($sql) === false) {
        die("failed to apply migration " . $file . ": " . $connection->errorCode());
    }
}

function applyMigration(PDO $connection, int $id, string $file) {
    global $MIGRATION_TABLE;

    $connection->beginTransaction();

    $sql = file_get_contents(ROOT . "/persistence/migrations/" . $file);
    if (!$sql) {
        die("Unable to read migration file: " . $file);
    }

    executeSqlScript($connection, $sql, $file);

    $statement = $connection->prepare(<<<EOF
        INSERT INTO `${MIGRATION_TABLE}`
            (`id`, `file`) VALUES 
            (?, ?)
    EOF);
    $statement->execute([$id, $file]);

    try {
        $connection->commit();
    } catch (PDOException $e) {
        // this might happen if the migration script contains a DDL statement
        // -> ignore
    }
}

return function(PDO $connection) {
    ensureDbStructureForMigrations($connection);

    $migrations = getMigrationsToApply($connection);
    foreach ($migrations as $id => $file) {
        applyMigration($connection, $id, $file);
    }
};
