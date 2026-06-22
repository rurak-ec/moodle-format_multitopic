# Changelog

All notable changes to the rurak-ec fork of **format_multitopic** are documented here.
This fork tracks [james-cnz/moodle-format_multitopic](https://github.com/james-cnz/moodle-format_multitopic)
upstream; entries below describe the fork-specific deltas. The format is based on
[Keep a Changelog](https://keepachangelog.com/).

## [Unreleased]

### Added
- New course format option **"Page title in body"** (`showpagetitleinbody`, default *Hide*): a
  page/tab no longer repeats its own name as a redundant heading inside the page body in the
  non-editing (student) view — the name still shows as a tab. The heading is kept in **editing mode**
  (to preserve rename/move/delete controls) and **topic** headings are unaffected. Implemented with a
  template flag (`fmthidepagetitle` in `content/section/header.php` + `header.mustache`) gated on the
  section level and `show_editor()`; no DB/schema change, `get_section_name()` untouched. Spanish
  strings added under `lang/es/`. Bumped `$plugin->version` to `2026062300`; phpcs 0/0.

### Changed
- Forked from upstream `v5.1.1` and adopted the rurak-ec workspace repo layout
  (`workspace/format_multitopic/`, `scripts/`, `docs/`, root `README`/`CONTRIBUTING`/`LICENSE`,
  `.github/workflows/moodle-plugin-ci.yml`).
- Capped the supported Moodle range to **5.0 – 5.3-dev** via `$plugin->supported = [500, 503]`
  (CI matrix: 5.0 / 5.1 / 5.2 stable + non-blocking `main` / 5.3-dev).

### Code quality (no functional changes)
- Added `fmt_is_section_user_visible()` and routed the four "Variant A" occurrences of the
  `$s->section == 0 || $s->uservisible && $format->is_section_visible($s)` predicate through it
  (`content.php`, `sectionnavigation.php` ×2, `global_navigation_wrapper.php`). The helper keeps the
  exact expression and short-circuit; verified `helper === inline` for every section of a multi-level
  course with hidden pages/topics; phpcs 0/0. The semantically-different "Variant B" sites
  (`section.php`, `tabtreecontainer.php`, and inside `is_section_visible()`) were left untouched.
- Split the 178-line `fmt_get_sections_extra()` into `fmt_sections_extra_forward_pass()` and
  `fmt_sections_extra_reverse_pass()` private helpers. Verified behaviour-preserving: the computed
  section-extra output (sanitised levels, prev/next/parent links, visibility, date range,
  current-nesting, page depth) is byte-identical on a multi-level course with periods and hidden
  sections; phpcs 0/0. The internal logic was moved verbatim (de-indented), not rewritten.
- Replaced the hand-maintained 101-entry bannerslice percentage list with an equivalent generated
  array (`array_map(... str_pad ..., range(0, 100))`); proven byte-identical to the original.
- Removed factually-incorrect `@since Moodle 2.0` / `@since Moodle 2.3` file-header tags.
- Added `phpcs.xml.dist` (Moodle standard with the `moodle.Commenting.TodoComment` issue-reference
  requirement relaxed for upstream fork notes), so `phpcs --max-warnings 0` is clean (0 errors,
  0 warnings) — verified locally with `moodlehq/moodle-cs`.

### Reviewed but not changed
- `course_format_options()` / `section_format_options()` were **not** merged: they only share
  Moodle's idiomatic static-cache skeleton; their option sets differ entirely (the `hiddensections`
  version-branch exists once, in `course_format_options()`), so there is no real duplication to
  extract and merging would break the expected per-method pattern.

### Deferred (next, CI-gated)
- Moodle 4.5 support is intentionally **not** declared; lowering the floor below 5.0 requires
  compatibility shims and is deferred to a separate functional pass.
