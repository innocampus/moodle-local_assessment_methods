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

    public function __construct()
    {
        parent::__construct(
            helper::PLUGIN_PATH . 'index.php',
            ['id' => 'method_form', 'name' => 'method_form', 'method' => 'POST', 'accept-charset' => 'UTF-8', 'class' => 'sddsfsdfd']);
    }

    public function definition() {
        $setting = False;
        $method = $this->optional_param('method',null, PARAM_NOTAGS);
        $mform = $this->_form;

        $mform->addElement('hidden', 'action', manager::ACTION_EXECUTE_FORM);
        $mform->setType('action', PARAM_ALPHA);

        // General settings
        $mform->addElement('header', 'generalheader', get_string('general'));
        $mform->setExpanded('generalheader');

        $mform->addElement('text', 'method_id', helper::get_string('method_id'));
        $mform->setType('method_id', PARAM_ALPHA);
        $mform->addRule('method_id', get_string('required'),'required');
        if ($method) {
            $mform->setDefault('method_id', $method);
            $setting = helper::get_setting();  // used later
        }

        // Translations
        $mform->addElement('header', 'settingsheader', helper::get_string('translations'));
        $mform->setExpanded('settingsheader');

        $lang_strings = get_string_manager()->get_list_of_translations();
        foreach ($lang_strings as $lang_code => $localized_string) {
            $name = self::get_translation_element_name($lang_code);
            $mform->addElement('text', $name, $localized_string);
            $mform->setType($name, PARAM_NOTAGS);
            if ($setting && isset($setting[$method])) {
                $mform->setDefault($name, $setting[$method][$lang_code]);
            }
            if ($lang_code === 'en' or $lang_code === 'en_en') {
                $mform->addRule($name, get_string('required'), 'required');
            }
        }

        $this->add_action_buttons();
    }

    /*
    public function definition_after_data()
    {
        parent::definition_after_data();
        $method_element = $this->_form->getElement('method_id');
        if (!empty($method_element->getValue())) {
            $method_element->setAttributes(['disabled' => 'disabled']);
        }
    }
    */

    public static function get_translation_element_name(string $lang_code) : string {
        return "method_translation_${lang_code}";
    }
}