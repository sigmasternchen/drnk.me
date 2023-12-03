<?php

require_once(ROOT . "/utils/error.php");

const CANDIDATES_PER_ITERATION = 10;
const MIN_LENGTH = 4;
const MAX_LENGTH = 20;

const DEFAULT_SCHEMA = "http://";

function generateCandidate(string $length) {
    //$charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    $charset = "bcdfghklmnpqrstvwxyzBCDFGHKLMNPQRSTVWXYZ256789";

    return join("",
        array_map(
            fn($_) => $charset[rand(0, strlen($charset) - 1)],
            range(1, $length)
        )
    );
}

function validateInput(string $url) {
    $error = null;

    if (strpos($url, "://") === false) {
        $url = DEFAULT_SCHEMA . $url;
    }

    $puny = idn_to_ascii($url);

    $matches = [];
    preg_match(
        "/^.*?:\/\/([a-zA-Z0-9\-.]+).*$/",
        $puny,
        $matches
    );
    $hostname = $matches[1] ?? null;
    $matches = [];
    preg_match(
        "/^.*?:\/\/([0-9:.]+).*$/",
        $puny,
        $matches
    );
    $ip = $matches[1] ?? null;

    if ($url == DEFAULT_SCHEMA) {
        setStatusCode(400);
        $error = "Something went wrong. Please try again later.";
    } else if (!filter_var($puny, FILTER_VALIDATE_URL)) {
        setStatusCode(400);
        $error = "That URL doesn't looks right. Please try again.";
    } else if (substr($url, 0, 4) !== "http") {
        setStatusCode(400);
        $error = "Only http:// and https:// are supported.";
    } else if (
        ($hostname && !$ip && !dns_get_record($hostname, DNS_A | DNS_AAAA | DNS_CNAME)) ||
        ($ip && !filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE))
    ) {
        setStatusCode(400);
        $error = "This host doesn't seem to exist. Please use another one.";
    }

    if ($error) {
        $data = [
            "error" => $error,
            "url" => $url,
        ];
        require(ROOT . "/templates/pages/components/url-form.php");
        return null;
    } else {
        return $url;
    }
}

function generateSlug(URLs $repository) {
    $candidates = [];
    for ($length = MIN_LENGTH; count($candidates) == 0 || $length > MAX_LENGTH; $length++) {
        $candidates = $repository->getUnusedSlugs(
            array_map(
                fn($_) => generateCandidate($length),
                range(1, CANDIDATES_PER_ITERATION)
            )
        );
    }

    return $candidates[0];
}

function generateAccessKey(string $slug, string $url) {
    return sha1($url . "-" . $url . "-" . microtime() . "-" . rand());
}

return function (array &$context) {
    $url = $_POST["url"] ?? "";
    $url = validateInput($url);
    if (!$url) {
        return;
    }

    $repository = $context[REPOSITORIES]->urls;

    $result = $repository->getByUrl($url);
    if ($result) {
        // don't leak existing access key
        $result->accessKey = "";
    } else {
        $slug = generateSlug($repository);
        $accessKey = generateAccessKey($slug, $url);

        $result = $repository->add(new URL(
            $slug,
            $url,
            $accessKey
        ));
    }

    $data = [
        "url" => "https://drnk.me/" . $result->slug,
        "accessKey" => $result->accessKey,
    ];
    require(ROOT . "/templates/pages/components/creation-successful.php");
};
