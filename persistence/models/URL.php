<?php

class URL {
    public int $id;
    public DateTime $created;
    public DateTime $updated;
    public ?DateTime $deleted;
    public string $slug;
    public string $url;
    public ?string $accessKey;

    public function __construct(
        string $slug,
        string $url,
        ?string $accessKey
    ) {
        $this->slug = $slug;
        $this->url = $url;
        $this->accessKey = $accessKey;
    }
}