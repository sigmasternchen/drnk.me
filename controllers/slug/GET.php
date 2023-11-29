<?php

require_once(ROOT . "/utils/error.php");

return function (array &$context) {
    $path = substr($context[REQUEST_PATH], 1);
    $url = $context[REPOSITORIES]->urls->getBySlug($path);

    if (!$url) {
        redirect("/");
    } else {
        redirect($url->url);
    }
};
