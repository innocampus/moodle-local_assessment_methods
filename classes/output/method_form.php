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
use local_assessment_methods\manager;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->libdir . '/formslib.php');

class method_form extends \moodleform {

    public function definition() {
        /** @var HTML_QuickForm_text $method_elem */
        $setting = False;
        $method = $this->optional_param('method',null, PARAM_NOTAGS);
        $mform = $this->_form;

        // General settings
        $mform->addElement('header', 'generalheader',get_string('general'));
        $mform->addElement('hidden', 'action', manager::ACTION_EXECUTE_FORM)->setType(PARAM_ALPHA);
        /** @var HTML_QuickForm_text $elem */
        $elem = $mform->addElement('text', 'method_id', helper::get_string('method_id'));
        $elem->setType(PARAM_ALPHA);
        $mform->addRule('method_id', get_string('required'),'required');
        if ($method) {
            $elem->setValue($method);
            $elem->setAttributes(['disabled' => 'disabled']);
            $setting = helper::get_setting();  // used later
        }

        // Translations
        $mform->addElement('header', 'settingsheader', helper::get_string('translations'));
        $lang_strings = get_string_manager()->get_list_of_translations();
        foreach ($lang_strings as $lang_code => $localized_string) {
            /** @var HTML_QuickForm_text $elem */
            $elem = $mform->addElement('text', self::get_translation_element_name($lang_code), $localized_string);
            $elem->setType(PARAM_NOTAGS);
            if ($setting && isset($setting[$method])) {
                $elem->setValue($setting[$method][$lang_code]);
            }
        }
        $mform->setExpanded('generalheader');
        $mform->setExpanded('settingsheader');
        $this->add_action_buttons();
    }

    public function definition_after_data()
    {
        parent::definition_after_data();
        /** @var HTML_QuickForm_text $method_element */
        $method_element = $this->_form->getElement('method_id');
        if (!empty($method_element->getValue())) {
            $method_element->setAttributes(['disabled' => 'disabled']);
        }
    }

    public static function get_translation_element_name(string $lang_code) : string {
        return "method_translation_${lang_code}";
    }
}