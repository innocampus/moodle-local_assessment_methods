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
 * @package    local_assessment_methods
 *
 * @author     Lars Bonczek <bonczek@tu-berlin.de>
 * @copyright  2021 Technische Universität Berlin <info@isis.tu-berlin.de>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_assessment_methods\helper;

function xmldb_local_assessment_methods_install() {
    helper::add_or_update_method("home_exam", ["de" => "Digitale Fernprüfung (Klausur)", "en" => "E-Exam at home"]);
    helper::add_or_update_method("uni_exam", ["de" => "Digitale Präsenzprüfung (Klausur)", "en" => "E-Exam at university"]);
    helper::add_or_update_method("portfolio", ["de" => "Portfolio-Teilleistung", "en" => "Part of portfolio exam"]);
    helper::add_or_update_method("presentation", ["de" => "Referat", "en" => "Presentation"]);
    helper::add_or_update_method("paper", ["de" => "Hausarbeit", "en" => "(Seminar) paper"]);
    helper::add_or_update_method("thesis", ["de" => "Abschlussarbeit", "en" => "Thesis"]);
    helper::add_or_update_method("prerequisite", ["de" => "Klausurvoraussetzung", "en" => "Prerequiste for exam"]);
    helper::add_or_update_method("trial", ["de" => "Probeklausur", "en" => "Trial exam"]);
    helper::add_or_update_method("homework", ["de" => "Hausaufgabe", "en" => "Homework"]);
    helper::add_or_update_method("self", ["de" => "Vertiefung/Selbststudium", "en" => "Self-Assignment"]);
    helper::add_or_update_method("pretest", ["de" => "Vortest (keine Portfolio-Teilleistung)", "en" => "Pretest"]);
}