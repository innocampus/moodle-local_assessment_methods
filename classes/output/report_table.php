<?php

/**
 * Report table class.
 *
 * @package    local_assessment_methods
 * @author     Christian Gillen <c.gillen@tu-berlin.de>
 * @copyright  2021 Technische Universität Berlin <info@isis.tu-berlin.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_assessment_methods\output;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/searchlib.php');
require_once($CFG->libdir . '/tablelib.php');

use local_assessment_methods\helper;
use moodle_page;

/**
 * Class report_table
 * @package local_assessment_methods\output
 */
class report_table extends \table_sql implements \renderable {

    /** @var string $search */
    protected $search;

    /**
     * Constructor
     *
     * @param string $uniqueid
     */
    public function __construct(string $search) {
        parent::__construct('local-assessment-methods-report-table');

        $this->search = trim($search);

        // Define columns.
        $columns = array(
            'name' => helper::get_string('assign_quiz_name'),
            'method'=> helper::get_string('assessment_method'),
            'method_id'=> helper::get_string('method_id'),
            'over' => helper::get_string('duedate_timeclose'),
            'cname' => helper::get_string('course'),
            'uid' => helper::get_string('user')
        );
        $this->define_columns(array_keys($columns));
        $this->define_headers(array_values($columns));

        // Table configuration.
        $this->set_attribute('id', $this->uniqueid);
        $this->set_attribute('cellspacing', '0');

        $this->sortable(true, 'over', SORT_DESC);

        $this->initialbars(false);
        $this->collapsible(false);

        $this->useridfield = 'u.id';

        $download = optional_param('download', '', PARAM_ALPHA);
        $this->is_downloading($download, 'assessment_methods', 'assessment_methods');

        // Initialize table SQL properties.
        $this->init_sql();
    }

    /**
     * Initializes table SQL properties
     *
     * @return void
     */
    protected function init_sql() {
        global $DB;

        $common_fields = 'am.id as amid, cm.id as cmid, m.name as modname, am.method as method, am.method as method_id,
                            c.id as cid, c.shortname as cname, u.id as uid, u.firstname as fname, u.lastname as lname, 
                            u.alternatename as aname, ';
        $fields = $common_fields . "CASE m.name
                                        WHEN 'quiz' THEN q.timeclose
                                        WHEN 'assign' THEN a.duedate
                                    END as over,
                                    CASE m.name
                                        WHEN 'quiz' THEN q.name
                                        WHEN 'assign' THEN a.name
                                    END as name";
        $from = '{local_assessment_methods} am
                 JOIN {course_modules} cm ON cm.id = am.cmid
                 JOIN {course} c ON c.id = cm.course
                 JOIN {modules} m ON m.id = cm.module
                 JOIN {user} u ON u.id = am.userid
                 FULL JOIN {assign} a ON a.id = cm.instance
                 FULL JOIN {quiz} q ON q.id = cm.instance';
        $where = "1=1";

        if (!empty($this->search)) {

            $searchstring = str_replace(["\\\"", 'setting:'], ["\"", 'subject:'], $this->search);

            $parser = new \search_parser();
            $lexer = new \search_lexer($parser);

            if ($lexer->parse($searchstring)) {
                $parsearray = $parser->get_parsed_array();

                // Data fields should contain both value/oldvalue.
                $datafields = $DB->sql_concat_join("':'", ['modname', 'method']);

                list($where, $params) = search_generate_SQL($parsearray, $datafields, 'method', 'am.userid', 'uid',
                    'fname', 'lname', 'over', 'amid');
            }
        }

        $this->set_sql($fields, $from, $where);
        $this->set_count_sql('SELECT COUNT(1) FROM ' . $from . ' WHERE ' . $where);

        echo "Report Table!";

    }

    /**
     * Format assign_quiz_name column
     *
     * @param stdClass $row
     * @return string
     */
    public function col_name($row) {
        $url = helper::get_assquiz_url($row->cmid, $row->modname);
        return \html_writer::link($url, $row->name);
    }

    /**
     * Format assessment_method column
     *
     * @param \stdClass $row
     * @return string
     */
    public function col_method(\stdClass $row) {
        return helper::get_method_options($row->method, null)[$row->method];
    }

    /**
     * Format duedate_timeclose column
     *
     * @param stdClass $row
     * @retrun string
     */
    public function col_over(\stdClass $row) {
        return userdate($row->over);
    }

    /**
     * Format course column
     *
     * @param stdClass $row
     * @return string
     */
    public function col_cname($row) {
        $url = helper::get_course_url($row->cid);
        return \html_writer::link($url, $row->cname);
    }

    /**
     * Format user column
     *
     * @param stdClass $row
     * @return string
     */
    public function col_uid($row) {
        $url = helper::get_user_url($row->uid);
        $fullname = $row->aname ?: $row->fname . ' ' . $row->lname;
        return \html_writer::link($url, $fullname);
    }
}
