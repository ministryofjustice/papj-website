<?php

global $ppj_template_data;
$td = $ppj_template_data;

//ppj\dump($td);

?>
<div class="l-full">
    <div class="landing-page-groups__container">
        <div class="landing-page-groups">
            <?php foreach($td['groups'] as $group): ?>
                <div class="landing-page-groups__group landing-page-groups__group--<?= strtolower(str_replace(' ', '-', $group['group_name'])) ?>">
                    <div class="landing-page-groups__group-title-container">
                        <h3 class="landing-page-groups__group-title"><?= $group['group_name'] ?></h3>
                    </div>

                    <?php $numberOfCardsInRow = sizeof($group['cards']); ?>
                    <div class="landing-page-groups__card-row landing-page-groups__card-row--number-of-cards-<?= $numberOfCardsInRow ?>">
                        <?php foreach($group['cards'] as $card): ?>
                            <div class="landing-page-groups__card">
                                <?php if(isset($card['image']['url'])): ?>
                                    <div class="landing-page-groups__card-image-container">
                                        <div class="landing-page-groups__card-image-ratio">
                                            <?php echo wp_get_attachment_image( $card['image']['id'], 'header-portrait-home',false, array( "class" => "landing-page-groups__card-image" ) ); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <h4 class="landing-page-groups__card-title"><?= $card['title'] ?></h4>

                                <p class="landing-page-groups__card-content"><?= $card['content'] ?></p>

                                <?php if (isset($card['link']['url'])): ?>
                                    <a class="landing-page-groups__card-cta-link"
                                       href="<?= $card['link']['url'] ?>"><?= $card['link']['title'] ?></a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
