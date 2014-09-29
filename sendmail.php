<?php

define('AJAX_SCRIPT', TRUE);
require_once('../../config.php');
require_once($CFG->libdir.'/phpmailer/class.phpmailer.php'); //required
require_once($CFG->dirroot.'/blocks/helpdesk/lib.php');

//var_dump($_REQUEST);
$context = required_param('context', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

require_login(null, false, null, false, true);

confirm_sesskey();

global $USER, $DB, $PAGE, $COURSE;

//query the database for all the data needed

$query_user = "SELECT u.email, u.firstname, u.lastname
				FROM {course} c
				JOIN {context} ctx ON c.id = ctx.instanceid
				JOIN {role_assignments} ra ON ra.contextid = ctx.id
				JOIN {user} u ON u.id = ra.userid
				JOIN {role} r ON r.id = ra.roleid
				WHERE (r.shortname = ? OR r.shortname = ?) AND c.id = ?";

$params = array('editingteacher', 'teacher', $courseid);

$user_data = $DB->get_records_sql($query_user, $params);

$mail = new PHPmailer();
$mail->WordWrap = 50;	// Set word wrap to 50 characters
$mail->isHTML(true);	// Set email format to HTML
$mail->SetFrom($CFG->noreplyaddress, 'No-reply');
$mail->Subject = get_string('subject', 'block_helpdesk');
$mail->AltBody = get_string('altbody', 'block_helpdesk');

//little hack to handle email recipients, we need one entry of AddAddress type
$cnt = 0;

foreach ($user_data as $user) {

	if ($cnt == 0) {
		$mail->AddAddress($user->email, $user->firstname." ".$user->lastname);
	} else {
		$mail->AddCC($user->email, $user->firstname." ".$user->lastname);
	}

	$cnt++;

	$mail->Body =  get_string('hi', 'block_helpdesk') . $user->firstname . " " . $user->lastname . ",";

}

$body = generate_email($context, $courseid);

$mail->Body = $mail->Body.$body;

if(!$mail->Send()) {
  $result = false;
  echo($mail->ErrorInfo);
} else {
  $result = true;
}	

if (request_is_ajax()) {
    header('Content-Type: application/json');
	echo json_encode(array('result' => $result));

} else {

    $page = required_param('page', PARAM_TEXT);
    $pageurl = new moodle_url($page . '?id=' . get_book_id($context));
    
    //redirect($pageurl);
    //echo json_encode(array('result' => $result));
    header('Location: '.$pageurl);
    exit;
}