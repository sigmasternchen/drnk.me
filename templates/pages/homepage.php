<?php
require(__DIR__ . "/../layout/top.php");
?>

<div class="page-header">
    <h1>drnk.me</h1>
    <h2>URL Shortener</h2>
</div>

<img class="bottle" src="/static/img/bottle.png" alt="picture of a vile with a tag 'Drnk Me'" />

<div class="center-panel">
    <?php require(__DIR__ . "/components/url-form.php"); ?>
</div>

<?php
require(__DIR__ . "/../layout/bottom.php");
