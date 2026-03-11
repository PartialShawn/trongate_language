# Language Module for Trongate v2

The **Language module** provides a simple and extensible system for multi-language support in Trongate v2 that includes:

- Language detection from URL, cookie, or default.
- Dynamic loading of language files.
- Global `_l()` helper for translating strings.
- Interceptor for early language detection before routing.

---

## Folder Structure

Here's the folder layout for the Language module within your Trongate app:

```
app/
│
├── config/
│   ├── config.php
│   └── custom_routing.php
├── languages/
│   ├── en.php
│   ├── fr.php
│   └── vn.php
├── modules/
│   ├── language/
│   │   ├── Language.php
│   │   └── language_helper.php
│   └── welcome/
│       ├── Welcome.php
│       ├── css/
│       │   └── custom.css
│       ├── js/
│       │   └── custom.js
│       └── views/
│           ├── default_homepage.php
│           └── demo_homepage.php
├── .gitignore
└── README.md
```

- app/config/: Configuration files for languages and routing.
- app/languages/: Translation files for each supported language.
- app/modules/language/: Contains the Language module and helper.
- app/modules/welcome/: Default welcome module with a demo view.

---

## 1. Configuration

### **config.php**

Define the available languages and default language:

```php
define('AVAILABLE_LANGUAGES', ['en', 'fr', 'vn']);
define('DEFAULT_LANGUAGE', 'en');

// Interceptors (run before routing)
$interceptors = [
    'language' => 'init'
];
define('INTERCEPTORS', $interceptors);
```

- **AVAILABLE_LANGUAGES**: Array of language codes that your site supports.
  - en = English
  - fr = French
  - vn = Vietnamese
- **DEFAULT_LANGUAGE**: Fallback language if none is detected.
- **INTERCEPTORS**: Runs the `init()` method of the `Language` module early in the request lifecycle to detect/ set the language.

---

### **custom_routing.php**

For language-based URLs, two consolidated routes strip the language prefix for any supported language:

```php
$routes = [
    'tg-admin' => 'trongate_administrators/login',
    'tg-admin/submit_login' => 'trongate_administrators/submit_login',
    '([a-z]{2})/(:any)/(:any)' => '$2/$3',
    '([a-z]{2})/(:any)' => '$2'
];
define('CUSTOM_ROUTES', $routes);
```

- A single regex pattern `([a-z]{2})` matches any two-letter language code (e.g. `en`, `fr`, `vn`) instead of repeating a route per language.
- Because `([a-z]{2})` is an explicit capture group, it occupies `$1`, so the subsequent `(:any)` captures shift to `$2` and `$3`.
- This allows URLs like `http://localhost/{app_name}/fr/welcome/index` to route to the French welcome index page.
- The interceptor detects the language prefix and sets the `site_lang` cookie for 30 days.
- To add a new language, no route changes are needed — just create the language file and add the code to `AVAILABLE_LANGUAGES`.

---

## 2. Language Files

Store language files in `languages/` within your **app folder**:

```
languages/
  en.php
  fr.php
  vn.php
```

Each file should return an associative array of translation keys:

`en.php`

```php
<?php
return [
    'create_project' => 'Create Project',
    'edit_project' => 'Edit Project',
    'delete_project' => 'Delete Project'
];
```

- The keys are used in controllers/views.
- Add new languages by creating additional files (e.g., `es.php` for Spanish).

---

## 3. Usage in Controllers

Inject the language module into your controller or use the global `_l()` helper:

```php
class Projects extends Trongate {

    public function edit(): void {
        // Option 1: Use Language module directly
        $project_name_label = $this->language->get('edit_project');

        // Option 2: Use global helper
        $project_name_label = _l('edit_project');

        $data['label'] = $project_name_label;
        $this->view('projects/edit', $data);
    }
}
```

- `$this->language->get('key')` reads the string for the current language.
- `_l('key')` is a global shortcut provided by the module’s helper.

---

## 4. Usage in Views

```php
<h1><?= _l('create_project') ?></h1>

<form method="post">
    <label><?= _l('project_name') ?></label>
    <input type="text" name="name" />
</form>
```

- All view files can use `_l()` without instantiating the module.
- Strings fallback to the key if no translation is found.

---

## 5. Interceptor Behavior

The `init()` method of the Language module:

1. Reads the first URL segment (e.g., `fr` or `en`).
2. Sets `$_GET['lang']` for use throughout the request.
3. Falls back to:
   - Cookie `site_lang` if present.
   - `DEFAULT_LANGUAGE` if neither URL nor cookie is valid.

---

## 6. Optional: Manually Switching Languages

```php
$this->language->set('fr'); // Force French
```

- Updates the current language dynamically.
- Useful for language switcher forms.

---

## 7. Notes

- No changes are needed in `engine/ignition.php` or other core files.
- The module works fully via the interceptor and the helper.
- The `([a-z]{2})` regex in routing and `in_array()` in the interceptor both validate the language code — malformed codes are silently ignored.
- Adding a new language requires:
  1. Creating a `languages/xx.php` file.
  2. Adding the language code to `AVAILABLE_LANGUAGES` in `config.php`.
  3. No route changes needed — the `([a-z]{2})` pattern handles all two-letter codes automatically.

---

This setup provides a **fully modular, multilingual architecture** compatible with Trongate v2’s philosophy of modular MVC and zero core changes.

DaFa
