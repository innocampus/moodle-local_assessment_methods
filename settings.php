<?php

//TODO: Make usable activities selectable

$temp = new admin_settingpage(
    'assessmentmethods',
    get_string('pluginname', 'local_assessment_methods'),
    'local/assessment_methods:manage'
);

try {
    $temp->add(new admin_setting_local_assessment_methods(
        get_string()
    ));
} catch (moodle_exception $e) {
    $temp = null;
}

if ($temp) {
    /** @var admin_category $ADMIN */
    $ADMIN->add('localplugins', $temp);
}

$ADMIN->add('reports', new admin_externalpage(
    'assessmentmethodsreport',
    get_string('pluginname', 'local_assessment_methods'),
    $CFG->wwwroot . '/local/assessment_methods/report.php',
    'local/assessment_methods:view_report'
));
