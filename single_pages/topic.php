<?php
$token = Core::make('token');
?>

<?php if ($forumSuccess) { ?>
    <div class="alert alert-success"><?= $forumSuccess ?></div>
<?php } ?>

<?php if ($forumError) { ?>
    <div class="alert alert-danger"><?= $forumError ?></div>
<?php } ?>

<h1><?= $currentPage->getCollectionName() ?></h1>

<?php foreach ($messages as $message) {
    ?>
    <div class="ortic-forum-message row thumbnail" id="message-<?= $message->getID() ?>">

        <div class="col-xs-1">
            <?php View::element('user_avatar', ['user' => $message->user], 'ortic_forum') ?>
        </div>
        <div class="col-xs-11">
            <div>
                <strong><?php View::element('user_link', ['user' => $message->user], 'ortic_forum') ?></strong>
                <?= Core::make('helper/date')->formatDateTime($currentPage->getCollectionDateLastModified()) ?>
                <?php if ($message->canEdit()) { ?>
                    |
                    <a class="ortic-forum-edit" href="#">
                        <?= t('Edit') ?>
                    </a>
                    <a class="ortic-forum-edit-cancel" style="display: none;" href="#">
                        <?= t('Cancel') ?>
                    </a>
                    |
                    <a class="ortic-forum-delete"
                       href="<?= $this->action('deleteMessage', [$message->getID(), $token->generate('deleteMessage')]) ?>">
                        <?= t('Delete') ?>
                    </a>
                <?php } ?>
            </div>
            <div class="ortic-forum-message-text">
                <p>
                    <?= nl2br($message->getMessage()) ?>
                </p>
                <?php if ($attachment = $message->getAttachmentFile()) { ?>
                    <a href="<?= $attachment->getURL() ?>" target="_blank"><?= $attachment->getFileName() ?></a>
                <?php } ?>
            </div>
            <div class="ortic-forum-message-edit" style="display: none;">
                <form method="POST" action="<?= $this->action('updateMessage', [$message->getID()]) ?>">
                    <?php echo $token->output('updateMessage'); ?>

                    <textarea type="text" class="form-control" name="message" id="message"
                              placeholder=""><?= $message->getMessage() ?></textarea>
                    <button class="btn btn-primary"><?= t('Save') ?></button>
                </form>
            </div>

        </div>
    </div>
<?php } ?>

<?php View::element('new_answer', ['self' => $this, 'token' => $token], 'ortic_forum') ?>
