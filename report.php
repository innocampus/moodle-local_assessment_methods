<?php

require_once dirname(__FILE__) . "/lib.php";

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_title(get_string("pluginname", "local_assessment_methods", get_site()->shortname));
$PAGE->set_url(new moodle_url("/local/assessment_methods/report.php"));
$PAGE->set_pagelayout("standard");
$PAGE->navbar->add(get_string("pluginname", "local_assessment_methods"));

require_login(SITEID, false);
require_capability("local/assessment_methods:view_report", $context);

$output = new \local_assessment_methods\output\renderer($PAGE);

echo $OUTPUT->header();
echo $OUTPUT->footer();
