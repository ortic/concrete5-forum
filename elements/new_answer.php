<hr>

<h3 id="reply"><?= t('Add reply') ?></h3>

<?php if (User::isLoggedIn()) { ?>
    <form method="POST" action="<?= $self->action('writeAnswer') ?>" enctype="multipart/form-data">
        <?= $token->output('writeAnswer') ?>

        <div class="form-group">
            <label for="message"><?= t('Message') ?></label>
            <textarea class="form-control" name="message" id="message" placeholder=""></textarea>
        </div>
        <div class="form-group">
            <label for="attachment"><?= t('Attachment') ?></label>
            <input type="file" class="form-control" name="attachment" id="attachment" placeholder="">
        </div>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="1" id="subscribe" name="subscribe" checked>
            <label class="form-check-label" for="subscribe">
                <?=t('Send email when new message is posted')?>
            </label>
        </div>
        <button class="btn btn-primary"><?= t('Post Message') ?></button>
    </form>
<?php } else { ?>
    <div class="alert alert-info">
        <?= t('Please <a href="%s">sign in</a> or <a href="%s">register</a> to write a new topic.', $self->action('login'), $self->action('register'))?>
    </div>
<?php } ?>
