<?php

require_once(ROOT . "/utils/error.php");

const CANDIDATES_PER_ITERATION = 10;
const MIN_LENGTH = 3;
const MAX_LENGTH = 20;

function generate_candidate($length) {
    $charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    return join("",
        array_map(
            fn($_) => $charset[rand(0, strlen($charset) - 1)],
            range(1, $length)
        )
    );
}

return function (array &$context) {
    $url = $_POST["url"] ?? "";

    if (!$url) {
        setStatusCode(400);
        $data = [
            "error" => "Something went wrong. Please try again later."
        ];
        require(ROOT . "/templates/pages/components/url-form.php");
        return;
    }

    $repository = $context[REPOSITORIES]->urls;

    $candidates = [];
    for ($length = MIN_LENGTH; count($candidates) == 0 || $length > MAX_LENGTH; $length++) {
        $candidates = $repository->getUnusedSlugs(
            array_map(
                fn($_) => generate_candidate($length),
                range(1, CANDIDATES_PER_ITERATION)
            )
        );
    }

    $slug = $candidates[0];
    $accessKey = sha1($url . "-" . $url . "-" . microtime() . "-" . rand());

    $result = $context[REPOSITORIES]->urls->add(new URL(
        $slug,
        $url,
        $accessKey
    ));

    $data = [
        "url" => "https://drnk.me/$slug",
        "accessKey" => $accessKey,
    ];
    require(ROOT . "/templates/pages/components/creation-successful.php");
};
