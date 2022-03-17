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

use local_assessment_methods\helper;
use moodle_page;
use html_table;
use html_table_row;
use html_table_cell;
use html_writer;

/**
 * Class table
 * @package local_assessment_methods\output
 */
class setting_table implements \renderable {

    /** @var array $data */
    private $data;
    /** @var string $lang */
    private $lang;
    /** @var bool $canedit */
    private $canedit;
    /** @var bool $candelete */
    private $candelete;

    function __construct(array $data, string $lang, bool $canedit, bool $candelete) {
        $this->data = $data;
        $this->lang = $lang;
        $this->canedit = $canedit;
        $this->candelete = $candelete;
    }

    /**
     * @return html_table
     * @throws \moodle_exception
     */
    public function create() {
        $table = new html_table();
        $table->head = $this->create_assessment_methods_table_head();
        $rows = [];
        foreach (array_keys($this->data) as $method) {
            $rows[] = $this->create_assessment_methods_table_row_data($method);
        }
        $table->data = $rows;

        return $table;
    }

    // HELPER METHODS

    /**
     * @return array
     */
    private function create_assessment_methods_table_head() {
        $head = [
            'ID',
            'Description'             //TODO translate
        ];
        if ($this->canedit || $this->candelete) {
            $head[] = 'Actions';            //TODO translate
        }
        return $head;
    }

    /**
     * @param $method_id
     * @return html_table_row
     * @throws \moodle_exception
     */
    private function create_assessment_methods_table_row_data($method_id) {
        $row = new html_table_row();

        $row->cells = [
            new html_table_cell($method_id),
            new html_table_cell($this->data[$method_id][$this->lang])
        ];
        if ($this->canedit || $this->candelete) {
            $cell = new html_table_cell();
            $cell->text = "";
            if ($this->canedit) {
                $icon = new \pix_icon('edit', 'Edit');  //TODO translate
                $cell->text .= $link = html_writer::link(helper::get_method_edit_url(), "$icon");
            }
            if ($this->candelete) {
                $icon = new \pix_icon('delete', 'Delete');  //TODO translate
                $cell->text .= $link = html_writer::link(helper::get_method_delete_url(), "$icon");
            }
            $row->cells[] = $cell;
        }

        return $row;
    }
}