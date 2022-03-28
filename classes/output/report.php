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

use html_table;
use moodle_page;

defined('MOODLE_INTERNAL') || die();

/**
 * Class renderer
 * @package local_assessment_methods\output
 * @property-read \stdClass $data
 */
class report implements \renderable {
    //TODO make this work!

    /** @var array $data */
    private $data;

    function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * @return html_table
     */
    public function table(): html_table {
        $table = new html_table();
        $table->head = $this->create_table_header();
        $table->data = [];
        foreach (array_keys($this->data) as $row_id) {
            $table->data[] = $this->create_table_row($row_id);
        }
        return $table;
    }

    private function create_table_header(): array {
        return [];
    }

    private function create_table_row($row_id): ?\html_table_row {
        return null;
    }

    static function filter_form(): ?\moodleform {
        return null;
    }

    static function quiz_svg(): string {
        return '';
    }

    static function assign_svg(): string {
        return '';
    }

}
