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
use local_assessment_methods\output\method_form;
use moodle_exception;
use context_system;
use moodle_url;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->libdir . '/adminlib.php');

class manager {

    const ACTION_DELETE_METHOD = 'delete';
    const ACTION_EXECUTE_FORM = 'exe-form';
    const ACTION_VIEW_FORM = 'view-form';
    const ACTION_VIEW_ADMIN_PAGE = 'admin';
    const ACTION_VIEW_REPORT = 'report';

    /**
     * @param string $action
     * @throws moodle_exception
     */
    public static function execute(string $action) {
        $method = optional_param('method', null, PARAM_ALPHA);
        switch ($action) {
            case self::ACTION_EXECUTE_FORM:
                self::process_form();
                redirect(helper::get_admin_setting_url());
                break;
            case self::ACTION_DELETE_METHOD:
                self::delete_method($method);
                redirect(helper::get_admin_setting_url());
                break;
            case self::ACTION_VIEW_FORM:
                self::make_page_header($action, $method);
                self::show_form($method);
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
        $PAGE->navbar->add(
            get_string('administrationsite'),
            new moodle_url('/admin/search.php')
        );

        switch ($for_action) {
            case self::ACTION_VIEW_FORM:
                $title = $method
                    ? helper::get_string('edit_method')
                    : helper::get_string('create_method');
                $capability = 'local/assessment_methods:manage';
                if ($method) {
                    $url->param('method', $method);
                }
                // breadcrumbs
                $PAGE->navbar->add(
                    get_string('plugins', 'admin'),
                    new moodle_url('/admin/search.php#linkmodules')
                );
                $PAGE->navbar->add(
                    helper::get_string('pluginname'),
                    helper::get_admin_setting_url()
                );
                break;
            case self::ACTION_VIEW_ADMIN_PAGE:
                $title = helper::get_string('pluginname');
                $capability = 'local/assessment_methods:manage';
                // breadcrumbs
                $PAGE->navbar->add(
                    get_string('plugins', 'admin'),
                    new moodle_url('/admin/search.php#linkmodules')
                );
                break;
            case self::ACTION_VIEW_REPORT:
            default:
                $title = helper::get_string('report');
                $capability = 'local/assessment_methods:view_report';
                // breadcrumbs
                $PAGE->navbar->add(
                    get_string('reports'),
                    new moodle_url('/admin/search.php#linkreports')
                );
                $PAGE->navbar->add(
                    helper::get_string('pluginname'),
                    helper::get_admin_setting_url()
                );
        }

        $PAGE->navbar->add($title);
        $PAGE->set_title($title);
        $PAGE->set_heading($title);
        $PAGE->set_url($url);

        require_login(SITEID, false);
        require_capability($capability, $context);

        echo $OUTPUT->header();
    }

    /**
     * @throws moodle_exception
     */
    private static function show_form() {
        global $OUTPUT;

        $form = new method_form();
        $form->display();
        echo $OUTPUT->footer();
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
        $table = new output\setting_table(helper::get_setting(), true, true);
        if (!$table->is_empty()) {
            echo $renderer->render($table);
            echo $renderer->method_link();
        } else {
            echo $renderer->no_methods_box();
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
    private static function process_form() {
        $form = new method_form();
        if ($form->is_cancelled()) {
            redirect(helper::get_admin_setting_url());
        } else if ($form->is_submitted() && $form->is_validated()) {
            $data = $form->get_data();
            $setting = [];
            $lang_codes = array_keys(get_string_manager()->get_list_of_languages());
            foreach ($lang_codes as $lc) {
                $name = $form::get_translation_element_name($lc);
                if (isset($data[$name]) && !empty($data[$name])) {
                    $setting[$lc] = $data[$name];
                }
            }
            if (!empty($setting)) {
                helper::add_setting($data->method, $setting);
            }
        }
    }

    /**
     * @param $method
     * @throws moodle_exception
     */
    private static function delete_method($method) {
        if ($method) {
            $setting = helper::get_setting();
            if (isset($setting[$method])) {
                unset($setting[$method]);
                helper::write_setting($setting);
            }
        }
    }
}
