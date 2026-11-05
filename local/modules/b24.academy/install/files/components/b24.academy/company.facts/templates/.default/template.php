<?php
defined('B_PROLOG_INCLUDED') || die;

/**
 * @var array $arResult
 */
?>
<div class="company__facts">
    <?php if (empty($arResult['facts'])) { ?>
        На текущий момент нет доступных фактов о компании
    <?php } ?>
    <?php foreach ($arResult['facts'] as $fact) { ?>
        <div class="company__fact">
            <h4><?= htmlspecialchars($fact['NAME']) ?></h4>
            <p><?= htmlspecialchars($fact['DETAIL_TEXT']) ?></p>
        </div>
    <?php } ?>
</div>