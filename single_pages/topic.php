<h1><?= $topic->getSubject() ?></h1>

<div class="ortic-forum-message row">
    <div class="col-xs-1">
        <?php View::element('user_avatar', ['user' => $topic->user], 'ortic_forum') ?>
    </div>
    <div class="col-xs-11">
        <div>
            <strong><?php View::element('user_link', ['user' => $topic->user], 'ortic_forum') ?></strong>
            <?=Core::make('helper/date')->formatDateTime($topic->getDateCreated())?>
        </div>
        <p>
            <?= nl2br($topic->getMessage()) ?>
        </p>
    </div>
</div>

<?php foreach ($messages as $message) { ?>
    <div class="ortic-forum-message row">
        <div class="col-xs-1">
            <?php View::element('user_avatar', ['user' => $message->user], 'ortic_forum') ?>
        </div>
        <div class="col-xs-11">
            <div>
                <strong><?php View::element('user_link', ['user' => $message->user], 'ortic_forum') ?></strong>
                <?=Core::make('helper/date')->formatDateTime($topic->getDateCreated())?>
            </div>
            <p>
                <?= nl2br($message->getMessage()) ?>
            </p>
        </div>
    </div>
<?php } ?>

<hr>

<form method="POST" action="<?= $this->action($topic->getSlug() . '/_answer') ?>">
    <div class="form-group">
        <label for="message"><?= t('Message') ?></label>
        <textarea type="text" class="form-control" name="message" id="message" placeholder=""></textarea>
    </div>
    <button class="btn btn-primary"><?= t('Post Message') ?></button>
</form>