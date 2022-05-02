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
use local_assessment_methods\helper;
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
        return array(get_string('method_id', 'local_assessment_methods'), get_string('duedate_timeclose', 'local_assessment_methods'), get_string('assign_quiz_name', 'local_assessment_methods'), get_string('assessment_method', 'local_assessment_methods'), get_string('course', 'local_assessment_methods'), get_string('user', 'local_assessment_methods'));
    }

    private function create_table_row($row_id): ?\html_table_row
    {
        //echo nl2br("PAUSE \n");

        // from unix time stamp to a readable date and time format
        $date = $this->data[$row_id]->over;
        $this->data[$row_id]->over = userdate($date);

        /*
         * from course module id and course module module to the relative
         * assign or quiz URL
         */
        $id = $this->data[$row_id]->cmid;
        $mod = $this->data[$row_id]->cmmod;
        unset($this->data[$row_id]->cmid);
        unset($this->data[$row_id]->cmmod);
        $name = $this->data[$row_id]->name;
        $url = helper::get_assquiz_url($id, $mod);
        $this->data[$row_id]->name = \html_writer::link($url, $name);

        // from the method code word to the respective translation
        //$method = $this->data[$row_id]->method;
        //$this->data[$row_id]->method = helper::get_methods();

        //var_dump($this->data[$row_id]->method);

        // from course id to the relative course URL
        $id = $this->data[$row_id]->cid;
        unset($this->data[$row_id]->cid);
        $name = $this->data[$row_id]->cname;
        $url = helper::get_course_url($id);
        $this->data[$row_id]->cname = \html_writer::link($url, $name);

        // from user id to the relative user URL
        $id = $this->data[$row_id]->uid;
        unset($this->data[$row_id]->uid);
        $name = $this->data[$row_id]->uname;
        $url = helper::get_user_url($id);
        $this->data[$row_id]->uname = \html_writer::link($url, $name);

        $table_row = new \html_table_row((array)$this->data[$row_id]);

        return $table_row;
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
