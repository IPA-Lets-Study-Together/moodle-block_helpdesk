<?php

define('AJAX_SCRIPT', TRUE);
require_once('../../config.php');
require_once($CFG->libdir.'/phpmailer/class.phpmailer.php'); //required
require_once($CFG->dirroot.'/blocks/helpdesk/lib.php');

require_login(null, false, null, false, true);

confirm_sesskey();

global $USER, $DB, $PAGE, $COURSE;

$coursecontext = context_course::instance($COURSE->id);

$courseid = $coursecontext->id;

//query the database for all the data needed

$query_user = "SELECT u.email, u.firstname, u.lastname
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

$course_name = $DB->get_field_sql($query_course, $params);

$query_resource = "SELECT b.name
					FROM {book} b
					JOIN {course} c ON c.id = b.id
					WHERE c.id = ?";

$resource_name = $DB->get_field_sql($query_resource, array($courseid), MUST_EXIST);

$mail = new PHPmailer();
$mail->WordWrap = 50;	// Set word wrap to 50 characters
$mail->isHTML(true);	// Set email format to HTML
$mail->SetFrom($CFG->noreplyaddress, 'No-reply');
$mail->Subject = get_string('subject', 'block_helpdesk');
$mail->AltBody = get_string('altbody', 'block_helpdesk');

$cnt = 0;

foreach ($user_data as $user) {

	if ($cnt == 0) {
		$mail->AddAddress($user->email, $user->firstname." ".$user->lastname);
	} else {
		$mail->AddCC($user->email, $user->firstname." ".$user->lastname);
	}

	$cnt++;

	$mail->Body = get_string('hi', 'block_helpdesk').$user->firstname." ".$user->lastname;

}

$body = generate_email($course_name, $resource_name, $courseid);

$mail->Body = $mail->Body.$body;

if(!$mail->Send()) {
  $result = false;
  echo($mail->ErrorInfo);
} else {
  $result = true;
}	

header('Content-Type: application/json');
echo json_encode(array('result' => $result));