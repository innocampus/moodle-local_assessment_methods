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

    /**
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_method_add_url(): moodle_url
    {
        $params = ['action' => manager::ACTION_VIEW_FORM];
        return new moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    /**
     * @param ?string $id
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_method_edit_url(?string $id): moodle_url
    {
        $url = new moodle_url(self::PLUGIN_PATH . 'index.php', ['action' => manager::ACTION_EXECUTE_FORM]);
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
    public static function get_method_delete_url(string $id): moodle_url
    {
        $params = ['action' => manager::ACTION_DELETE_METHOD, 'id' => $id];
        return new moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    /**
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_admin_setting_url(): moodle_url
    {
        $params = ['action' => manager::ACTION_VIEW_ADMIN_PAGE];
        return new moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    /**
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_report_url(): moodle_url
    {
        $params = ['action' => manager::ACTION_VIEW_REPORT];
        return new moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    /**
     * @return moodle_url
     * @throws moodle_exception
     */
    public static function get_form_action_url(): moodle_url {
        $params = ['action' => manager::ACTION_EXECUTE_FORM];
        return new moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    public static function get_method_form(): output\method_form {
        return new output\method_form(
            self::get_form_action_url(),
            ['id' => 'method_form', 'name' => 'method_form', 'method' => 'POST', 'accept-charset' => 'UTF-8']
        );
    }

    /**
     * @param $identifier
     * @return string
     * @throws moodle_exception
     */
    public static function get_string($identifier): string {
        return get_string($identifier, 'local_assessment_methods');
    }

    public static function get_setting(): array
    {
        try {
            $json = get_config('local_assessment_methods', 'method_json');
            return (array) json_decode($json);
        } catch (\dml_exception $_) {
            return [];
        }
    }

    /**
     * Writes the settings
     *
     * @throws moodle_exception
     * @param array $settings an array with following structure:
     *  +- method1 -+- en_en -- English string
     *  |           +- de_de -- German string
     *  |           +- es_es -- Spanish string
     *  |
     *  +- method2 -+- en_en -- English string
     *              +- de_de -- German string
     *              +- es_es -- Spanish string
     */
    public static function write_setting(array $settings) {
        $lang_man = get_string_manager();
        $new_settings = [];
        foreach ($settings as $method => $langs) {
            $mlang_codes = array_keys($langs);
            foreach ($mlang_codes as $lc) {
                if ($lang_man->translation_exists($lc, false)) {
                    if (!isset($new_settings[$method])) {
                        $new_settings[$method] = [];
                    }
                    $new_settings[$method][$lc] = $settings[$method][$lc];
                }
            }
        }

        if (!empty($new_settings)) {
            set_config('methods_json', json_encode($new_settings), 'local_assessment_methods');
        } else {
            throw new moodle_exception('write_empty_settings_error', 'local_assessment_methods');
        }
    }

    /**
     * Add a single setting
     *
     * @throws moodle_exception
     * @param string $method
     * @param array $setting an array with following structure:
     * -+- en_en -- English string
     *  +- de_de -- German string
     *  +- es_es -- Spanish string
     */
    public static function add_setting(string $method, array $setting) {
        $settings = self::get_setting();
        $settings[$method] = $setting;
        self::write_setting($settings);
    }
}
