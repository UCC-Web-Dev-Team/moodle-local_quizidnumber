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

/**
 * Hook callbacks for local_quizidnumber.
 *
 * @package    local_quizidnumber
 * @copyright  2026 Clemence Ayekple
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_quizidnumber;

use core\hook\output\before_standard_top_of_body_html_generation;

/**
 * Callbacks that inject the student ID number into quiz pages.
 */
class hook_callbacks {

    /** @var string[] Page types where the ID number should be shown. */
    private const TARGET_PAGETYPES = ['mod-quiz-attempt', 'mod-quiz-review'];

    /** @var int Number of repeated watermark tiles used to fill the page. */
    private const WATERMARK_TILES = 240;

    /**
     * Inject the attempt owner's ID number near the top of the body
     * on quiz attempt and review pages.
     *
     * @param before_standard_top_of_body_html_generation $hook
     */
    public static function before_standard_top_of_body_html(
        before_standard_top_of_body_html_generation $hook
    ): void {
        global $PAGE, $USER;

        // Only act on quiz attempt and review pages.
        if (!in_array($PAGE->pagetype, self::TARGET_PAGETYPES, true)) {
            return;
        }

        // Only students see the watermark. Anyone who can grade or view the
        // quiz reports (teachers, managers, admins) is excluded, so a teacher
        // reviewing a student's attempt is never watermarked with an ID number.
        if (has_capability('mod/quiz:viewreports', $PAGE->context)) {
            return;
        }

        // A non-privileged user can only ever reach the attempt or review page
        // for their *own* attempt, so the watermark is simply their ID number.
        $idnumber = trim((string) ($USER->idnumber ?? ''));
        if ($idnumber === '') {
            $idnumber = get_string('noidnumber', 'local_quizidnumber');
        }

        // Show just the ID number itself — no label prefix.
        $text = s($idnumber);

        // Build a tiled, repeated watermark that the CSS rotates diagonally
        // across the whole page. The number of tiles is generous so the
        // pattern fills large screens; surplus tiles are simply clipped.
        $tiles = '';
        for ($i = 0; $i < self::WATERMARK_TILES; $i++) {
            $tiles .= \html_writer::span($text, 'local-quizidnumber-wm-item');
        }

        $html = \html_writer::div(
            $tiles,
            'local-quizidnumber-watermark',
            ['aria-hidden' => 'true']
        );

        $hook->add_html($html);
    }
}
