<?php
    if ($data["error"] ?? false) {
?>
        <div class="error">
            <?php echo $data["error"]; ?>
        </div>
<?php
    }
?>

<form action="/manage" method="POST" hx-post="/manage" hx-swap="innerHTML" hx-target=".center-panel">
    <input type="text" name="url" placeholder="<?php echo $data["url"] ?? "URL" ?>">
    <input type="submit" value="Submit">
</form>