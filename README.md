# Language Module for Trongate v2

The **Language module** provides a simple and extensible system for multi-language support in Trongate v2 that includes:

- Language detection from URL, cookie, or default.
- Dynamic loading of language files.
- Global `_l()` helper for translating strings.
- Interceptor for early language detection before routing.

---

## Folder Structure

Here's a recommended folder layout for the Language module within your Trongate app:

```
app/
  config/
    config.php
    custom_routing.php
  languages/
    en.php
    fr.php
  modules/
    language/
      Language.php
      language_helper.php
  modules/
    projects/
      Projects.php
      views/
        edit.php
```

- app/config/: Configuration files for languages and routing.
- app/languages/: Translation files for each supported language.
- app/modules/language/: Contains the Language module and helper.
- app/modules/projects/: Inspired example module by [PartialShawn](https://github.com/PartialShawn/projects-manager-trongate) using the language system.

---

## 1. Configuration

### **config.php**

Define the available languages and default language:

```php
define('AVAILABLE_LANGUAGES', ['en', 'fr']);
define('DEFAULT_LANGUAGE', 'en');

// Interceptors (run before routing)
$interceptors = [
    'language' => 'before'
];
define('INTERCEPTORS', $interceptors);
```

- **AVAILABLE_LANGUAGES**: Array of language codes that your site supports.
- **DEFAULT_LANGUAGE**: Fallback language if none is detected.
- **INTERCEPTORS**: Runs the `before()` method of the `Language` module early in the request lifecycle to detect/ set the language.

---

### **custom_routing.php**

For language-based URLs, add routes to strip the language segment for routing:

```php
$routes = [
    'tg-admin' => 'trongate_administrators/login',
    'tg-admin/submit_login' => 'trongate_administrators/submit_login',
    'fr/(:any)/(:any)' => '$1/$2',
    'en/(:any)/(:any)' => '$1/$2'
];
define('CUSTOM_ROUTES', $routes);
```

- This allows URLs like `http://localhost/{app_name}/fr/projects/edit` to route to `projects/edit`.
- The interceptor will detect `fr` as the current language.

---

## 2. Language Files

Store language files in `languages/` within your **app folder**:

```
languages/
  en.php
  fr.php
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

`fr.php`

```php
<?php
return [
  'create_project' => 'Créer un projet',
  'update_project' => 'Mettre à jour le projet',
  'delete_project' => 'Supprimer le projet',
  'submit' => 'Envoyer'
];
```

- The keys are used in controllers/views.
- Add new languages by creating additional files (e.g., `es.php` for Spanish) and adding the code to `AVAILABLE_LANGUAGES` or letting it be auto-detected if you implement dynamic loading.

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

The `before()` method of the Language module:

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

- No changes are needed in `engine/ignition.php/get_segments()` or other core files.
- The module works fully via the interceptor and the helper.
- Adding a new language requires:
  1. Creating a `languages/xx.php` file.
  2. Adding the language code to `AVAILABLE_LANGUAGES` (or using dynamic detection).
  3. Adding a new custom route if you are using a URL

---

This setup provides a **fully modular, multilingual architecture** compatible with Trongate v2’s philosophy of modular MVC and zero core changes.

DaFa
