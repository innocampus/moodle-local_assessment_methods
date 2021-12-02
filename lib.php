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
    if (in_array($wrapper->modulename, ["quiz", "assign"])) {
        $options = get_method_options($wrapper->modulename);
        if (!empty($options)) {
            $method_choice = $mform->createElement('select', 'assessment_method', get_string('assessment_method', 'local_assessment_methods'), $options);
            if ($wrapper->modulename == "quiz") {
                $mform->insertElementBefore($method_choice, 'timing');
            } elseif ($wrapper->modulename == "assign") {
                $mform->insertElementBefore($method_choice, 'introattachments');
            }
            $mform->addRule('assessment_method', get_string('required'), 'required');
            $mform->addHelpButton('assessment_method', 'assessment_method', 'local_assessment_methods');
            if (!empty($wrapper->coursemodule) && $record = $DB->get_record('assessment_methods', ['cmid' => $wrapper->coursemodule])) {
                $method_choice->setSelected($record->method);
            }
        }
    }
}

/**
 * @return array|false The (associated) array maps lang code to their resp. index number
 * @throws dml_exception
 */
function get_lang_indexes() {
    $keys_raw = get_config('local_assessment_methods', 'lang_filter');
    $keys = explode(LOCAL_ASSESSMENT_METHODS_DELIMITER, $keys_raw);
    return array_combine($keys, range(0, count($keys)-1));
}

/**
 * @param $module
 * @return array
 * @throws coding_exception
 * @throws dml_exception
 */
function get_method_options($module) {
    /** @var stdClass $config */
    $config = get_config('local_assessment_methods');
    $indexes = get_lang_indexes();
    $lang = current_language();
    if (isset($indexes[$lang])) {
        $lang_index = $indexes[$lang];
    } else {
        $lang = get_parent_language($lang);
        if (isset($indexes[$lang])) {
            $lang_index = $indexes[$lang];
        } else {
            $lang_index = $indexes[LOCAL_ASSESSMENT_METHODS_KEY_INDEX] === 0 ? 1 : 0;
        }
    }
    $methods = [];
    $methods_raw = $config->{"${module}_methods"};
    if (!empty($methods_raw)) {
        $method_lines = explode(PHP_EOL, $methods_raw);
        $max_index = max($lang_index, $indexes[LOCAL_ASSESSMENT_METHODS_KEY_INDEX]);
        foreach ($method_lines as $method_line) {
            if (substr_count($method_line, LOCAL_ASSESSMENT_METHODS_DELIMITER) >= $max_index) {
                $method = explode(LOCAL_ASSESSMENT_METHODS_DELIMITER, $method_line);
                $methods[$method[$indexes[LOCAL_ASSESSMENT_METHODS_KEY_INDEX]]] = $method[$lang_index];
            } else {
                $methods[] = '[' . get_string('missing_index', 'local_assessment_methods', $max_index) . ']';
            }
        }
    }
    return $methods;
}

/**
 * @param $data
 * @param $course
 * @throws dml_exception
 */
function local_assessment_methods_coursemodule_edit_post_actions($data, $course) {
    global $DB, $USER;

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

function local_assessment_methods_coursemodule_validation($fromform, $fields) {
    // Validation & Notification
}
