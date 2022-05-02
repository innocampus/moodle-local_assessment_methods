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

use local_assessment_methods\error\write_empty_settings_error;
use moodle_exception;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Assessment methods helper class.
 *
 * @package    local_assessmenet_methods
 * @author     Jan Eberhardt <jan.eberhardt@tu-berlin.de>
 * @copyright  2022 Technische Universität Berlin <info@isis.tu-berlin.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class helper {

    const PLUGIN_PATH = '/local/assessment_methods/';

    private static $loadedmethods = null;

    /**
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_method_add_url(): moodle_url {
        $params = ['action' => manager::ACTION_VIEW_FORM];
        return new moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    /**
     * @param ?string $id
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_method_edit_url(?string $id = null): moodle_url {
        $url = new moodle_url(self::PLUGIN_PATH . 'index.php', ['action' => manager::ACTION_VIEW_FORM]);
        if ($id) {
            $url->param('method', $id);
        }
        return $url;
    }

    /**
     * @param string $id
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_method_delete_url(string $id): moodle_url {
        $params = ['action' => manager::ACTION_DELETE_METHOD, 'method' => $id, 'sesskey' => sesskey()];
        return new moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    /**
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_admin_setting_url(): moodle_url {
        $params = ['action' => manager::ACTION_VIEW_ADMIN_PAGE];
        return new moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    /**
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_report_url(): moodle_url {
        $params = ['action' => manager::ACTION_VIEW_REPORT];
        return new moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    /**
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_form_action_url($method = null): moodle_url {
        $params = ['action' => manager::ACTION_VIEW_FORM];
        if ($method) {
            $params['method'] = $method;
        }
        return new moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    //TODO user URL DONE
    /**
     * @param string $userid
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_user_url(string $userid): moodle_url {
        return new moodle_url('/user/profile.php?id=' . $userid);
    }

    //TODO course URL DONE
    /**
     * @param string $courseid
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_course_url(string $courseid): moodle_url {
        return new moodle_url('/course/view.php?id=' . $courseid);
    }

    //TODO assign/quiz URL DONE
    /**
     * @param string $cmid
     * @param string $cmmodule
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_assquiz_url(string $cmid, $cmmodule): moodle_url {
        $url = null;
        if ($cmmodule == '1') {
            $url = new moodle_url('/mod/assign/view.php?id=' . $cmid);
        }
        elseif ($cmmodule == '23') {
            $url = new moodle_url('/mod/quiz/view.php?id=' . $cmid);
        }
        return $url;
    }


    /**
     * @param $identifier
     * @return string
     * @throws moodle_exception
     */
    public static function get_string($identifier): string {
        return get_string($identifier, 'local_assessment_methods');
    }

    public static function get_methods(): array {
        if (self::$loadedmethods !== null) {
            return self::$loadedmethods;
        }
        try {
            $json = get_config('local_assessment_methods', 'methods_json');
            if (!$json) {
                self::$loadedmethods = [];
            } else {
                self::$loadedmethods = json_decode($json, true);
            }
            return self::$loadedmethods;
        } catch (\dml_exception $_) {
            return [];
        }
    }

    /**
     * Writes the settings
     *
     * @param array $methods an array with following structure:
     *  +- method1 -+- translations -+- en_en -- English string
     *  |           |                +- de_de -- German string
     *  |           |                +- es_es -- Spanish string
     *  |           +- visibility -- int
     *  |
     *  +- method2 -+- translations -+- en_en -- English string
     *              |                +- de_de -- German string
     *              |                +- es_es -- Spanish string
     *              +- visibility -- int
     */
    public static function write_methods(array $methods) {
        set_config('methods_json', json_encode($methods), 'local_assessment_methods');
        self::$loadedmethods = $methods;
    }

    /**
     * Add a single setting
     *
     * @param string $method
     * @param array $langs an array with following structure:
     * -+- en_en -- English string
     *  +- de_de -- German string
     *  +- es_es -- Spanish string
     * @param int $visibility
     */
    public static function add_or_update_method(string $method, array $langs, int $visibility = manager::VISIBILITY_ALL) {
        $methods = self::get_methods();
        $methods[$method] = [
            'translations' => $langs,
            'visibility' => $visibility
        ];
        self::write_methods($methods);
    }

    /**
     * @param $method
     */
    public static function delete_method($method) {
        if ($method) {
            // TODO: throw error if method is in use anywhere
            $methods = self::get_methods();
            if (isset($methods[$method])) {
                unset($methods[$method]);
                self::write_methods($methods);
            }
        }
    }

    /**
     * Returns an array of options to be used in an assessment method select element (including an empty option if no
     * method is selected). Returns an empty list if no methods are available.
     *
     * @param $selected
     * @param $module
     * @return array
     * @throws \coding_exception
     */
    public static function get_method_options($selected, $module): array {
        $methods = self::get_methods();

        $options = [];
        foreach ($methods as $method => $method_data) {
            if ($method_data['visibility'] !== \local_assessment_methods\manager::VISIBILITY_ALL && $method !== $selected) {
                continue;
            }
            $langs = $method_data['translations'];
            if (empty($langs)) {
                $options[$method] = $method;
                continue;
            }
            $lang = current_language();
            while ($lang && empty($langs[$lang])) {
                $lang = get_parent_language($lang);
            }
            if ($lang) {
                $options[$method] = $langs[$lang];
            } else {
                $options[$method] = reset($langs);
            }
        }

        if (!$selected && !empty($options)) {
            $options = array_merge(
                ['' => get_string('please_select', 'local_assessment_methods')],
                $options
            );
        }

        return $options;
    }

    /**
     * Get assessment method of an activity.
     *
     * @param int $cmid course module id
     * @return string|null
     */
    public static function get_cm_method(int $cmid) : ?string {
        global $DB;

        $method = $DB->get_field('local_assessment_methods', 'method', ['cmid' => $cmid]);
        return ($method === false) ? null : $method;
    }

    public static function set_cm_method($cmid, $method, $userid = null) {
        global $USER, $DB, $CFG;
        if (!$userid) {
            // if no login is present, use guest user
            $userid = (is_object($USER) && !empty($USER->id)) ? $USER->id : $CFG->siteguest;
        }

        if ($record = $DB->get_record('local_assessment_methods', ['cmid' => $cmid])) {
            $record->method = $method;
            $record->userid = $userid;
            $DB->update_record('local_assessment_methods', $record);
        } else {
            $DB->insert_record(
                'local_assessment_methods',
                ['cmid' => $cmid, 'userid' => $userid, 'method' => $method]
            );
        }
    }
}
