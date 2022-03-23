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
 * @copyright  2022 Technische Universität Berlin <info@isis.tu-berlin.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_assessment_methods\output;

use HTML_QuickForm_text;
use local_assessment_methods\helper;

defined('MOODLE_INTERNAL') || die();

class method_form extends \MoodleQuickForm {

    public function definition() {
        /** @var HTML_QuickForm_text $method_elem */
        $setting = False;
        $method = $this->optional_param('method',null, PARAM_NOTAGS);
        if ($method) {
            $setting = helper::get_setting();
        }

        $this->addElement('text', 'method_id', $method);
        $group = [];
        $lang_strings = get_string_manager()->get_list_of_translations();
        foreach ($lang_strings as $lang_code => $localized_string) {
            /** @var HTML_QuickForm_text $elem */
            $elem = $this->createElement('text', self::get_translation_element_name($lang_code), $localized_string);
            $elem->setType(PARAM_NOTAGS);
            if ($setting && isset($setting[$method])) {
                $elem->setValue($setting[$method][$lang_code]);
            }
            $group[] = $elem;
        }
        $this->addGroup($group);
        $this->addElement('submit', 'Submit');   //TODO translate
    }

    public static function get_translation_element_name(string $lang_code) : string {
        return "method_translation_${lang_code}";
    }
}