<?php
if (!function_exists('_l')) {
  /**
   * Get localised string
   */
  function _l(string $key): string {
    static $language_module = null;

    if ($language_module === null) {
      // Automatically lazy-load the Language module
      $language_module = new Language('language');
    }

    return $language_module->get($key);
  }
}
