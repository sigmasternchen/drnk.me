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
    <input type="text" name="url" value="<?php echo htmlspecialchars($data["url"] ?? ""); ?>" placeholder="https://">
    <input type="submit" value="Submit">
</form>