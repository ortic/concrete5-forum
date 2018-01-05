<table class="table">
    <thead>
    <tr>
        <th><?= t('Subject') ?></th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($results as $message) { ?>
        <tr>
            <td>
                <a href="<?= $this->action($message->getSlug()) ?>">
                    <?= $message->getSubject() ?>
                </a>
            </td>
        </tr>
    <?php }
    ?>
    </tbody>
</table>

<?=$pagination->renderView()?>

<hr>

<form method="POST" action="<?=$this->action('_new')?>">
    <div class="form-group">
        <label for="subject"><?=t('Subject')?></label>
        <input type="text" class="form-control" name="subject" id="subject" placeholder="">
    </div>
    <div class="form-group">
        <label for="message"><?=t('Message')?></label>
        <textarea type="text" class="form-control" name="message" id="message" placeholder=""></textarea>
    </div>
    <button class="btn btn-primary"><?=t('Post Message')?></button>
</form>