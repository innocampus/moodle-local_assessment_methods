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
 * @package    local_assessment_methods
 *
 * @author     Jan Eberhardt <jan.eberhardt@tu-berlin.de>
 * @copyright  2021 Technische Universität Berlin <info@isis.tu-berlin.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_assessment_methods\output;

use moodle_page;

defined('MOODLE_INTERNAL') || die();

/**
 * Class renderer
 * @package local_assessment_methods\output
 * @property-read \stdClass $data
 */
class renderer extends \plugin_renderer_base {

    protected $data;

    function __construct(moodle_page $page, string $target)
    {
        global $DB;

        parent::__construct($page, $target);

        //TODO: Fill $this>data
    }

    function report_header() {
        //TODO
    }

    function report_form() {
        //TODO
    }

    function report_quiz_svg() {
        //TODO
    }

    function report_assign_svg() {
        //TODO
    }
}