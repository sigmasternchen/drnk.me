<?php

return function (array &$context) {
    $data = [];

    if (key_exists("formonly", $_GET)) {
        require(ROOT . "/templates/pages/components/url-form.php");
    } else {
        require(ROOT . "/templates/pages/homepage.php");
    }
};
