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
 * Assessment Methods - Force content creators to specify assignment methods in activities
 *
 * @package   local_assessment_methods
 * @copyright Jan Eberhardt (@innoCampus, TU Berlin)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/** @var stdClass $plugin - dirty hack for PHPStorm */
$plugin->component = 'local_assessment_methods';
$plugin->version   = 2021061401;
$plugin->requires  = 2020061500;
$plugin->cron      = 0;
$plugin->maturity  = MATURITY_RC;
$plugin->release   = '1.00 (Build: 2021061400)';
