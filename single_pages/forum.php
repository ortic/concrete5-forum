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
        <th><?= t('Replies') ?></th>
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
                <?= $topic->messageCount - 1 ?>
            </td>
            <td>
                <a href="<?= $topic->getCollectionLink() ?>#message-<?=$topic->lastMessageId?>">
                    <?= $date->formatDateTime($topic->lastMessageCreated) ?>
                </a>
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
