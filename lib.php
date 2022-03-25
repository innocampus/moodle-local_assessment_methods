<?php

defined('MOODLE_INTERNAL') || die;

define('LOCAL_ASSESSMENT_METHODS_DELIMITER', '|');
define('LOCAL_ASSESSMENT_METHODS_KEY_INDEX', 'key');

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
    if (!empty($wrapper->coursemodule)
            && ($record = $DB->get_record('assessment_methods', ['cmid' => $wrapper->coursemodule]))) {
        $selected = $record->method;
    }

    $options = get_method_options($selected, $wrapper->modulename);
    if (empty($options)) {
        return;
    }
    if (!$selected) {
        $options = array_merge(
            ['' => get_string('please_select', 'local_assessment_methods')],
            $options
        );
    }

    $method_choice = $mform->createElement(
        'select',
        'assessment_method',
        get_string('assessment_method', 'local_assessment_methods'),
        $options
    );
    $method_choice->setSelected($selected);
    $mform->insertElementBefore($method_choice, 'introeditor');
    $mform->addRule('assessment_method', get_string('required'), 'required');
    $mform->addHelpButton('assessment_method', 'assessment_method', 'local_assessment_methods');
}

/**
 * @param $data
 * @param $course
 * @throws dml_exception
 */
function local_assessment_methods_coursemodule_edit_post_actions($data, $course) {
    global $DB, $USER;

    // ensure existence of the property
    if (empty($data->assessment_method)) {
        return;
    }

    // if no login is present, use guest user
    $userid = (is_object($USER) && !empty($USER->id)) ? $USER->id : 1;
    if ($record = $DB->get_record('assessment_methods', ['cmid' => $data->coursemodule])) {
        $record->method = $data->assessment_method;
        $record->userid = $userid;
        $DB->update_record('assessment_methods', $record);
    } else {
        $DB->insert_record(
            'assessment_methods',
            ['cmid' => $data->coursemodule, 'userid' => $userid, 'method' => $data->assessment_method]
        );
    }
}

/**
 * @param $module
 * @return array
 * @throws coding_exception
 * @throws dml_exception
 */
function get_method_options($selected, $module) {
    /** @var stdClass $methods */
    $methods = \local_assessment_methods\helper::get_methods();

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

    return $options;
}