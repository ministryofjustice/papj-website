<?php

global $ppj_template_data;

$td = $ppj_template_data;
$textBlockClassName = 'text-block-container';
$textBlockClasses = $textBlockClassName;
$multiTextBlockClassName = "text-block__multi-text-block-container";
$multiTextBlockClasses = $multiTextBlockClassName;
$multiTextBlocks = false;

// the default format of this new acf select field is different if it hasn't been saved
if (isset($td['width'])) {
    if (is_array($td['width'])) {
        $layout = 'l-' . $td['width'][0];
    } else {
        $layout = 'l-' . $td['width'];
    }
} else {
    $layout = 'l-full';
}

$isValidRegularTextBlock = ($td['type'] == 'regular' && $td['content']);

$isDoubleTextBlock = ($td['type'] == 'double' && isset($td['double_text_blocks']) && is_array( $td['double_text_blocks']));
$isTripleTextBlock = ($td['type'] == 'triple' && isset($td['triple_text_blocks']) && is_array( $td['triple_text_blocks']));
$isValidMultiTextBlock = ($isDoubleTextBlock || $isTripleTextBlock);

$isValidAccordionBlock = ($td['type'] == 'accordion' && isset($td['accordion']) && is_array( $td['accordion']));

// compute the 'style' BEM modifier
if ($isValidMultiTextBlock) {
    $textBlockClasses .= ' ' . $textBlockClassName . '--multi';

    if ($isTripleTextBlock) {
        $multiTextBlocks = $td['triple_text_blocks'];
        $multiTextBlockClasses .= ' ' . $multiTextBlockClassName .'--triple';

        if (isset($td['triple_text_blocks'][0]['icon'])) {
            $isIconBlocks = true;
        } else {
            $multiTextBlockClasses .= ($isIconBlocks) ? '' : ' ' .$multiTextBlockClassName .'--text-only';
        }

    } else {
        $multiTextBlocks = $td['double_text_blocks'];
        $multiTextBlockClasses .= ' ' . $multiTextBlockClassName .'--double';
    }
}

if (isset($td['style']) && !is_array($td['style'])) {
    $textBlockClasses .= ' ' . $textBlockClassName . '--' . $td['style'];
}

if (isset($td['link']['title'])) {
    $textBlockClasses .= ' ' . $textBlockClassName . '--has-cta-link';
}

?>
<div class="<?= $layout ?>"
     id="<?= urlencode(strtolower($td['title'])) ?>"
>
    <div class="<?= $textBlockClasses ?>">
        <div class="text-block">
            <?php if ($td['title']) : ?>
                <h2 class="text-block__title">
                    <?= $td['title'] ?>
                </h2>
            <?php endif; ?>


            <?php if ($isValidRegularTextBlock) { ?>

                <div class="text-block__content">
                    <?= $td['content'] ?>
                </div>

            <?php } elseif ($isValidMultiTextBlock) {?>

                <div class="<?= $multiTextBlockClasses ?>">
                    <?php foreach ($multiTextBlocks as $block) : ?>
                        <div class="text-block__multi-text-block">
                            <?php if (isset($block['icon']) && $block['icon'] != 'none' ) : ?>
                                <div class="text-block__multi-text-block-icon">
                                    <?php include(get_template_directory() . '/dest' . $block['icon']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="text-block__multi-text-block-text-container">
                                <?php if (isset($block['icon_text']) && $block['icon_text']) : ?>
                                    <div class="text-block__multi-text-block-icon-text">
                                        <?= $block['icon_text'] ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (isset($block['content']) && $block['content']) : ?>
                                    <div class="text-block__multi-text-block-content">
                                        <?php echo do_shortcode($block['content']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

            <?php } elseif ($isValidAccordionBlock) { ?>

                <div class="text-block__content">
                    <accordion numbered="<?= (!empty($td['numbered']) ? 'true' : '') ?>">

                        <?php if (isset($td['accordion']) && is_array($td['accordion'])) : ?>
                            <?php foreach ($td['accordion'] as $el) : ?>
                                <accordion-element
                                    title="<?= $el['title'] ?>"
                                    subtitle="<?= $el['subtitle'] ?>"
                                >
                                    <?= $el['content'] ?>
                                </accordion-element>

                            <?php endforeach; ?>

                        <?php endif ?>

                    </accordion>
                </div>

            <?php } ?>

        </div>

        <?php if (isset($td['link']) && isset($td['link']['url']) && isset($td['link']['title'])) : ?>
            <div class="text-block__cta-link-container">
                <a href="<?= $td['link']['url'] ?>" class="text-block__cta-link"><?= $td['link']['title'] ?></a>
            </div>
        <?php endif; ?>

    </div>
</div>
