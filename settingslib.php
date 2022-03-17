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
 * Settings lib
 *
 * @package    local_assessment_methods
 * @copyright  2022 Jan Eberhardt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_assessment_methods\output\setting_table;

class admin_setting_local_assessment_methods extends admin_setting {

    /**
     * Returns current value of this setting
     * @return mixed array or string depending on instance, NULL means not set yet
     */
    public function get_setting()
    {
        return \local_assessment_methods\helper::get_setting();
    }

    /**
     * Store new setting
     *
     * @param mixed $data string or array, must not be NULL
     * @return string empty string if ok, string error message otherwise
     */
    public function write_setting($data)
    {
        // settings write will be handled from an external page
        return "";
    }

    /**
     * @param mixed $data
     * @param string $query
     * @return string
     * @throws coding_exception
     */
    public function output_html($data, $query = '')
    {
        global $PAGE, $USER;

        /** @var local_assessment_methods\output\renderer $renderer */
        $renderer = $PAGE->get_renderer('local_assessment_methods');
        $output = $renderer->method_link();

        try {
            $perm = has_capability('local_assessment_methods/manage', context_system::instance());
        } catch (dml_exception | coding_exception $_) {
            $perm = false;
        }
        $output .= $renderer->render(new setting_table(json_decode($data), $USER->lang, $perm, $perm));

        return $output;
    }
}
