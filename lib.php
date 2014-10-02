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

/**
 * Find out whether we're desponding to an AJAX call by seeing if the HTTP_X_REQUESTED_WITH header
 * is XMLHttpRequest
 * @param int $context - context variable extracted from $PAGE global variable
 * @param int $courseid - course id of initial course from which the user is sending message
 *
 * @return string with formed email text
 */
function generate_email($context, $courseid, $usermsg) {

	global $DB, $CFG;

	$course_name = $DB->get_field('course', 'fullname', array('id' => $courseid), MUST_EXIST);

	$query_book = "SELECT cm.id, mb.name
		FROM {course_modules} AS cm
		INNER JOIN {context} AS ctx ON ctx.contextlevel =70
		AND ctx.instanceid = cm.id
		INNER JOIN {modules} AS mdl ON cm.module = mdl.id
		LEFT JOIN {book} AS mb ON mdl.name =  'book'
		AND cm.instance = mb.id
		WHERE ctx.id = ?";

	$data =  $DB->get_record_sql($query_book, array($context), MUST_EXIST);

	$email_body = get_string('mail_part_1', 'block_helpdesk'). 
		'<a href="'
		.$CFG->wwwroot.
		'/mod/book/view.php?id='.
		$data->id.
		'.">'. 
		$data->name.
		'</a>'.
		get_string('mail_part_2', 'block_helpdesk').
		'<a href="'.
		$CFG->wwwroot.
		'/course/view.php?id='.
		$courseid.
		'.">'. 
		$course_name.
		'</a>'.
		get_string('mail_part_3', 'block_helpdesk');

	if (!empty($usermsg)) {

		$email_body .= '<br>'.
			get_string('user_msg', 'block_helpdesk').
			'<br>'.
			$usermsg;
	}

	return $email_body;
}

/**
 * Find out whether we're desponding to an AJAX call by seeing if the HTTP_X_REQUESTED_WITH header
 * is XMLHttpRequest
 *
 * @return boolean whether we're reponding to an AJAX call or not
 */
function request_is_ajax() {

    $reqwith = 'HTTP_X_REQUESTED_WITH';
    if (isset($_SERVER[$reqwith]) && $_SERVER[$reqwith] == 'XMLHttpRequest') {
        $xhr = true;
    } else {
        $xhr = false;
    }
    
    return $xhr;
}

/**
 * Get book id using context provided
 * @param int $context - context variable extracted from $PAGE global variable
 *
 * @return string
 */
function get_book_id($context) {

    global $DB;

	$query_book = "SELECT cm.id
					FROM {course_modules} AS cm
					INNER JOIN {context} AS ctx ON ctx.contextlevel =70
					AND ctx.instanceid = cm.id
					INNER JOIN {modules} AS mdl ON cm.module = mdl.id
					LEFT JOIN {book} AS mb ON mdl.name =  'book'
					AND cm.instance = mb.id
					WHERE ctx.id = ?";

	$bookid =  $DB->get_field_sql($query_book, array($context), MUST_EXIST);

	return $bookid;
}
