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
 * Event handlers.
 *
 * @package    local_assessment_methods
 * @copyright  2022 Lars Bonczek, innoCampus, TU Berlin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_assessment_methods;

use core\event\course_module_deleted;

defined('MOODLE_INTERNAL') || die();

/**
 * Event handlers.
 *
 * @copyright  2022 Lars Bonczek, innoCampus, TU Berlin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {
    /**
     * Course module (activity) has been deleted.
     *
     * @param course_module_deleted $event
     * @throws \dml_exception
     */
    static public function course_module_deleted(course_module_deleted $event) {
        global $DB;
        $DB->delete_records('local_assessment_methods', ['cmid' => $event->objectid]);
    }
}
