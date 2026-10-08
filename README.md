# Multitopic course format (format_multitopic)

A Moodle **course format** that shows multiple topics per page with tabbed navigation between pages.
Topics are collapsible and can optionally be timed (period-based sections), so a course can be
organised as a multi-level structure of tabs and sections instead of one long single-page list.

- **Component:** `format_multitopic`
- **Supported Moodle:** 5.0 – 5.2 (CI also tracks `main` / 5.3-dev)
- **License:** GNU GPL v3 or later
- **Upstream:** fork of [james-cnz/moodle-format_multitopic](https://github.com/james-cnz/moodle-format_multitopic)
- **Issues:** <https://github.com/rurak-ec/moodle-format_multitopic/issues>

> Development repository. The installable plugin lives in
> [`workspace/format_multitopic`](workspace/format_multitopic). The plugin's own README has the
> upstream feature details: [`workspace/format_multitopic/README.md`](workspace/format_multitopic/README.md).

---

## English

### What it does
- Organises course content into **pages with tabs**; each tab can hold several topics/sections.
- **Collapsible** topics, with an optional **timed** mode that lays sections out by date period.
- Drag-and-drop section and activity management, course index, and bulk editing, built on Moodle's
  modern `core_courseformat` output and reactive (state-based) JavaScript.

### Relationship to upstream
This is the rurak-ec fork. The upstream remote is kept as `upstream`
(`git@github.com:james-cnz/moodle-format_multitopic.git`) so improvements can still be pulled in.
Compared to upstream we cap the supported Moodle range to **5.0–5.3-dev** and apply house code-quality
conventions; **plugin functionality is unchanged**.

### Theming
Each row of section tabs (row 1: pages; row 2: sub-pages) is a compact segmented bar, like the theme's
secondary navigation: a softly tinted rounded bar, the tabs as text, a light pill on the active tab and thin
soft dividers between tabs. Every colour comes from the active theme's Bootstrap 5.3 variables
(`--bs-tertiary-bg`, `--bs-primary-bg-subtle`, `--bs-body-color`, `--bs-primary`, `--bs-emphasis-color-rgb`, …),
so the tabs follow each theme's colours and dark mode with no theme CSS. A theme can fine-tune them by
redefining these tokens on `body.format-multitopic .course-section-tabs`:

| Token | Used for |
|---|---|
| `--fmt-tab-bar-bg` | Bar background |
| `--fmt-tab-text` / `--fmt-tab-hover-text` | Tab text / text on hover |
| `--fmt-tab-active-bg` | Pill behind the active (and hovered) tab |
| `--fmt-tab-divider` | Dividers between tabs |
| `--fmt-tab-accent` | Marker, focus ring, "+" tab |
| `--fmt-tab-muted` | Hidden/unavailable sections (`.dimmed`) |
| `--fmt-tab-radius` / `--fmt-tabs-gap` | Corner radius / space above and below the tabs |

### Installation
1. Build the ZIP: `./scripts/package_workspace.sh`
2. Install via **Site administration → Plugins → Install plugins**, or copy
   `workspace/format_multitopic` to `course/format/multitopic` and run the upgrade.

### Quality
CI runs `moodle-plugin-ci` (phplint, phpcs, phpdoc, validate, savepoints, mustache, grunt, PHPUnit,
Behat) across Moodle 5.0 / 5.1 / 5.2 on PostgreSQL and MariaDB, plus a non-blocking `main` (5.3-dev) run.
A second workflow runs the JamesCNZ PHPDoc-types sniff.

---

## Español

### Qué hace
- Organiza el contenido del curso en **páginas con pestañas**; cada pestaña puede contener varios
  temas/secciones.
- Temas **colapsables**, con un modo **temporizado** opcional que distribuye las secciones por periodo
  de fechas.
- Gestión de secciones y actividades por arrastrar y soltar, índice del curso y edición masiva, sobre
  la salida moderna `core_courseformat` y JavaScript reactivo (basado en estado).

### Relación con upstream
Este es el fork de rurak-ec. Se conserva el remote `upstream`
(`git@github.com:james-cnz/moodle-format_multitopic.git`) para seguir trayendo mejoras. Frente a
upstream acotamos el rango de Moodle soportado a **5.0–5.3-dev** y aplicamos las convenciones de código
de la casa; **la funcionalidad del plugin no cambia**.

### Apariencia (temas)
Cada fila de pestañas de sección (fila 1: páginas; fila 2: subpáginas) es una barra segmentada compacta, como la
navegación secundaria del tema: barra redondeada con un tono suave, pestañas como texto, una pastilla clara en
la activa y líneas divisorias finas y suaves entre pestañas. Todos los colores salen de las variables de
Bootstrap 5.3 del tema activo, así que siguen los colores y el modo oscuro de cada tema sin CSS en el tema. Un
tema puede ajustarlos redefiniendo los tokens `--fmt-tab-*` (tabla en la sección *Theming*) sobre
`body.format-multitopic .course-section-tabs`.

### Instalación
1. Generar el ZIP: `./scripts/package_workspace.sh`
2. Instalar desde **Administración del sitio → Plugins → Instalar plugins**, o copiar
   `workspace/format_multitopic` a `course/format/multitopic` y ejecutar la actualización.

## Licencia / License
GNU GPL v3 or later. See [LICENSE](LICENSE).
