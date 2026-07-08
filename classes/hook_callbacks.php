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
use mod_quiz\quiz_attempt;

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
        global $PAGE, $DB, $USER;

        // Only act on quiz attempt and review pages.
        if (!in_array($PAGE->pagetype, self::TARGET_PAGETYPES, true)) {
            return;
        }

        // Default to the logged-in user. On attempt.php this IS the student
        // taking the quiz, so the watermark is always correct here even if the
        // attempt lookup below cannot run for any reason.
        $idnumber = $USER->idnumber ?? '';

        // On review.php the attempt may belong to a different user (e.g. a
        // teacher reviewing a student's attempt), so prefer the attempt owner.
        // Catch *any* error/exception so a lookup problem never blanks the
        // watermark — we simply fall back to the logged-in user's ID number.
        $attemptid = optional_param('attempt', 0, PARAM_INT);
        if ($attemptid) {
            try {
                $attemptobj = quiz_attempt::create($attemptid);
                $owner = $DB->get_field('user', 'idnumber', ['id' => $attemptobj->get_userid()]);
                if ($owner !== false) {
                    $idnumber = $owner;
                }
            } catch (\Throwable $e) {
                // Keep the logged-in user's ID number as the fallback.
                debugging('local_quizidnumber: attempt lookup failed: ' . $e->getMessage(),
                    DEBUG_DEVELOPER);
            }
        }

        if (trim((string) $idnumber) === '') {
            $idnumber = get_string('noidnumber', 'local_quizidnumber');
        }

        $label = get_string('studentid', 'local_quizidnumber');
        $text  = s($label . ': ' . $idnumber);

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
