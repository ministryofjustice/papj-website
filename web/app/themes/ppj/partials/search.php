<?php

global $ppj_template_data;
$td = $ppj_template_data;

$title             = (isset($td['title']))                ? $td['title']                : '';
$jobFeedUrl        = (isset($td['job_feed_url']))         ? $td['job_feed_url']         : '';
$jobListMessage    = (isset($td['job_list_message']))     ? $td['job_list_message']     : '';
$jobListMessageURL = (isset($td['job_list_message_url'])) ? $td['job_list_message_url'] : '';

?>

<div class="l-full">
    <search title="<?= $title ?>"
            job-feed-url="<?= $jobFeedUrl ?>"
            job-list-message="<?= $jobListMessage ?>"
            job-list-message-url="<?= $jobListMessageURL ?>"
    ></search>
</div>
