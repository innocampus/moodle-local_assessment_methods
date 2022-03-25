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
        switch ($action) {
            case self::ACTION_DELETE_METHOD:
                // TODO confirmation form?
                require_sesskey();
                helper::delete_method($method);
                redirect(helper::get_admin_setting_url());
                break;
            case self::ACTION_VIEW_FORM:
                self::process_form($method);
                break;
            case self::ACTION_VIEW_ADMIN_PAGE:
                self::make_page_header($action);
                self::show_admin_page();
                break;
            case self::ACTION_VIEW_REPORT:
            default:
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
                $capability = 'local/assessment_methods:manage';
                if ($method) {
                    $url->param('method', $method);
                }
                break;
            case self::ACTION_VIEW_ADMIN_PAGE:
                $title = helper::get_string('assessment_method_list');
                $capability = 'local/assessment_methods:manage';
                break;
            case self::ACTION_VIEW_REPORT:
                $title = helper::get_string('report');
                $capability = 'local/assessment_methods:view_report';
                break;
            default:
                throw new \moodle_exception('unknown_action', 'local_assessment_methods');
        }

        $PAGE->set_title($title);
        $PAGE->set_url($url);

        require_capability($capability, $context);

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
        global $PAGE, $OUTPUT;

        /** @var output\renderer $renderer */
        $renderer = $PAGE->get_renderer('local_assessment_methods');

        $data = [];
        //TODO fill $data

        $renderer->render(new output\report($data));
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
