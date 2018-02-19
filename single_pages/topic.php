<h1><?= $topic->getSubject() ?></h1>

<?php foreach ($messages as $message) {
    $userIsOwner = ($user->getUserId() == $message->user->getUserId());
    ?>
    <div class="ortic-forum-message row thumbnail" id="message-<?=$message->getID()?>">

        <div class="col-xs-1">
            <?php View::element('user_avatar', ['user' => $message->user], 'ortic_forum') ?>
        </div>
        <div class="col-xs-11">
            <div>
                <strong><?php View::element('user_link', ['user' => $message->user], 'ortic_forum') ?></strong>
                <?= Core::make('helper/date')->formatDateTime($topic->getDateCreated()) ?>
                | <?php if ($userIsOwner) { ?>
                    <a class="ortic-forum-edit">
                        <?= t('Edit') ?>
                    </a>
                    <a class="ortic-forum-edit-cancel" style="display: none;">
                        <?= t('Cancel') ?>
                    </a>
                <?php } ?>
            </div>
            <div class="ortic-forum-message-text">
                <p>
                    <?= nl2br($message->getMessage()) ?>
                </p>
            </div>
            <div class="ortic-forum-message-edit" style="display: none;">
                <form method="POST"
                      action="<?= $this->action($topic->getSlug() . '/' . $message->getID() . '/_edit') ?>">
                    <textarea type="text" class="form-control" name="message" id="message"
                              placeholder=""><?= $message->getMessage() ?></textarea>
                    <button class="btn btn-primary"><?= t('Save') ?></button>
                </form>
            </div>

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