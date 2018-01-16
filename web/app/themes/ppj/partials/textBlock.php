<?php

global $ppj_template_data;
$td = $ppj_template_data;

// the default format of this new acf select field is different if it hasn't been saved
if (is_array($td['width'])) {
    $layout = 'l-' . $td['width'][0];
} else {
    $layout = 'l-' . $td['width'];
}
?>
<div class="<?= $layout ?>"
     id="<?= urlencode(strtolower($td['title'])) ?>"
>

    <div class="text-block">
        <?php if ($td['type'] == 'regular') : ?>
            <?php if ($td['title']) : ?>
                <h2 class="text-block__title">
                    <?= $td['title'] ?>
                </h2>
            <?php endif; ?>

            <?php if ($td['subtitle']) : ?>
                <h3 class="text-block__subtitle">
                    <?= $td['subtitle'] ?>
                </h3>
            <?php endif; ?>

            <?php if ($td['content']) : ?>
                <div class="text-block__content">
                    <?= $td['content'] ?>
                </div>
            <?php endif; ?>

        <?php endif; ?>

        <?php if ($td['type'] == 'quote') : ?>
            <?php if ($td['quote']) : ?>
                <div class="text-block__quote">
                    <div class="text-block__quote-mark"></div>

                    <div class="text-block__quote-content">
                        <?= $td['quote'] ?>
                    </div>

                    <?php if ($td['quote_source']) : ?>
                        <div class="text-block__quote-source">
                            <?= $td['quote_source'] ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (isset($td['link']) && isset($td['link']['url']) && isset($td['link']['title'])) : ?>
            <div class="text-block__link-container">
                <a href="<?= $td['link']['url'] ?>" class="text-block__link"><?= $td['link']['title'] ?></a>
            </div>
        <?php endif; ?>

    </div>
</div>
