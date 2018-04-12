<?php
if (!$user) {
    echo t('Deleted User');
    return;
}

if ($user instanceof \Concrete\Core\Entity\User\User) {
    $user = $user->getUserInfoObject();
}
$userProfileUrl = $user ? $user->getUserPublicProfileUrl() : '';
?>

<?php if ($user) { ?>
    <?php if ($userProfileUrl) { ?>
        <a href="<?= $userProfileUrl ?>">
    <?php } ?>
    <?= $user->getUserName() ?>
    <?php if ($userProfileUrl) { ?>
        </a>
    <?php } ?>
<?php } ?>