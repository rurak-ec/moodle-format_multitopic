# Plugin map (format_multitopic)

All paths are relative to `workspace/format_multitopic/`. Deployed location:
`{moodleroot}/course/format/multitopic/`.

## Entry points
- `version.php` — plugin metadata (`component`, `version`, `requires`, `supported`, `dependencies`).
- `format.php` — page entry that renders the course in this format.
- `lib.php` — the `format_multitopic` class (extends `core_courseformat\base`): section model
  (`fmt_get_sections_extra()`), format/section options, navigation, banners.
- `locallib.php` — section-move/utility functions used by the format.
- `settings.php` — admin settings.
- `styles.css` — plugin CSS (tab markers, dimmed states, banners).

## Forked core files (kept in sync by hand)
- `_course_changenumsections.php` — customised copy of core `course/changenumsections.php`; reached
  from `classes/output/courseformat/content/addsection.php` and `.../contenttabs/tabtreecontainer.php`.
- `_course_edit.js` — legacy (non-AMD) banner-preview script, loaded from `lib.php` via
  `$PAGE->requires->js('/course/format/multitopic/_course_edit.js')`; mirrored by
  `classes/courseheader.php`.

## classes/
- `output/renderer.php` — main renderer.
- `output/courseformat/…` — `content.php`, section/tab output (`section/controlmenu.php`,
  `section/header.php`, `contenttabs/tabtreecontainer.php`, …) overriding `core_courseformat` output.
- `courseformat/stateactions.php`, `courseformat/sectionactions.php` — reactive state/section actions.
- `courseheader.php`, `coursecontentheaderfooter.php`, `section_info_extra.php`,
  `global_navigation_wrapper.php` (instantiated from `lib.php`).
- `privacy/provider.php` — null privacy provider.

## amd/
- `src/` — ES6 modules (course content, course index, tabs, mutations). Compiled with `grunt amd`
  to `src`→`build/`. The committed `build/*.min.js` ships as-is.

## Other
- `backup/moodle2/restore_format_multitopic_plugin.class.php` — restore handling.
- `db/upgrade.php` — upgrade steps.
- `lang/en/format_multitopic.php` — English strings.
- `templates/` — Mustache templates for the format output.
- `tests/behat/` — 12 Behat feature files (the regression safety net; there are currently no PHPUnit
  tests).
