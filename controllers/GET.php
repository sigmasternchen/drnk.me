<?php

return function (array &$context) {
    $data = [];

    if (key_exists("formonly", $_GET)) {
        require(ROOT . "/templates/pages/fragments/url-form.php");
    } else {
        require(ROOT . "/templates/pages/homepage.php");
    }
};
