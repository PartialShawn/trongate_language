<?php
// Build options dynamically from the AVAILABLE_LANGUAGES constant.
// Label is derived by uppercasing the language code (e.g. 'en' → 'EN').
$lang_options = array_combine(
    AVAILABLE_LANGUAGES,
    array_map('strtoupper', AVAILABLE_LANGUAGES)
);

// Use the persisted cookie if valid, otherwise fall back to the configured default.
$current_lang = (isset($_COOKIE['site_lang']) && in_array($_COOKIE['site_lang'], AVAILABLE_LANGUAGES, true))
    ? $_COOKIE['site_lang']
    : DEFAULT_LANGUAGE;
?>
<div id="theme-toggle-container">

    <?php /* ── Language Switcher ── */ ?>
    <?= form_open('language/switch_lang', ['id' => 'lang-switcher-form']) ?>
    <?= form_dropdown('lang', $lang_options, $current_lang, [
        'id'       => 'lang-switcher',
        'aria-label' => 'Select language',
        'onchange' => "document.getElementById('lang-switcher-form').submit()",
    ]) ?>
    <?= form_close() ?>

    <?php /* ── Theme Toggle ── */ ?>
    <button id="theme-toggle" class="button-icon" aria-label="Toggle Theme">
        <span id="theme-icon"></span>
    </button>

</div>
<h1 class="mt-2"><?= _l('welcome_h1') ?></h1>
<h2><?= _l('welcome_h2') ?></h2>
<p class="mt-2"><?= _l('welcome_p') ?></p>

<div class="mt-3">
    <?php
    echo anchor('https://trongate.io', _l('welcome_visit_trongate'), ['class' => 'button', 'target' => '_blank']);
    echo anchor('https://trongate.io/docs', _l('welcome_view_docs'), ['class' => 'button alt', 'target' => '_blank']);
    ?>
</div>