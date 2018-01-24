<?php
$date = Core::make('date');
?>

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
                <a href="<?= $this->action($topic->getSlug()) ?>">
                    <?= $topic->getSubject() ?>
                </a>
            </td>
            <td>
                <?php View::element('user_link', ['user' => $topic->user], 'ortic_forum') ?>
            </td>
            <td>
                <?= $date->formatDateTime($topic->getDateCreated()) ?>
            </td>
            <td class="text-right">
                <a href="<?= $this->action($topic->getSlug()) ?>" class="btn btn-xs btn-primary">
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

<form method="POST" action="<?= $this->action('_new') ?>">
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