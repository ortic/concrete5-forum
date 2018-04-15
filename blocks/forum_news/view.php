<?php
$date = Core::make('date');
?>

<div class="ortic-forum-message-list">
    <?php foreach ($forumMessages as $forumMessage) { ?>
        <div>
            <a href="<?= $forumMessage->getLink() ?>">
                <strong><?= $forumMessage->getPage()->getCollectionName() ?></strong>
                <span class="ortic-forum-message-date"><?=$date->formatDateTime($forumMessage->getDateCreated()) ?></span>

                <p>
                    <?= $forumMessage->getMessageTeaser() ?>
                </p>
            </a>
        </div>
    <?php } ?>
</div>
