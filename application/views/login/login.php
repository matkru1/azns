<div id="message">
    <?php
if (isset($message)) {
    echo $message;
}
?>
</div>
<div id="twitterbutton">
<p>Aby skorzystać z aplikacji należy sie zalogować poprzez Twitter</p><a href="<?php echo $baseUrl ?>index.php/login"><img src="<?php echo $baseUrl ?>images/twitter_log_in.png"/></a>
</div>