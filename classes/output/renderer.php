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

use local_assessment_methods\helper;
use coding_exception;
use html_writer;
use moodle_exception;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base {

    /**
     * @return string
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function method_link(): string {
        return html_writer::link(\local_assessment_methods\helper::get_method_add_url(),
            helper::get_string('create_method_button_text'), ['class' => 'btn btn-secondary mb-3']);
    }

    /**
     * @param setting_table $table
     * @return string
     * @throws moodle_exception
     */
    public function render_setting_table(setting_table $table): string {
        return html_writer::table($table->create());
    }

    /**
     * @param report $report
     * @return string
     */
    public function render_report(report $report): string {
        return html_writer::table($report->table());
    }

    /** @var int Page size for displaying report table. */
    const REPORT_TABLE_PAGESIZE = 10;

    /**
     * Return output to be rendered to page
     *
     * @param report_table $table
     * @return string HTML rendered table
     */
    protected function render_report_table(report_table $table) {
        ob_start();

        $table->out(self::REPORT_TABLE_PAGESIZE, false);
        $output = ob_get_contents();

        ob_end_clean();

        return $output;
    }
}