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

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/searchlib.php');
require_once($CFG->libdir . '/tablelib.php');

use html_table;
use local_assessment_methods\form\search;
use local_assessment_methods\helper;
use moodle_page;

/**
 * Class report
 * @package local_assessment_methods\output
 * @property-read \stdClass $data
 */
class report extends \table_sql implements \renderable {
    //TODO make this work! DONE by Christian Gillen

    /** @var array $data */
    private $data;

    function __construct(array $data) {
        parent::__construct('local-assessment-methods-report');
        $this->data = $data;
        $this->sortable(true, 'duedate_timeclose', SORT_ASC);
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

    public function is_empty(): bool {
        return empty($this->data);
    }

    private function create_table_header(): array {
        return array(
            helper::get_string('assign_quiz_name'),
            helper::get_string('assessment_method'),
            helper::get_string('method_id'),
            helper::get_string('duedate_timeclose'),
            helper::get_string('course'),
            helper::get_string('user')
        );
    }

    private function create_table_row($row_id): ?\html_table_row {
        $data = $this->data[$row_id];
        // from unix time stamp to a readable date and time format
        $date = userdate($data->over);

        // from course module id and course module module to the relative assign or quiz URL
        $url = helper::get_assquiz_url($data->cmid, $data->modname);
        $name = \html_writer::link($url, $data->name);

        // from the method code word to the respective translation
        $method = helper::get_method_options($data->method, null)[$data->method];

        // from course id to the relative course URL
        $url = helper::get_course_url($data->cid);
        $course = \html_writer::link($url, $data->cname);

        // from user names to the relative user URL
        $url = helper::get_user_url($data->uid);
        $fullname = $data->aname ?: $data->fname . ' ' . $data->lname;
        $user = \html_writer::link($url, $fullname);

        // make the row
        $table_row = new \html_table_row([
            $name, $method, $data->method, $date, $course, $user
        ]);

        return $table_row;
    }

    static function filter_form(): ?\moodleform {
        return new search();
    }

    static function quiz_svg(): string {
        return '';
    }

    static function assign_svg(): string {
        return '';
    }

}
