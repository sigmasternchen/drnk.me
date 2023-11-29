<?php

require_once(__DIR__ . "/../models/URL.php");

class URLs {
    private string $table = "dm_urls";

    private PDO $connection;

    public function __construct(PDO $connection) {
        $this->connection = $connection;
    }

    public function add(URL $entry) {
        $statement = $this->connection->prepare(<<<EOF
            INSERT INTO `$this->table`
                (`slug`, `url`, `access_key`) VALUES
                (?, ?, ?)
        EOF);
        $statement->execute([
            $entry->slug,
            $entry->url,
            $entry->accessKey,
        ]);
    }

    public function getBySlug(string $slug) {
        $statement = $this->connection->prepare(<<<EOF
            SELECT * FROM `$this->table`
            WHERE `slug` = ?
        EOF);
        $statement->execute([$slug]);

        if ($statement->rowCount() == 0) {
            return null;
        } else {
            $row = $statement->fetch();
            $url = new URL($row["slug"], $row["url"], $row["access_key"]);
            $url->id = $row["id"];
            $url->created = new DateTime($row["created"]);
            $url->updated = new DateTime($row["updated"]);
            $url->deleted = ($row["deleted"]) ? new DateTime($row["deleted"]) : null;

            return $url;
        }
    }
}
