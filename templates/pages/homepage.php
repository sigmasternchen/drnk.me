<?php
require(__DIR__ . "/../layout/top.php");
?>

<div class="page-header">
    <h1>drnk.me</h1>
    <h2>URL Shortener</h2>
</div>

<img class="bottle" src="/static/img/bottle.png" alt="picture of a vile with a tag 'Drnk Me'" />

<div class="center-panel">
    <form action="?create" method="POST">
        <input type="text" name="url" placeholder="<?php echo $data["url"] ?? "URL" ?>">
        <input type="submit" value="Submit">
    </form>
</div>

<?php
require(__DIR__ . "/../layout/bottom.php");
