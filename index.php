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
use local_assessment_methods\helper;
use local_assessment_methods\manager;

require_once('../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_admin();

$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$license = optional_param('method', '', PARAM_ALPHA);

// Route via the manager.
manager::execute($action);
