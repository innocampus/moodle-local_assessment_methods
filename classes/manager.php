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

namespace local_assessment_methods;

use cache;
use cache_session;
use cache_store;
use coding_exception;
use core\update\checker_exception;
use dml_exception;
use moodle_exception;
use context_system;
use moodle_url;
use local_assessment_methods\helper;
use stdClass;

defined('MOODLE_INTERNAL') || die();
global $CFG;

//require(__DIR__.'/../../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/adminlib.php');


class manager {

    const ACTION_DELETE_METHOD = 'delete';
    const ACTION_VIEW_FORM = 'edit';
    const ACTION_VIEW_ADMIN_PAGE = 'admin';
    const ACTION_VIEW_REPORT = 'report';

    const VISIBILITY_HIDDEN = 0;
    const VISIBILITY_ADMINS_ONLY = 1; // TODO do
    const VISIBILITY_ALL = 2;

    /**
     * @param string $action
     * @throws moodle_exception
     */
    public static function execute(string $action) {
        $method = optional_param('method', null, PARAM_ALPHANUMEXT);
        $context = context_system::instance();
        switch ($action) {
            case self::ACTION_DELETE_METHOD:
                // TODO confirmation form?
                require_sesskey();
                require_capability('local/assessment_methods:manage', $context);
                helper::delete_method($method);
                redirect(helper::get_admin_setting_url());
                break;
            case self::ACTION_VIEW_FORM:
                require_capability('local/assessment_methods:manage', $context);
                self::process_form($method);
                break;
            case self::ACTION_VIEW_ADMIN_PAGE:
                require_capability('local/assessment_methods:manage', $context);
                self::make_page_header($action);
                self::show_admin_page();
                break;
            case self::ACTION_VIEW_REPORT:
            default:
                require_capability('local/assessment_methods:view_report', $context);
                self::make_page_header($action);
                //self::process_search($action);
                self::show_report();
        }
    }

    /**
     * @param string $for_action
     * @param string|null $method
     * @throws \require_login_exception
     * @throws \required_capability_exception
     * @throws coding_exception
     * @throws dml_exception
     * @throws moodle_exception
     */
    private static function make_page_header(string $for_action, ?string $method = null) {
        global $PAGE, $OUTPUT;

        $url = new moodle_url(helper::PLUGIN_PATH . 'index.php', ['action' => $for_action]);
        $context = context_system::instance();

        $PAGE->set_pagelayout("admin");
        $PAGE->set_context($context);

        switch ($for_action) {
            case self::ACTION_VIEW_FORM:
                $title = $method
                    ? helper::get_string('edit_method')
                    : helper::get_string('create_method');
                if ($method) {
                    $url->param('method', $method);
                }
                // Test by CG
                $PAGE->set_title($title);
                $PAGE->set_url($url);
                echo $OUTPUT->header();
                break;
            case self::ACTION_VIEW_ADMIN_PAGE:
                $title = helper::get_string('assessment_method_list');
                // Test by CG
                $PAGE->set_title($title);
                $PAGE->set_url($url);
                echo $OUTPUT->header();
                break;
            case self::ACTION_VIEW_REPORT:
            default:
                $title = helper::get_string('report');
                // Test by CG
                $PAGE->set_title($title);
                $PAGE->set_url($url);
                echo $OUTPUT->header();
                break;
            /*default:
                throw new \moodle_exception('unknown_action', 'local_assessment_methods');*/
        }

        // commented out by CG for testing purposes
        //$PAGE->set_title($title);
        //$PAGE->set_url($url);
        //echo $OUTPUT->header();
    }

    /**
     * @throws coding_exception
     * @throws moodle_exception
     */
    private static function show_admin_page() {
        global $PAGE, $OUTPUT;

        /** @var output\renderer $renderer */
        $renderer = $PAGE->get_renderer('local_assessment_methods');
        echo $renderer->method_link();
        $table = new output\setting_table(helper::get_methods(), true, true);
        if (!$table->is_empty()) {
            echo $renderer->render($table);
            echo $renderer->method_link();
        } else {
            echo $OUTPUT->notification(helper::get_string('setting_table_empty_notice'), 'info');
        }
        echo $OUTPUT->footer();
    }

/*    private static function get_data_from_db(): array {
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
        //TODO fill $data DONE by Christian Gillen
        $data = $DB->get_records_sql("SELECT ${fields}
                                            FROM ${from}
                                            WHERE ${where}");
        return $data;
    }*/

/*    private static function build_and_render_table($data) {
        global $PAGE, $OUTPUT;
        $renderer = $PAGE->get_renderer('local_assessment_methods');
        $table = new output\report($data);
        if (!$table->is_empty()) {
            // the table filled with data before is rendered
            echo $renderer->render($table);
        } else {
            echo $OUTPUT->notification(helper::get_string('report_table_empty_notice'), 'info');
        }
    }*/

    private static function show_report() {
        global $PAGE, $OUTPUT;

        /** @var output\renderer $renderer */
        $renderer = $PAGE->get_renderer('local_assessment_methods');

        //TODO fill $data DONE by Christian Gillen with helper function right above
        //self::build_and_render_table(self::get_data_from_db());

        $search = optional_param('search', '', PARAM_TEXT);
        //admin_externalpage_setup('assessment_methods', '', ['search' => $search], '', ['pagelayout' => 'report']);

        $mform = new form\search();
        /*if ($mform->is_cancelled()) {
            redirect(helper::get_report_url());
            //self::execute(self::ACTION_VIEW_ADMIN_PAGE);
            //self::show_report();
            //redirect(helper::get_admin_setting_url());
        }*/
        /*else {
            redirect(helper::get_admin_setting_url());
        }*/

        echo $OUTPUT->heading(helper::get_string('pluginname'));

        /** @var cache_session $cache */
        $cache = cache::make_from_params(cache_store::MODE_SESSION, 'assessment_methods', 'search');

        if (!empty($search)) {
            $searchdata = (object) ['setting' => $search];
        } else {
            $searchdata = $cache->get('data');
        }

        $mform->set_data($searchdata);

        $searchclauses = [];

        if ($mform->is_cancelled()) {
            redirect(helper::get_report_url());
        }

        $data = ($mform->is_submitted() ? $mform->get_data() : fullclone($searchdata));
        if ($data instanceof stdClass) {
            if (!empty($data->assessment_methods)) {
                $searchclauses[] = "assessment_methods:{$data->assessment_methods}";
            }
            if (!empty($data->activities)) {
                if ($data->activities == "1") {
                    $searchclauses[] = "assign";
                } else {
                    $searchclauses[] = "quiz";
                }
            }
            if (!empty($data->datefrom)) {
                $searchclauses[] = "datefrom:{$data->datefrom}";
            }
            if (!empty($data->dateto)) {
                $dateto = $data->dateto + DAYSECS - 1;
                $searchclauses[] = "dateto:{$dateto}";
            }
            if (!empty($data->course)) {
                $searchclauses[] = $data->course;
            }
            if (!empty($data->user)) {
                $searchclauses[] = "user:{$data->user}";
            }
            unset($data->submitbutton);
            $cache->set('data', $data);
        }

        $mform->display();

        $table = new output\report_table(implode(' ', $searchclauses));

        if (!$table->is_downloading()) {
            // Only print headers if not asked to download data.
            // Print the page header.
            $PAGE->set_title(helper::get_string('pluginname'));
            $PAGE->set_heading(helper::get_string('pluginname'));
            $PAGE->navbar->add(helper::get_string('pluginname'), new moodle_url('/index.php'));
            //echo $OUTPUT->header();
        }

        $table->define_baseurl($PAGE->url);

        echo $renderer->render($table);

        if (!$table->is_downloading()) {
            echo $OUTPUT->footer();
        }
    }

    /**
     * @throws moodle_exception
     */
    private static function process_form($method) {
        global $OUTPUT;

        $form = new output\method_form(
            helper::get_form_action_url($method),
            ['edit' => !empty($method)]
        );
        if ($form->is_cancelled()) {
            redirect(helper::get_admin_setting_url());
        } else if ($data = $form->get_data()) {
            $translations = [];
            $lang_codes = array_keys(get_string_manager()->get_list_of_languages());
            foreach ($lang_codes as $lc) {
                $name = $form::get_translation_element_name($lc);
                if (isset($data->$name) && !empty($data->$name)) {
                    $translations[$lc] = $data->$name;
                }
            }
            helper::add_or_update_method($method ?? $data->method_id, $translations, $data->visibility);
            redirect(helper::get_admin_setting_url());
        } else {
            self::make_page_header(self::ACTION_VIEW_FORM, $method);
            if ($method) {
                $data = new \stdClass();
                $data->method_id = $method;
                $methods = helper::get_methods();
                if (!empty($methods[$method])) {
                    $data->visibility = $methods[$method]['visibility'];
                    foreach ($methods[$method]['translations'] as $lang => $name) {
                        $el = output\method_form::get_translation_element_name($lang);
                        $data->$el = $name;
                    }
                }
                $form->set_data($data);
            }
            $form->display();
            echo $OUTPUT->footer();
        }
    }

}
