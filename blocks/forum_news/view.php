<?php foreach ($forumMessages as $forumMessage) { ?>
    <a href="<?=$forumMessage->getLink()?>">
        <?= nl2br($forumMessage->getMessage()) ?>
    </a>
    <hr>
<?php } ?>