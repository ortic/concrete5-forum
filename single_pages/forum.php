<?php

$date = Core::make('date');
$token = Core::make('token');

?>

<?php if($success): ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<table class="table">
    <thead>
    <tr>
        <th><?= t('Subject') ?></th>
        <th><?= t('Author') ?></th>
        <th><?= t('Date') ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($topics as $topic) {
        ?>
        <tr>
            <td>
                <a href="<?= $topic->getCollectionLink() ?>">
                    <?= $topic->getCollectionName() ?>
                </a>
            </td>
            <td>
                <?php View::element('user_link', ['user' => UserInfo::getByID($topic->getVersionObject()->getVersionAuthorUserID())], 'ortic_forum') ?>
            </td>
            <td>
                <?= $date->formatDateTime($topic->getCollectionDateLastModified()) ?>
            </td>
            <td class="text-right">
                <a href="<?= $topic->getCollectionLink() ?>" class="btn btn-xs btn-primary">
                    <?= t('Show Topic') ?>
                </a>
            </td>
        </tr>
    <?php }
    ?>
    </tbody>
</table>

<?= $pagination->renderView() ?>

<hr>

<h3><?= t('Create new topic') ?></h3>

<form method="POST" action="<?= $this->action('writeTopic') ?>">
    <?php if($error_writeTopic): ?>
        <div class="alert alert-error"><?= $error_writeTopic ?></div>
    <?php endif; ?>

    <?php echo $token->output('writeTopic'); ?>

    <div class="form-group">
        <label for="subject"><?= t('Subject') ?></label>
        <input type="text" class="form-control" name="subject" id="subject" placeholder="">
    </div>
    <div class="form-group">
        <label for="message"><?= t('Message') ?></label>
        <textarea type="text" class="form-control" name="message" id="message" placeholder=""></textarea>
    </div>
    <button class="btn btn-primary"><?= t('Post Message') ?></button>
</form>