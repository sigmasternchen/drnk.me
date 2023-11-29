<?php

return (function() {
    require(ROOT . "/credentials.php");

    return new PDO($DB_DSN, $DB_USERNAME, $DB_PASSWORD);
})();
