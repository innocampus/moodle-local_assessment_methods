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
 * Settings lib
 *
 * @package    local_assessment_methods
 * @author     Jan Eberhardt <jan.eberhardt@tu-berlin.de>
 * @copyright  2022 Technische Universität Berlin
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once('../../config.php');

require_admin();

$returnurl = \local_assessment_methods\helper::get_admin_settings_url();

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$license = optional_param('method', '', PARAM_ALPHA);

if (!confirm_sesskey()) {
    redirect($returnurl);
}

// Route via the manager.
$manager = new \local_assessment_methods\manager();
$PAGE->set_context(context_system::instance());
$PAGE->set_url(\local_assessment_methods\helper::get_index_url());

$manager->execute($action, $method);
