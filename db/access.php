<?php

$capabilities = array(
	'block/helpdesk:myaddinstance' => array(
		'captype' => 'write',
		'contextlevel' => CONTEXT_SYSTEM,
		'archetypes' => array(
			'user' => CAP_ALLOW
		),

		'clonepermissionfrom' => 'moodle/my:manageblocks'

	),

	'block/helpdesk:addinstance' => array(
		'risk_bitmask' => RISK_SPAM | RISK_XSS,

		'captype' => 'write',
		'contextlevel' => CONTEXT_BLOCK,
		'archetypes' => array(
			'student' => CAP_ALLOW
		),

		'clonepermissionfrom' => 'moodle/site:manageblocks'
	),

	'block/helpdesk:cansend' => array(
		'risk_bitmask' => RISK_SPAM | RISK_XSS,

		'captype' => 'write',
		'contextlevel' => CONTEXT_BLOCK,
		'archetypes' => array(
			'student' => CAP_ALLOW
		),

		'clonepermissionfrom' => 'moodle/site:manageblocks'
	),
);