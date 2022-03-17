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
     * @return \moodle_url
     * @throws \moodle_exception
     */
    public static function get_method_add_url() {
        return new \moodle_url(self::PLUGIN_PATH . 'index.php', ['action' => manager::ACTION_NEW_METHOD]);
    }

    /**
     * @param string $id
     * @return \moodle_url
     * @throws \moodle_exception
     */
    public static function get_method_edit_url($id) {
        $params = ['action' => manager::ACTION_EDIT_METHOD, 'id' => $id];
        return new \moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    /**
     * @param string $id
     * @return \moodle_url
     * @throws \moodle_exception
     */
    public static function get_method_delete_url($id) {
        $params = ['action' => manager::ACTION_DELETE_METHOD, 'id' => $id];
        return new \moodle_url(self::PLUGIN_PATH . 'index.php', $params);
    }

    /**
     * @return array|null
     */
    public static function get_setting() {
        $setting = null;
        try {
            $json = get_config('local_assessment_methods', 'method_json');
        } catch (\dml_exception $_) {
            $json = null;
        }
        if ($json) {
            $setting = (array) json_decode($json);
        }
        return $setting;
    }

    /**
     * Writes the settings
     *
     * @throws \moodle_exception
     * @param array $settings an array with following structure:
     *
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
            throw new \moodle_exception('write_empty_settings_error', 'local_assessment_methods');
        }
    }
}
