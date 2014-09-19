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
 * Define the helpdesk block's class
 *
 * @package    	block_helpdesk
 * @author 		Ivana Skelic, Hrvoje Golcic
 * @copyright	2014 IPA "Let's Study Together!"" project
 * @license    	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/phpmailer/class.phpmailer.php'); //required
//require_once($CFG->libdir.'/chromephp/ChromePhp.php');//izbrisati kasnije


/**
 * helpdesk block class
 */
class block_helpdesk extends block_base {
	function init() {
		$this->title = get_string('pluginname', 'block_helpdesk');
	}

    function has_config() {
        return false;
    }

    /**
     * Disable multiple instances of this block
     * @return bool Returns false
     */
    function instance_allow_multiple() {
        return false;
    }

    /**
	 * Set where the block should be allowed to be added
	 * @return array
	 */
	public function applicable_formats() {
		return array('all' => true);
	}

	/**
	 * Set the content of the block
	 * @return string
	 */
	function get_content(){
		global $COURSE, $PAGE, $USER, $OUTPUT;

		if ($this->content !== NULL) {
			return $this->content;
		}

		if (!isloggedin() or isguestuser()) {
            return '';      // Never useful unless you are logged in as real users
        }

        $this->page->requires->js('/blocks/helpdesk/sendemail.js');

		$this->content = new stdClass;
		$this->content->text = '';
		$this->content->footer = '';
		
		if (empty($this->instance)) {
			return $this->content;
		}

		$context = context_module::instance($COURSE->id);
		$courseid = array($COURSE->id);
		
		require_capability('block/helpdesk:cansend', $context);

		$pageurl = $PAGE->url;

		$divattrs = array('id' => 'helpdesk', 'class' => 'content');

		$this->content->text .= html_writer::start_tag('div', $divattrs);
		$this->content->text .= get_string('badstructure', 'block_helpdesk');
		$this->content->text .= html_writer::end_tag('div');

		if (has_capability('block/helpdesk:cansend', $context) && (strpos($pageurl, 'book'))) {

			$this->content->text .= html_writer::start_tag('div');
			$this->content->text .= $OUTPUT->action_link('/blocks/helpdesk/sendmail.php', get_string('composenew', 'block_helpdesk'), new component_action('click', 'block_helpdesk_sendemail'));
			$this->content->text .= html_writer::end_tag('div');
			
		} else {
			$this->content->text .= html_writer::start_tag('div');
			$this->content->text .= get_string('link', 'block_helpdesk');
			$this->content->text .= html_writer::end_tag('div');
		}

		return $this->content;
	}

}