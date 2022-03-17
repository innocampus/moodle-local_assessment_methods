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
use context_system;

defined('MOODLE_INTERNAL') || die();

class renderer extends \renderer_base {

    /**
     * @return mixed
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function method_link() {
        $link = \html_writer::link(\local_assessment_methods\helper::get_method_add_url(),
            get_string('createmethodbuttontext', 'local_assessment_methods'),
            ['class' => 'btn btn-secondary mb-3']);

        return $link;
    }

    /**
     * @param setting_table $table
     * @return string
     * @throws \moodle_exception
     */
    public function render_setting_table(setting_table $table) {
        return \html_writer::table($table->create());
    }

    public function render_report(report $report) {
        return \html_writer::table($report->table());
    }

}