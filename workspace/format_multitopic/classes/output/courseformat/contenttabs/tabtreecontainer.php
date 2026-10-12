<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace format_multitopic\output\courseformat\contenttabs;

use core\output\html_writer;
use core\output\named_templatable;
use core\output\renderable;
use core\output\renderer_base;
use core\url;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use stdClass;

/**
 * Class to render tabs.
 *
 * @package   format_multitopic
 * @copyright 2019 onwards James Calder and Otago Polytechnic
 * @copyright based on work by 2012 David Herney Bernal - cirano
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tabtreecontainer implements named_templatable, renderable {
    use courseformat_named_templatable;

    /** @var course_format the course format */
    protected $format;

    /**
     * Constructor.
     *
     * @param course_format $format the course format
     */
    public function __construct(course_format $format) {
        $this->format = $format;
    }

    /**
     * Check if a section has direct content (activities or summary).
     *
     * @param \format_multitopic\section_info_extra|object $sectionextra
     * @return bool
     */
    protected function section_has_content($sectionextra): bool {
        if (!is_object($sectionextra) || !isset($sectionextra->sectionbase)) {
            return false;
        }
        $base = $sectionextra->sectionbase;
        $hasactivities = !empty($base->sequence);
        $summary = trim(strip_tags($base->summary ?? ''));
        $hassummary = !empty($summary) || preg_match('/<img|<iframe|<video|<audio/i', $base->summary ?? '');
        return $hasactivities || $hassummary;
    }

    /**
     * Find the first visible child section extra for a parent section.
     *
     * @param \format_multitopic\section_info_extra $parentsectionextra
     * @return \format_multitopic\section_info_extra|null
     */
    protected function get_first_child_section_extra($parentsectionextra) {
        $sectionsextra = $this->format->fmt_get_sections_extra();
        foreach ($sectionsextra as $sec) {
            if ($sec->parentid == $parentsectionextra->id && $this->format->is_section_visible($sec->sectionbase)) {
                return $sec;
            }
        }
        return null;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return \stdClass data context for a mustache template
     */
    #[\Override]
    public function export_for_template(renderer_base $output): stdClass {
        $format = $this->format;
        $course = $format->get_course();
        $sectionsextra = $format->fmt_get_sections_extra();
        $displaysectionextra = $sectionsextra[$format->get_sectionid()];
        $maxsections = $format->get_max_sections();
        $canaddmore = ($maxsections > $format->get_last_section_number());

        $activetab = [
            ($displaysectionextra->levelsan <= 0) ? $displaysectionextra->id : $displaysectionextra->parentid,
            $displaysectionextra->id,
        ];
        $tabrows = $this->get_tab_rows($sectionsextra, $activetab, $format->show_editor());

        $eft = (object)['tabs' => false, 'secondrow' => false];
        $roweft = null;
        $rowlevel = 0;
        foreach ($tabrows as $tabrow) {
            if (!$tabrow) {
                break;
            }
            if ($roweft == null) {
                $roweft = $eft;
            } else {
                $roweft->secondrow = (object)[];
                $roweft = $roweft->secondrow;
            }
            $roweft->tabs = [];

            // Determinar qué pestaña de esta fila debe estar activa.
            $activeid = $activetab[$rowlevel] ?? null;
            if ($rowlevel == 1 && !in_array($activeid, $tabrow) && !empty($tabrow)) {
                // Si la sección padre se omitió de la fila 2 por no tener contenido,
                // marcar activa la primera subpestaña existente en la fila.
                $firsttab = reset($tabrow);
                if (is_numeric($firsttab)) {
                    $activeid = $firsttab;
                }
            }

            foreach ($tabrow as $tabid) {
                $roweft->tabs[] = $this->export_tab_for_template(
                    is_numeric($tabid) ? $sectionsextra[$tabid] : $tabid,
                    $rowlevel,
                    $canaddmore,
                    $output,
                );
                if ($tabid == $activeid) {
                    end($roweft->tabs)->active = true;
                }
            }
            $roweft->secondrow = false;
            $rowlevel++;
        }

        return $eft;
    }

    /**
     * Get tab rows.
     *
     * @param \format_multitopic\section_info_extra[] $sectionextralist
     * @param int[] $activetab
     * @param bool $showeditor
     * @return int[][]
     */
    protected function get_tab_rows(array $sectionextralist, array $activetab, bool $showeditor): array {
        $format = $this->format;
        $tabsrow = [[], []];
        $depthmax = -1;
        $depthactive = -1;

        foreach ($sectionextralist as $sectionextra) {
            if ($sectionextra->sectionbase->component || ($sectionextra->levelsan >= 2)) {
                continue;
            }
            if (($sectionextra->levelsan < 0) || ($sectionextra->id == $activetab[$sectionextra->levelsan])) {
                for (
                    $level = $sectionextra->levelsan;
                    ($level < 2) && (($level < 0) || ($sectionextra->id == $activetab[$level]));
                    $level++
                ) {
                    $depthactive = $level;
                }
            } else {
                $depthactive = min($depthactive, $sectionextra->levelsan - 1);
            }
            if (
                ($sectionextra->levelsan >= 0) && ($sectionextra->levelsan <= $depthactive + 1)
                && $format->is_section_visible($sectionextra->sectionbase)
            ) {
                array_push($tabsrow[$sectionextra->levelsan], $sectionextra->id);
                $depthmax = max($depthmax, $sectionextra->levelsan);
            }
        }

        for ($level = 0; $level <= min($depthmax + ($showeditor ? 1 : 0), 1); $level++) {
            $parentid = ($level == 0) ? $format->fmtrootsectionid : $activetab[$level - 1];

            // Si estamos en la fila 2 (nivel 1), y la sección padre no tiene contenido propio
            // y no estamos en modo edición, NO insertamos la pestaña en la fila 2.
            $hideparenttab = ($level == 1)
                && !$showeditor
                && !empty($tabsrow[1])
                && isset($sectionextralist[$parentid])
                && !$this->section_has_content($sectionextralist[$parentid]);

            if (!$hideparenttab) {
                array_unshift($tabsrow[$level], $parentid);
            }

            if ($showeditor) {
                array_push($tabsrow[$level], "add" . $parentid);
            }
        }

        return $tabsrow;
    }

    /**
     * Export tab data so it can be used as the context for a mustache template.
     *
     * @param \format_multitopic\section_info_extra|string $sectionextra
     * @param int $level
     * @param bool $canaddmore
     * @param renderer_base $output typically, the renderer that's calling this function
     * @return \stdClass data context for a mustache template
     */
    public function export_tab_for_template(
        \format_multitopic\section_info_extra|string $sectionextra,
        int $level,
        bool $canaddmore,
        renderer_base $output
    ): stdClass {
        $format = $this->format;
        $course = $format->get_course();

        if (is_object($sectionextra)) {
            // Make tab.
            $thissection = $sectionextra->sectionbase;
            $sectionname = get_section_name($course, $thissection);
            $tabname = $sectionname;

            // Si la sección padre se renderiza en una fila hija (subpestañas nivel 1),
            // usar un alias visual ("Inicio") para no duplicar el nombre con la fila superior.
            if ($level == 1 && $sectionextra->levelsan <= 0) {
                $tabname = get_string('parenttabalias', 'format_multitopic');
            }

            $url = course_get_url($course, $thissection);

            // Si es una pestaña de fila 1 (nivel 0) que agrupa subsecciones pero NO tiene contenido propio,
            // enlazar directamente a su primera subsección visible (a menos que estemos en modo edición).
            if ($level == 0 && !$format->show_editor() && !empty($sectionextra->hassubsections) && !$this->section_has_content($sectionextra)) {
                $firstchild = $this->get_first_child_section_extra($sectionextra);
                if ($firstchild) {
                    $url = course_get_url($course, $firstchild->sectionbase);
                }
            }

            $inactive = !(($thissection->section == 0) || $thissection->uservisible);
            if ($inactive) {
                $url = null;
            }

            $eft = (object)[
                'id' => "tab_id_{$thissection->id}_l{$level}",
                'link' => $url?->out(false),
                'text' => html_writer::tag('div', $tabname, ['class' =>
                    'tab_content'
                    . (($sectionextra->currentnestedlevel >= $level) ? ' marker' : '')
                    . ((!$thissection->visible || !$thissection->available) && ($thissection->section != 0)
                        || ($level > $sectionextra->pagedepthdirect) ? ' dimmed' : ''),
                    'data-itemid' => $thissection->id,
                ]),
                'title' => $sectionname,
                'inactive' => $inactive,
                'level' => $level,
                'sectionid' => $thissection->id,
            ];
        } else {
            // Make "add" tab.
            preg_match('/^add(\d+)$/', $sectionextra, $matches);
            $parentid = $matches[1];
            $straddsection = get_string_manager()->string_exists('addsectionpage', 'format_' . $course->format) ?
                                get_string('addsectionpage', 'format_' . $course->format)
                                : get_string('addsection', 'core_courseformat');
            $params = [
                'courseid' => $course->id,
                'increase' => true,
                'sesskey' => sesskey(),
                'insertparentid' => $parentid,
                'insertlevel' => $level,
                'returnurl' => new url("/course/view.php?id={$course->id}"
                    . (($format->get_sectionid() != $format->fmtrootsectionid) ?
                    "&sectionid={$format->get_sectionid()}" : "")),
            ];
            $url = new url('/course/format/multitopic/_course_changenumsections.php', $params);
            $icon = $output->pix_icon('t/switch_plus', $straddsection, 'moodle');
            $inactive = !$canaddmore;
            if ($inactive) {
                $url = null;
            }

            $eft = (object)[
                'id' => "tab_id_{$parentid}_l{$level}_add",
                'link' => $url?->out(false),
                'text' => $icon,
                'title' => s($straddsection),
                'inactive' => $inactive,
                'level' => $level,
            ];
        }

        return $eft;
    }
}
