<?php

use local_assessment_methods\helper;

defined('MOODLE_INTERNAL') || die;

/**
 * @param $formwrapper
 * @param MoodleQuickForm $mform
 * @throws coding_exception
 * @throws dml_exception
 */
function local_assessment_methods_coursemodule_standard_elements($formwrapper, $mform)  {
    global $DB;
    $wrapper = $formwrapper->get_current();
    if (!in_array($wrapper->modulename, ["quiz", "assign"])) {
        return;
    }

    $selected = null;
    if (!empty($wrapper->coursemodule)) {
        $selected = \local_assessment_methods\helper::get_cm_method($wrapper->coursemodule);
    }

    $options = helper::get_method_options($selected, $wrapper->modulename);
    if (empty($options)) {
        return;
    }

    $method_choice = $mform->createElement(
        'select',
        'assessment_method',
        get_string('assessment_method', 'local_assessment_methods'),
        $options
    );
    $method_choice->setSelected($selected);
    if ($mform->elementExists('local_assessment_archiving_enabled')) {
        $mform->insertElementBefore($method_choice, 'local_assessment_archiving_enabled');
    } else {
        $mform->insertElementBefore($method_choice, 'introeditor');
    }
    $mform->addRule('assessment_method', get_string('required'), 'required');
    $mform->addHelpButton('assessment_method', 'assessment_method', 'local_assessment_methods');
}

/**
 * @param $data
 * @param $course
 * @throws dml_exception
 */
function local_assessment_methods_coursemodule_edit_post_actions($data, $course) {
    // ensure existence of the property
    if (empty($data->assessment_method)) {
        return $data;
    }

    helper::set_cm_method($data->coursemodule, $data->assessment_method);
    return $data;
}
