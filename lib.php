<?php

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
            if ($record = $DB->get_record('assessment_methods', ['cmid' => $wrapper->coursemodule])) {
                $method_choice->setSelected($record->method);
            }
        }
    }
}

/**
 * @param $module
 * @param int $lang_index
 * @return array
 * @throws coding_exception
 * @throws dml_exception
 */
function get_method_options($module, $lang_index = 0) {
    /** @var stdClass $config */
    $config = get_config('local_assessment_methods');
    $filter = explode('|', $config->lang_filter);
    $lang = current_language();
    $key_index = intval(array_search('key', $filter)); // cast to int so that 0 is fallback
    $lang_index = array_search($lang, $filter);
    if ($lang_index === false) {
        $lang = get_parent_language($lang);
        $lang_index = array_search($lang, $filter);
        if ($lang_index === false) {
            $lang_index = $key_index === 0 ? 1 : 0;
        }
    }
    $methods = [];
    $methods_raw = $config->{"${module}_methods"};
    if (!empty($methods_raw)) {
        $method_lines = explode(PHP_EOL, $methods_raw);
        $max_index = max($lang_index, $key_index);
        foreach ($method_lines as $method_line) {
            if (substr_count($method_line, "|") >= $max_index) {
                $method = explode('|', $method_line);
                $methods[$method[$key_index]] = $method[$lang_index];
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
    global $DB;
    var_dump($data);
    if ($record = $DB->get_record('assessment_methods', ['cmid' => $data->coursemodule])) {
        $record->method = $data->assessment_method;
        $DB->update_record('assessment_methods', $record);
    } else {
        $DB->insert_record(
            'assessment_methods',
            ['cmid' => $data->coursemodule, 'method' => $data->assessment_method]
        );
    }
}

function local_assessment_methods_coursemodule_validation($fromform, $fields) {
    // Validation & Notification
}
