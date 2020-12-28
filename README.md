# Translation Helper

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/f9a5efb2d2034e36a242e47ea4d1128c)](https://app.codacy.com/gh/26B/i18n-midoru?utm_source=github.com&utm_medium=referral&utm_content=26B/i18n-midoru&utm_campaign=Badge_Grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/60718d18625e4201879774c318e5410f)](https://www.codacy.com/gh/26B/i18n-midoru/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=26B/i18n-midoru&amp;utm_campaign=Badge_Grade)
[![Codacy Badge](https://app.codacy.com/project/badge/Coverage/60718d18625e4201879774c318e5410f)](https://www.codacy.com/gh/26B/i18n-midoru/dashboard?utm_source=github.com&utm_medium=referral&utm_content=26B/i18n-midoru&utm_campaign=Badge_Coverage)

This library has some helper functions/classes for dealing with exporting and importing translations based on a config file. Right now we're only supporting [Localise](https://localise.biz) as the source for translations.

## How to use?

There are essentially three interfaces, they only differ on how they ingest the options to the Translations system.

1. Use a JSON file with all the configuration.

    ```json
    {
        "project_name": {
            "export": {
                "ext": "mo",
                "format": "gettext"
            }
        }
    }
    ```

    ```php
    use TwentySixB\Translations\Translations;
    use TwentySixB\Translations\Input\File;

    // Make POT 
    $translations = new Translations( new File( 'path_to_file.json' ) );

    $translations->make_pots();
    $translations->upload();
    ```

2. Use PHP as your interface and have a PHP data structure with the configuration.

    ```php
    use TwentySixB\Translations\Translations;
    use TwentySixB\Translations\Input\Dataset;

    // Make POT 
    $translations = new Translations(
    new Dataset(
        [
            'project_name' => [
                'export' => [
                    'ext' => 'mo',
                    'format' => 'gettext',
                ]
            ]
        ]
    )
    );

    $translations->make_pots();
    $translations->upload();
    ```

3. Use a custom command and fetch config from the options passed to it.

    Create your two command files in PHP.

    ```php
    <?php
    // upload.php

    use TwentySixB\Translations\Translations;
    use TwentySixB\Translations\Input\CLI;

    $translations = new Translations( new CLI() );
    $translations->make_pots();
    $translations->upload();
    ```

    ```php
    <?php
    // download.php

    use TwentySixB\Translations\Translations;
    use TwentySixB\Translations\Input\CLI;

    $translations = new Translations( new CLI() );
    $translations->download();
    ```

    Now you can run them from the command line like this:

    ```bash
    # Make POTs and upload files.
    $ php run upload.php --name="project_name" --ext="mo" --format="gettext"

    # Download translations form the system for two locales.
    $ php run download.php --name="project_name" --ext="mo" --format="jed" --locale="pt_PT" --locale="en"
    ```

## Options

Regardless of the method to pass information to the `Translations` class, you can use any of the following options to configure what you want to happen.

### Export (export)

- `locale`
  - `string` | `array`
  - Short code(s) for the language(s) to export. See [Locale Export API](https://localise.biz/api/docs/export/exportlocale).
- `ext`
  - `string`
  - The extensions accepted by the Localise API. See [Locale Export API](https://localise.biz/api/docs/export/exportlocale).
- `format`
  - `string`
  - The format accepted by the Localise API. See [Locale Export API](https://localise.biz/api/docs/export/exportlocale).
- `domain`
  - `string` | `optional`
  - Domain for the export. Appended in the beginning of the filename, before the locale. See below.
- `filename`
  - `string` | `optional`
  - Filename for the export. Takes precedence over domain. See below.
- `js-handle`
  - `string` | `optional`
  - To be append at the end of the filename with a '-' preceding it, before the extension. See below.
- `path`
  - `string` | `optional` | `default:` ./
  - Path to the directory where the file will be saved.
- `wrap-jed`
  - `bool` | `optional` | `default:` true if format is JED
  - Specifies if the content in the exported JSON files should be wrapped by an array with key 'locale_data'.
- `name`
  - `string` | `required` if using CLI
  - Name of the project.

The file path will be comprised of:

```
path ?(domain|filename-) locale ?(-js-handle) .ext
```

- `?(.*)` indicates optional values.

### Import (import)

- `locale`
  - `string` | `array`
  - Short code(s) for the language(s) to import. See [Locale Import API](https://localise.biz/api/docs/import/import).
- `ext`
  - `string`
  - The extensions accepted by the Localise API. See [Locale Import API](https://localise.biz/api/docs/import/import).
- `domain`
  - `string` | `optional`
  - Domain for the import. Appended in the beginning of the filename, before the locale. See below.
- `filename`
  - `string` | `optional`
  - Filename for the import. Takes precedence over domain. See below.
- `js-handle`
  - `string` | `optional`
  - To be append at the end of the filename with a '-' preceding it, before the extension. See below.
- `path`
  - `string` | `optional` | `default:` ./
  - Path to the directory where the file will be saved.
- `name`
  - `string` | `required` if using CLI
  - Name of the project.

The file path will be comprised of:

```
path ?(domain|filename-) locale ?(-js-handle) .ext
```

- `?(.*)` indicates optional values.

### Make pots (make_pots)

- `domain`
  - `string`
  - Domain for the `wp i18n make-pots` command.
- `source`
  - `string`
  - Source path for the `wp i18n make-pots` command. Directory where the strings to be translated will be extracted from.
- `destination`
  - `string`
  - Destination path for the `wp i18n make-pots` command. Where the pot file will be saved.
- `skip-js`:
  - `bool` | `optional` | `default:` true
  - Whether the option `--skip-js` will be passed to the `wp i18n make-pots` command. Makes it so strings to be translated inside JS code will not be considered.

## TODO

- Maybe rest of api params.
- Join all arguments and indicate which are not usable in some situations.
