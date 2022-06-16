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
 * @author     Christian Gillen <c.gillen@tu-berlin.de>
 * @copyright  2022 Technische Universität Berlin <info@isis.tu-berlin.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_assessment_methods\form;

use local_assessment_methods\helper;

defined('MOODLE_INTERNAL') || die();
global $CFG;

require_once($CFG->libdir . '/formslib.php');

/**
 * Class search
 * @package local_assessment_methods\form
 * @property-read \stdClass $data
 */
class search extends \moodleform {

    /**
     * Form definition
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;

        // Don't hide any fields by default
        $mform->addElement('header', 'heading',
            helper::get_string('search'));

        /*$mform->addElement('text', 'assign_quiz_name',
            helper::get_string('assign_quiz_name'));
        $mform->setType('assign_quiz_name', PARAM_TEXT);*/

        /*$mform->addElement('text', 'activities',
            helper::get_string('activities'));
        $mform->setType('activities', PARAM_TEXT);
        $mform->addHelpButton('activities', 'activities', 'local_assessment_methods');*/

        // select between assignments & quizzes, assignments only or quizzes only, called activities
        $mform->addElement('select', 'activities',
            helper::get_string('activities'),
            array(
                helper::get_string('assign_quiz'),
                helper::get_string('assign'),
                helper::get_string('quiz')
            )
        );

        // select between the different assessment methods
        $methods = array_merge(array(helper::get_string('all_assessment_methods')),
            helper::get_method_options(helper::get_methods(), null));
        $mform->addElement('select', 'assessment_methods',
            helper::get_string('assessment_methods'),
            $methods
        );

        /*$mform->addElement('text', 'method_id',
            helper::get_string('method_id'));
        $mform->setType('method_id', PARAM_TEXT);*/

        $mform->addElement('date_selector', 'datefrom',
            helper::get_string('datefrom'), ['optional' => true]);

        $mform->addElement('date_selector', 'dateto',
            helper::get_string('dateto'), ['optional' => true]);

        $mform->addElement('text', 'course',
            helper::get_string('course'));
        $mform->setType('course', PARAM_TEXT);
        $mform->addHelpButton('course', 'course', 'local_assessment_methods');

        $mform->addElement('text', 'user',
            helper::get_string('user'));
        $mform->setType('user', PARAM_TEXT);
        $mform->addHelpButton('user', 'user', 'local_assessment_methods');

        $this->add_action_buttons(true, helper::get_string('search'));
    }
}