<?php

$token = Loader::helper('validation/token');

?>

<?php if($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<?php if($error): ?>
    <div class="alert alert-error"><?= $error ?></div>
<?php endif; ?>

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
                    <a class="ortic-forum-delete" href="<?= $this->action('deleteMessage', [$message->getID(), $token->generate('deleteMessage')]) ?>">
                        <?= t('Delete') ?>
                    </a>
                <?php } ?>
            </div>
            <div class="ortic-forum-message-text">
                <p>
                    <?= nl2br($message->getMessage()) ?>
                </p>
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

<hr>

<h3><?= t('Add reply') ?></h3>

<form method="POST" action="<?= $this->action('writeAnswer') ?>">
    <?php if($error_writeAnswer): ?>
        <div class="alert alert-error"><?= $error_writeAnswer ?></div>
    <?php endif; ?>

    <?php echo $token->output('writeAnswer'); ?>

    <div class="form-group">
        <label for="message"><?= t('Message') ?></label>
        <textarea type="text" class="form-control" name="message" id="message" placeholder=""></textarea>
    </div>
    <button class="btn btn-primary"><?= t('Post Message') ?></button>
</form>