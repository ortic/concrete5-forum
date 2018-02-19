<fieldset>
    <div class="form-group">
        <label class="control-label"><?= t('Number of messages to show') ?></label>
        <input type="text" name="messagesToShow" value="<?= $messagesToShow ?>" class="form-control">
    </div>
    <div class="form-group">
        <label class="control-label"><?= t('Forums to include') ?></label>
        <?php foreach ($forums as $forum) { ?>

            <div class="checkbox">
                <label>
                    <input type="checkbox" name="forumsToInclude[]"
                           value="<?= $forum->getCollectionId() ?>" <?= (in_array($forum->getCollectionId(), $forumsToInclude) || $tickAllForums) ? 'checked' : '' ?> />
                    <?= $forum->getCollectionName() ?>
                </label>
            </div>
        <?php } ?>
    </div>
</fieldset>
