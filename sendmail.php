<?php

define('AJAX_SCRIPT', TRUE);
require_once('../../config.php');

require_login(null, false, null, false, true);

confirm_sesskey();

global $USER, $DB, $PAGE, $COURSE;

$context = $PAGE->context;

$coursecontext = context_course::instance($COURSE->id);

$courseid = $coursecontext->instanceid;


//we need user object for email_to_user function

$query_user = "SELECT u.*
				FROM {course} c
				JOIN {context} ct ON c.id = ct.instanceid
				JOIN {role_assignments} ra ON ra.contextid = ct.id
				JOIN {user} u ON u.id = ra.userid
				JOIN {role} r ON r.id = ra.roleid
				WHERE (r.shortname = ? OR r.shortname = ?) AND c.id = ?";

$params = array('editingteacher', 'teacher', $courseid);
$user_data = $DB->get_records_sql($query_user, $params);

$query_course = "SELECT c.fullname
			FROM {course} c
			WHERE c.id = ?";

$course_data = $DB->get_record_sql($query_course, $params);

//create new object for sending user
$from_user = new stdClass();
$from_user->email = $CFG->noreplyaddress;
$from_user->firstname = '';
$from_user->lastname = '';
$from_user->maildisplay = true;
$from_user->mailformat = 1;
$from_user->firstnamephonetic = '';
$from_user->lastnamephonetic = '';
$from_user->middlename = '';
$from_user->alternatename = '';

//form the mail
$subject = get_string('subject', 'helpdesk_block');
$body_html = 'message';
$alt_body = 'message';

$cnt_t = 0;
$cnt_f = 0;

foreach ($user_data as $user) {
	if (email_to_user($user, $from_user, $subject, $alt_body, $body_html)) {
		$cnt_t ++;
    } else {
        $cnt_f ++;
    }
}

if ($cnt_f <= $cnt_t)
	$result = true;
else
	$result = false;

//$OUTPUT->header();

header('Content-Type: application/json');

echo json_encode(array('result' => $result));