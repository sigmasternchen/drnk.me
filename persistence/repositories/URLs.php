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

        return $this->getById($this->connection->lastInsertId());
    }

    public function getUnusedSlugs(array $slugs) {
        $placeholderList = join(",", array_map(fn($_) => "?", $slugs));

        $statement = $this->connection->prepare(<<<EOF
            SELECT `slug` FROM `$this->table`
            WHERE `slug` IN ($placeholderList)
        EOF);
        $statement->execute($slugs);

        $existing = $statement->fetchAll(PDO::FETCH_COLUMN, 0);

        return array_values(array_diff($slugs, $existing));
    }

    private function entityFromRow($row) {
        $url = new URL($row["slug"], $row["url"], $row["access_key"]);
        $url->id = $row["id"];
        $url->created = new DateTime($row["created"]);
        $url->updated = new DateTime($row["updated"]);
        $url->deleted = ($row["deleted"]) ? new DateTime($row["deleted"]) : null;

        return $url;
    }

    public function getById(int $id) {
        $statement = $this->connection->prepare(<<<EOF
            SELECT * FROM `$this->table` WHERE `id` = ?
        EOF);
        $statement->execute([$id]);
        return $this->entityFromRow($statement->fetch());
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
            return $this->entityFromRow($statement->fetch());
        }
    }

    public function getByUrl(string $url) {
        $statement = $this->connection->prepare(<<<EOF
            SELECT * FROM `$this->table`
            WHERE `url` = ?
        EOF);
        $statement->execute([$url]);

        if ($statement->rowCount() == 0) {
            return null;
        } else {
            return $this->entityFromRow($statement->fetch());
        }
    }
}
