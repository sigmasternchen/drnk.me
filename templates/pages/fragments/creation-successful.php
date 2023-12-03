<div class="result">
    <a href="<?php echo $data["url"] ?? ""; ?>" target="_blank"><?php echo $data["url"] ?? ""; ?></a>
    <a href="javascript:copy('.result a')">copy</a>
</div>
<!-- <?php echo $data["accessKey"] ?? ""; ?> -->
Click <a hx-get="/?formonly" hx-swap="innerHTML" hx-target=".center-panel" href="/">here</a> to create a new link.
