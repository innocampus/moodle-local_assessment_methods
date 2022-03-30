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
 * @copyright  2022 Technische Universität Berlin <info@isis.tu-berlin.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_assessment_methods\output;


defined('MOODLE_INTERNAL') || die();

use core_tag\output\tag;
use local_assessment_methods\helper;
use local_assessment_methods\manager;
use moodle_exception;
use moodle_page;
use html_table;
use html_table_row;
use html_table_cell;
use html_writer;
use pix_icon;

/**
 * Class table
 * @package local_assessment_methods\output
 */
class setting_table implements \renderable {

    private $data;
    private $canedit;
    private $candelete;
    private $languages;

    function __construct(array $data, bool $canedit, bool $candelete) {
        $this->data = $data;
        $this->canedit = $canedit;
        $this->candelete = $candelete;
        /** @var \core_string_manager $lang_man */
        $string_man = get_string_manager();
        $this->languages = $string_man->get_list_of_translations();
    }

    /**
     * @return html_table
     * @throws moodle_exception
     */
    public function create(): html_table {
        $table = new html_table();
        $table->head = $this->create_assessment_methods_table_head();
        $rows = [];
        foreach ($this->data as $method => $method_data) {
            $rows[] = $this->create_assessment_methods_table_row_data($method, $method_data);
        }
        $table->data = $rows;
        return $table;
    }

    public function is_empty(): bool {
        return empty($this->data);
    }

    // HELPER METHODS

    /**
     * @return array
     * @throws moodle_exception
     */
    private function create_assessment_methods_table_head(): array {
        $head = [helper::get_string('method_id')];
        foreach ($this->languages as $language) {
            $head[] = $language;
        }
        if ($this->canedit || $this->candelete) {
            $head[] = helper::get_string('actions');
        }
        return $head;
    }

    /**
     * @param $method_id
     * @param $method_data
     * @return html_table_row
     * @throws moodle_exception
     */
    private function create_assessment_methods_table_row_data($method_id, $method_data): html_table_row {
        global $OUTPUT;

        $row = new html_table_row();
        if ($method_data['visibility'] !== manager::VISIBILITY_ALL) {
            $row->attributes['class'] .= ' text-muted';
        }
        $row->cells = [new html_table_cell($method_id)];
        foreach ($this->languages as $lc => $_) {
            $row->cells[] = new html_table_cell($method_data['translations'][$lc] ?? '-');
        }
        if ($this->canedit || $this->candelete) {
            $cell = new html_table_cell();
            $cell->text = "";
            if ($this->canedit) {
                $icon = new pix_icon('t/edit', get_string('edit'));
                $cell->text .= html_writer::link(helper::get_method_edit_url($method_id), $OUTPUT->render($icon));
            }
            if ($this->candelete) {
                $icon = new pix_icon('t/delete', get_string('delete'));
                $cell->text .= html_writer::link(helper::get_method_delete_url($method_id), $OUTPUT->render($icon));
            }
            $row->cells[] = $cell;
        }
        return $row;
    }
}