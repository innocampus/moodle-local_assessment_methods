<?php

//TODO: Make usable activities selectable

/** @var admin_category $ADMIN */
$ADMIN->add('localplugins', new admin_externalpage(
    'assessmentmethods',
    get_string('pluginname', 'local_assessment_methods'),
    \local_assessment_methods\helper::get_admin_setting_url(),
    'local/assessment_methods:manage'
));

$ADMIN->add('reports', new admin_externalpage(
    'assessmentmethodsreport',
    get_string('pluginname', 'local_assessment_methods'),
    \local_assessment_methods\helper::get_report_url(),
    'local/assessment_methods:view_report'
));
