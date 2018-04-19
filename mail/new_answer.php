<?php

defined('C5_EXECUTE') or die('Access Denied.');

$subject = $topicPage->getCollectionName();
$body = t("
%s has posted to a forum topic you're monitoring: 

Message:

%s

Click the link below to view the discussion:
%s

Best regards
", $currentUser->getUserName(), $message->getMessage(), $message->getLink());
