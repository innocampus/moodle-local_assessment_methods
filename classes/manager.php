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

use coding_exception;
use core\update\checker_exception;
use dml_exception;
use moodle_exception;
use context_system;
use moodle_url;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->libdir . '/formslib.php');

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
                break;
            case self::ACTION_VIEW_ADMIN_PAGE:
                $title = helper::get_string('assessment_method_list');
                break;
            case self::ACTION_VIEW_REPORT:
                $title = helper::get_string('report');
                break;
            default:
                throw new \moodle_exception('unknown_action', 'local_assessment_methods');
        }

        $PAGE->set_title($title);
        $PAGE->set_url($url);

        echo $OUTPUT->header();
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

    private static function show_report() {
        global $PAGE, $OUTPUT, $DB;

        /** @var output\renderer $renderer */
        $renderer = $PAGE->get_renderer('local_assessment_methods');

        $data = [];
        //TODO fill $data DONE by Christian Gillen

        $common_fields = 'am.id as amid, cm.module as cmid, m.name as modname, am.method as method, c.id as cid, c.shortname as cname, u.id as uid, u.firstname as fname, u.lastname as lname, u.alternatename as aname';
        $data = $DB->get_records_sql("SELECT ${common_fields}, a.duedate as over, a.name as name
                                            FROM {local_assessment_methods} am
                                            JOIN {course_modules} cm ON cm.id = am.cmid
                                            JOIN {course} c ON c.id = cm.course
                                            JOIN {modules} m ON m.id = cm.module
                                            JOIN {user} u ON u.id = am.userid
                                            JOIN {assign} a ON a.id = cm.instance
                                            WHERE m.name = 'assign'
                                      UNION SELECT ${common_fields}, q.timeclose as over, q.name as name
                                            FROM {local_assessment_methods} am
                                            JOIN {course_modules} cm ON cm.id = am.cmid
                                            JOIN {course} c ON c.id = cm.course
                                            JOIN {modules} m ON m.id = cm.module
                                            JOIN {quiz} q ON q.id = cm.instance
                                            JOIN {user} u ON u.id = am.userid
                                            WHERE m.name = 'quiz'");

        echo $renderer->render(new output\report($data));
        echo $OUTPUT->footer();
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
