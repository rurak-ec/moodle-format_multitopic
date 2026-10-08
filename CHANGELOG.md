# Changelog

All notable changes to the rurak-ec fork of **format_multitopic** are documented here.
This fork tracks [james-cnz/moodle-format_multitopic](https://github.com/james-cnz/moodle-format_multitopic)
upstream; entries below describe the fork-specific deltas. The format is based on
[Keep a Changelog](https://keepachangelog.com/).

## [Unreleased]

## [v5.1.1-rurak.13] - 2026-10-08

### Changed
- **Layout containment & performance optimization**: Added `contain: layout;` to `body.format-multitopic .course-section-tabs` in `styles.css` to isolate DOM reflows and layout recalculations during dynamic tab switching.
- **CI & Moodle 5.3 compatibility matrix**: Added `MOODLE_503_STABLE` testing on PHP 8.3 (pgsql) and PHP 8.4 (mariadb) in `.github/workflows/moodle-plugin-ci.yml`.
- **Packaging portability**: Added Python 3 `zipfile` fallback to `scripts/package_workspace.sh`.
- **Version bump**: Updated `$plugin->version` to `2026100801` and release to `v5.1.1-rurak.13`.

### Changed (Previous)
- **Tab dividers back to 1px** (`v5.1.1-rurak.12`, `$plugin->version` unchanged, CSS only):
  `--fmt-tab-divider-width` 2px → 1px (it is also the `gap` between tabs). Height (60%) and colour (24%) unchanged.
- **Slightly shorter, slightly stronger tab dividers** (`v5.1.1-rurak.11`, `$plugin->version` unchanged, CSS
  only): the line height goes 70% → 60% of the bar (`top/bottom` 15% → 20%) and `--fmt-tab-divider` 18% → 24%
  of `--bs-emphasis-color-rgb`. Still 2px wide.
- **Dividers also next to the active and hovered tab** (`v5.1.1-rurak.10`, `$plugin->version` unchanged, CSS
  only): the `:has()` rules that hid the divider on both sides of the active or hovered tab are gone, so every
  pair of neighbouring tabs is separated, including the selected one. Nothing is drawn at the ends of a row.
- **Wider, softer tab dividers** (`v5.1.1-rurak.9`, `$plugin->version` unchanged, CSS only): the divider is now
  2px wide (new token `--fmt-tab-divider-width`, also the `gap` between tabs, with rounded ends) and
  `--fmt-tab-divider` goes 45% → 18% of `--bs-emphasis-color-rgb` (#8a8c8d → #d0d2d5 on Space 5's bar), so it
  reads as a soft band instead of a dark hairline. Height unchanged (70% of the bar).
- **Tab dividers clearly visible** (`v5.1.1-rurak.8`, `$plugin->version` unchanged, CSS only):
  `--fmt-tab-divider` 25% → 45% of `--bs-emphasis-color-rgb` (#b9bbbd → #8a8c8d on Space 5's bar; dark mode
  #606064 → #8a8a8d) and the line height 60% → 70% of the bar. Still 1px.
- **More visible tab dividers** (`v5.1.1-rurak.7`, `$plugin->version` unchanged, CSS only): `--fmt-tab-divider`
  12% → 25% of `--bs-emphasis-color-rgb` (#d8dadd → #b9bbbd on Space 5's bar) and 50% → 60% of the bar height.
- **Tabs redesigned as a segmented bar, like the theme's secondary navigation** (`v5.1.1-rurak.6`,
  `$plugin->version` unchanged, CSS only). Replaces the folder tabs below. Each row (pages, sub-pages) is a
  rounded bar sized to its tabs (`--fmt-tab-bar-bg` = `--bs-tertiary-bg`), with the tabs as 14px medium text,
  a light pill on the active and hovered tab (`--fmt-tab-active-bg` = `--bs-primary-bg-subtle`) and 1px soft
  dividers between tabs (`--fmt-tab-divider`, 12% of `--bs-emphasis-color-rgb`), hidden next to the active or
  hovered tab (`:has()`). The bar clips the divider of the first tab of each wrapped line. Dark mode uses a 5%
  light overlay for the bar and a 25% primary pill. Removed the rail, the tablet horizontal scroll and the
  tokens `--fmt-tab-accent-text`, `--fmt-tab-accent-bg(-hover)`, `--fmt-tab-accent-border` and
  `--fmt-tab-rail`.
- **No fill on inactive tabs; larger, bolder active tab** (`v5.1.1-rurak.5`, `$plugin->version` unchanged,
  CSS only). Inactive tabs are outlined only (`--fmt-tab-accent-bg: transparent`, a faint .05 tint on hover);
  the active tab's text is `1.0625em` (16 → 17px on Space 5; 14 → ~15px on phones) and
  `--bs-font-weight-bold` (700, was 600).
- **Thinner rail, softer tint** (`v5.1.1-rurak.4`). The rail under row 1 is now 1px (`--fmt-tab-rail`), the
  same weight as the tab outlines, and the inactive-tab tint is lighter (`--fmt-tab-accent-bg` .10 → .05,
  hover .18 → .10). CSS only: `$plugin->version` was deliberately **not** bumped (stays `2026100501`), so
  deploying needs only a cache purge and no `upgrade.php` run. On the production site the upgrade lock
  (`upgraderunning`) returned errors to every request for ~35s during the previous deploy.
- **Active tab is white and opens downwards** (`v5.1.1-rurak.3`, version `2026100501`). Row 1: the other tabs
  are now tinted with the theme's primary colour (`--fmt-tab-accent-bg`, darker on hover), and the active tab
  is white (`--fmt-tab-active-bg`: `--bs-white`, or the page background under `[data-bs-theme="dark"]`)
  and covers the rail, so there is no line under it. The rail is now an inset shadow of the row
  (`--fmt-tab-rail`), so the active tab can cover it inside the scrolling row on small screens too. Row 2
  became a panel of the active tab's colour, outlined and hanging from the rail. Tokens `--fmt-tab-bg` and
  `--fmt-tab-border` were removed; `--fmt-tab-active-bg`, `--fmt-tab-accent-bg-hover` and `--fmt-tab-rail`
  were added.
- **Section tabs with their own design, coloured by the active theme** (`v5.1.1-rurak.2`, version
  `2026100500`). Row 1 (pages) is drawn as folder tabs on a rail, with the active tab tinted and slightly
  taller; row 2 (sub-pages) is a light strip with underlined tabs. All colours are `--fmt-tab-*` tokens
  derived from the theme's Bootstrap 5.3 variables (`--bs-primary`, `--bs-primary-rgb`, `--bs-body-bg`,
  `--bs-secondary-color`, `--bs-border-color`, `--bs-emphasis-color`), so the tabs follow each theme's
  brand colour and dark mode (checked with Boost, Classic and Space 5). Below 992px each row scrolls
  sideways instead of wrapping; below 576px long names wrap to two lines between words. The previous
  hard-coded colours (`#0f6cbf` marker, `#6a737b` dimmed, `#212529` drop line) are gone. Rows are matched
  structurally (`.course-section-tabs > ul:nth-of-type(n)`), so the reactive JS re-render needs no change.
  CSS only (`styles.css`); no template, JS or DB change.
- `scripts/package_workspace.sh` falls back to the project's PHP image (`ZipArchive`) when `zip` is not
  installed, as in the other rurak-ec plugins.

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
