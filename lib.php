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
 * Define the helpdesk block's additional functions
 *
 * @package    	block_helpdesk
 * @author 		Ivana Skelic, Hrvoje Golcic
 * @copyright	2014 IPA "Let's Study Together!"" project
 * @license    	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

function generate_email($courseid) {

	global $DB, $CFG;

	$query_data = "SELECT c.fullname, b.name
			FROM {book} b
			JOIN {course} c ON c.id = b.id
			WHERE c.id = ?";

	$data = $DB->get_records_sql($query_data, array($courseid));

	$course_name = $data->fullname;
	$book_name = $data->name;

	$email_body = get_string('mail_part_1', 'block_helpdesk') . '<a href="' . $CFG->wwwroot .'/course/view.php?id=' .
					$courseid . '.">' . $course_name . '</a>' . get_string('mail_part_2', 'block_helpdesk') .
					$book_name . get_string('mail_part_3', 'block_helpdesk');

	return $email_body;
}
