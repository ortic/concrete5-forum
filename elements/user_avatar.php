<?php
if (!$user) {
    return;
}
if ($user instanceof \Concrete\Core\Entity\User\User) {
    $user = $user->getUserInfoObject();
}
$userProfileUrl = $user ? $user->getUserPublicProfileUrl() : '';
?>

<?php if ($userProfileUrl) { ?>
    <a href="<?= $userProfileUrl ?>">
<?php } ?>
    <div class="ortic-forum-avatar">
        <?= $user->getUserAvatar()->output() ?>
    </div>
<?php if ($userProfileUrl) { ?>
    </a>
<?php } ?>