<?php
declare(strict_types=1);
/** @var array $home */
/** @var array $config */
/** @var array $lang */
/** @var array $adapterOptions */
$homeName = htmlspecialchars($home['home_name'] ?? ('#' . $home['home_id']));
$homeId = (int)$home['home_id'];
?>
<div class="sw-admin sw-edit">
    <p><a href="?m=steam_workshop&amp;p=main">&larr; <?php echo htmlspecialchars($lang['button_cancel']); ?></a></p>

    <h3><?php echo htmlspecialchars(sprintf($lang['heading_edit_home'], $homeName)); ?></h3>

    <form method="post" action="?m=steam_workshop&amp;p=main&amp;action=save" class="sw-form">
        <input type="hidden" name="home_id" value="<?php echo $homeId; ?>" />
        <?php $formConfig = $config; include __DIR__ . '/partials/form_fields.php'; ?>
        <?php include __DIR__ . '/partials/mod_picker.php'; ?>
        <div class="sw-form__actions">
            <button class="btn primary" type="submit"><?php echo htmlspecialchars($lang['button_save']); ?></button>
            <a class="btn" href="?m=steam_workshop&amp;p=main"><?php echo htmlspecialchars($lang['button_cancel']); ?></a>
        </div>
    </form>

    <?php include __DIR__ . '/partials/mod_table.php'; ?>
</div>
