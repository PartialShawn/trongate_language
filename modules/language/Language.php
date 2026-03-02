<?php

/**
 * Language Module for Trongate v2
 * 
 * Handles:
 * - Language detection via URL, cookie, or default
 * - Loading language files dynamically
 * - Providing translated strings via get() or the global _l() helper
 * - Interceptor for early language detection before routing
 */

class Language extends Trongate {

  /** @var array<string,array<string,string>> Cache of loaded languages */
  private static array $loaded_languages = [];

  /** @var string Current language code */
  private static string $current_language = '';

  /**
   * Constructor
   *
   * @param string|null $module_name Optional module name
   */
  public function __construct(?string $module_name = null) {
    parent::__construct($module_name);

    // Include the helper (makes _l() available globally)
    require_once __DIR__ . '/language_helper.php';

    // Detect language based on $_GET, cookie, or fallback
    $this->detect_language();
  }

  /**
   * Detect language from $_GET['lang'], cookie, or default
   *
   * @return void
   */
  private function detect_language(): void {
    if (self::$current_language !== '') {
        return;
    }

    // Check URL parameter first
    if (isset($_GET['lang']) && file_exists(APPPATH . "languages/{$_GET['lang']}.php")) {
      self::$current_language = strtolower($_GET['lang']);
      return;
    }

    // Fallback to cookie
    if (isset($_COOKIE['site_lang']) && file_exists(APPPATH . "languages/{$_COOKIE['site_lang']}.php")) {
      self::$current_language = strtolower($_COOKIE['site_lang']);
      return;
    }

    // Default language
    self::$current_language = DEFAULT_LANGUAGE;
  }

  /**
   * Load the current language file (if not already loaded)
   *
   * @return void
   * @throws Exception If the language file does not exist
   */
  private function load(): void {
    if (!isset(self::$loaded_languages[self::$current_language])) {
      $lang_file = APPPATH . "languages/" . self::$current_language . ".php";

      if (!file_exists($lang_file)) {
        throw new Exception("Language file not found: {$lang_file}");
      }

      self::$loaded_languages[self::$current_language] = include $lang_file;
    }
  }

  /**
   * Get a translated string by key
   *
   * @param string $key The translation key
   * @return string Translated string or key if not found
   */
  public function get(string $key): string {
    $this->load();
    return self::$loaded_languages[self::$current_language][$key] ?? $key;
  }

  /**
   * Get the current language code
   *
   * @return string
   */
  public function current(): string {
    return self::$current_language;
  }

  /**
   * Set the current language and persist it to the session cookie
   *
   * @param string $lang_code
   * @return void
   */
  public function set(string $lang_code): void {
    self::$current_language = strtolower($lang_code);
    $this->write_cookie(self::$current_language);
  }

  /**
   * Write the language choice to a long-lived cookie so it persists
   * across requests that do not carry a language URL prefix.
   *
   * @param string $lang
   * @return void
   */
  private function write_cookie(string $lang): void {
    setcookie(
      'site_lang',
      $lang,
      [
        'expires'  => time() + (86400 * 30),  // 30 days
        'path'     => '/',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax',
      ]
    );
  }

  /**
   * POST endpoint: switch the active language.
   *
   * Expects $_POST['lang'] to be a valid language code.
   * Sets the site_lang cookie and redirects back to the
   * referring page (or site root if no referrer).
   *
   * @return void
   */
  public function switch_lang(): void {
    // Validate CSRF token injected by form_close()
    $posted_token  = $_POST['csrf_token'] ?? '';
    $session_token = $_SESSION['csrf_token'] ?? '';

    if (!hash_equals($session_token, $posted_token)) {
      // Invalid or missing token — silently redirect home
      header('Location: ' . BASE_URL);
      exit;
    }

    // Token is consumed; regenerate on next form render
    unset($_SESSION['csrf_token']);

    $lang = strtolower(trim($_POST['lang'] ?? ''));

    if (in_array($lang, AVAILABLE_LANGUAGES, true)) {
      $this->set($lang);
    }

    // Redirect back to where the user came from
    $redirect = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
    header('Location: ' . $redirect);
    exit;
  }

  /**
   * Language Interceptor
   * 
   * Detects language from the original URL before routing
   * and sets $_GET['lang'] for use.
   *
   * @return void
   */
  public function before(): void {
    // Get the original URL path (without domain)
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $path = trim($path, '/'); // e.g., "fr/projects/edit"

    // Explode into segments
    $url_segments = explode('/', $path);

    // Language segment is expected as the first segment after the app folder
    $lang_segment = $url_segments[1] ?? null;

    if (in_array($lang_segment, AVAILABLE_LANGUAGES, true)) {
      // Language found in URL — use it and persist to cookie
      $_GET['lang'] = $lang_segment;
      $this->set($lang_segment);
    } elseif (
      isset($_COOKIE['site_lang']) &&
      in_array($_COOKIE['site_lang'], AVAILABLE_LANGUAGES, true)
    ) {
      // No URL prefix — honour the persisted cookie preference
      $_GET['lang'] = $_COOKIE['site_lang'];
      self::$current_language = $_COOKIE['site_lang']; // skip write_cookie; cookie already set
    } else {
      // No URL prefix and no valid cookie — fall back to default and set cookie
      $_GET['lang'] = DEFAULT_LANGUAGE;
      $this->set(DEFAULT_LANGUAGE);
    }
  }
}
