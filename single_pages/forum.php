<?php
$date = Core::make('date');
$token = Core::make('token');
$request = Request::getInstance();
?>

<?php if ($forumSuccess) { ?>
    <div class="alert alert-success"><?= $forumSuccess ?></div>
<?php } ?>

<?php if ($forumError) { ?>
    <div class="alert alert-danger"><?= $forumError ?></div>
<?php } ?>

<table class="table">
    <thead>
    <tr>
        <th><?= t('Subject') ?></th>
        <th><?= t('Author') ?></th>
        <th><?= t('Created Date') ?></th>
        <th><?= t('Last Activity Date') ?></th>
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
            <td>
                <?= $date->formatDateTime($topic->lastMessageCreated) ?>
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

<?php View::element('new_topic', ['forumTopicSubject' => $forumTopicSubject, 'forumTopicMessage' => $forumTopicMessage, 'self' => $this, 'token' => $token], 'ortic_forum') ?>
