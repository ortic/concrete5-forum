<h1><?=$topic->getSubject()?></h1>

<p>
    <?=nl2br($topic->getMessage())?>
</p>

<?php foreach ($messages as $message) { ?>
    <hr>
    <p>
        <?=nl2br($message->getMessage())?>
    </p>
<?php } ?>

<hr>

<form method="POST" action="<?=$this->action($topic->getSlug() . '/_answer')?>">
    <div class="form-group">
        <label for="message"><?=t('Message')?></label>
        <textarea type="text" class="form-control" name="message" id="message" placeholder=""></textarea>
    </div>
    <button class="btn btn-primary"><?=t('Post Message')?></button>
</form>