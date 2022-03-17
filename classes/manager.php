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

defined('MOODLE_INTERNAL') || die();

class manager {

    const ACTION_DELETE_METHOD = 'delete';
    const ACTION_VIEW_FORM = 'view-form';
    const ACTION_EXECUTE_FORM = 'exe-form';

    /** @var \local_assessment_methods\output\method_form */
    private $mform;


    public function execute($action, $method) {
        global $PAGE;

        $method = optional_param('method', false);

        switch ($action) {
            case self::ACTION_EXECUTE_FORM:

                break;
            case self::ACTION_DELETE_METHOD:
                break;
            case self::ACTION_VIEW_FORM:
            default:
                $this->show_form($method);
        }
        //TODO $PAGE->set_title()
    }

    private function show_form($method) {
        global $PAGE;

        $title = $method ? "Edit method" : "Create method";    //TODO: translate
        $PAGE->set_title($title);

        $form = new output\method_form('method_form', 'POST', helper::get_method_edit_url($method));
        $form->display();
    }

    private function delete_method($method) {
        if ($method) {
            $settings = helper::get_setting();
            array_remove_by_key($settings, $method);
        }
    }

    private function update_setting($method, $lang_array) {
        $setting = helper::get_setting();
    }
}