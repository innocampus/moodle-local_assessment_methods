<?php

//TODO: Make usable activities selectable
if ($hassiteconfig) {

    /** @var admin_category $ADMIN */
    $ADMIN->add('localplugins', new admin_category(
        'assessmentmethods',
        get_string('pluginname', 'local_assessment_methods')
    ));

    $ADMIN->add('assessmentmethods', new admin_externalpage(
        'assessmentmethods_admin',
        get_string('assessment_method_list', 'local_assessment_methods'),
        \local_assessment_methods\helper::get_admin_setting_url(),
        'local/assessment_methods:manage'
    ));

    $ADMIN->add('assessmentmethods', new admin_externalpage(
        'assessmentmethods_create',
        get_string('create_method', 'local_assessment_methods'),
        \local_assessment_methods\helper::get_method_edit_url(),
        'local/assessment_methods:manage'
    ));

    $ADMIN->add('reports', new admin_externalpage(
        'assessmentmethods_report',
        get_string('pluginname', 'local_assessment_methods'),
        \local_assessment_methods\helper::get_report_url(),
        'local/assessment_methods:view_report'
    ));

}
