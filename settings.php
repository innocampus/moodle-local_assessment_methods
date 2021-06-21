<?php

$temp = new admin_settingpage('assessmentmethods', get_string('pluginname', 'local_assessment_methods'), 'local/assessment_methods:manage');

$temp->add(new admin_setting_configtext(
    'local_assessment_methods/lang_filter',
    get_string('lang_filter', 'local_assessment_methods'),
    get_string('lang_filter_desc', 'local_assessment_methods'),
    'key|de|en'
));

$temp->add(new admin_setting_configtextarea(
    'local_assessment_methods/quiz_methods',
    get_string('quiz_methods', 'local_assessment_methods'),
    get_string('methods_desc', 'local_assessment_methods'),
    <<<EOL
home_exam|Digitale Fernprüfung (Klausur)|E-Exam at home
e_exam|Digitale Präsenzprüfung (Klausur)|E-Exam at university
portfolio|Portfolio-Teilleistung|Part of Portfolio exam
thesis|Abschlussarbeit|Thesis
prerequisite|Klausurvorraussetzung|Prerequiste for exam
trial|Probeklausur|Trial exam
homework|Hausaufgabe|Homework
self|Vertiefung/Selbststudium|Self-Assignment
EOL
));

$temp->add(new admin_setting_configtextarea(
    'local_assessment_methods/assign_methods',
    get_string('assign_methods', 'local_assessment_methods'),
    get_string('methods_desc', 'local_assessment_methods'),
    <<<EOL
home_exam|Digitale Fernprüfung (Klausur)|E-Exam at home
e_exam|Digitale Präsenzprüfung (Klausur)|E-Exam at university
portfolio|Portfolio-Teilleistung|Part of Portfolio exam
thesis|Abschlussarbeit|Thesis
prerequisite|Klausurvorraussetzung|Prerequiste for Exam
trial|Probeklausur|Trial exam
homework|Hausaufgabe|Homework
self|Vertiefung/Selbststudium|Self-Assignment
EOL
));

/** @var admin_category $ADMIN */
$ADMIN->add('localplugins', $temp);
