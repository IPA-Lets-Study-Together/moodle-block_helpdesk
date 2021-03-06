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
 * Define the structure_information block's class
 *
 * @package    	block_structure_information
 * @author 		Ivana Skelic, Hrvoje Golcic
 * @copyright	2014 IPA "Let's Study Together!"" project
 * @license    	http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/phpmailer/class.phpmailer.php'); //required

/**
 * structure_information block class
 */
class block_structure_information extends block_base {

	CONST JS_URL = '/blocks/helpdesk/sendemail.js';

	function init() {
		$this->title = get_string('pluginname', 'block_structure_information');
	}

	/*function user_can_addto($page) {
        // Don't allow people to add the block if they can't even use it
        if (!has_capability('moodle/helpdesk:add', $page->context)) {
            return false;
        }

        return parent::user_can_addto($page);
    }

    function user_can_edit() {
        // Don't allow people to edit the block if they can't even use it
        if (!has_capability('moodle/helpdesk:add',
                     context::instance_by_id($this->instance->parentcontextid))) {
            return false;
        }
        return parent::user_can_edit();
    }*/

    function has_config() {
        return true;
    }

    /**
     * Disable multiple instances of this block
     *
     * @return bool Returns false
     */
    function instance_allow_multiple() {
        return false;
    }

    /**
	 * Set where the block should be allowed to be added
	 *
	 * @return array
	 */
	public function applicable_formats() {
		return array('all' => true);
	}

	/**
	 * Set the content of the block
	 *
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

        //$this->page->requires->js('/blocks/helpdesk/sendemail.js');
		$this->content = new stdClass;
		$this->content->text = '';
		$this->content->footer = '';
		
		if (empty($this->instance)) {
			return $this->content;
		}


		$context = context_course::instance($this->page->course->id);
		
		require_capability('block/helpdesk:cansend', $context);

		//can not send moodle_url object as required param, send path instead
		$pageurl = (string)$PAGE->url;

		$divattrs = array('id' => 'helpdesk', 'class' => 'content1');

		$this->content->text .= html_writer::start_tag('div', $divattrs);

		$this->content->text .= html_writer::start_tag('div', array(
			'id' => 'helpdesk_txt', 
			'class' => ''
		));
		$this->content->text .= get_string('badstructure', 'block_helpdesk');
		$this->content->text .= html_writer::end_tag('div');


		if (stripos($pageurl, 'mod/book/')) {

			$divattr = array('id' => 'helpdesk_link');
			$this->content->text .= html_writer::start_tag('div', $divattr);
			
			// the following code changes previous line
			$this->content->text .= html_writer::start_tag('form', array(
				'method' => 'POST', 
				'action' => new moodle_url('/blocks/helpdesk/sendmail.php'),
				'id' => 'helpdesk_form'
				));

			$this->content->text .= html_writer::empty_tag('input', array(
				'name' => 'sesskey',
				'value' => sesskey(),
				'type' => 'hidden'
				));

			$this->content->text .= html_writer::empty_tag('input', array(
				'name' => 'context',
				'value' => (int)$PAGE->context->id,
				'type' => 'hidden'
				));

			$this->content->text .= html_writer::empty_tag('input', array(
				'name' => 'courseid',
				'value' => (int)$COURSE->id,
				'type' => 'hidden'
				));

			$this->content->text .= html_writer::empty_tag('input', array(
				'name' => 'page',
				'value' => $PAGE->url,
				'type' => 'hidden'
				));

			$this->content->text .= html_writer::tag('textarea', '', array(
				'name' => 'message',
				'placeholder' => get_string('input_txt', 'block_helpdesk')
				));

			$this->content->text .= html_writer::empty_tag('input', array(
				'name' => 'submit_button',
				'id' => 'helpdesk_submit',
				'value' => get_string('js_submit', 'block_helpdesk'),
				'type' => 'submit'
				));

			$this->content->text .= html_writer::end_tag('form');

			$this->content->text .= html_writer::end_tag('div');

			//include JS and JS strings
			$this->page->requires->string_for_js('input_txt', 'block_helpdesk');
			$this->page->requires->string_for_js('js_submit', 'block_helpdesk');

			$jsmodule = array(
					'name'  =>  'block_helpdesk',
					'fullpath'  =>  self::JS_URL,
					'requires'  =>  array('base', 'node')
				);

				// include js script and pass the arguments
			$this->page->requires->js_init_call('M.block_helpdesk.init', null, false, $jsmodule);

		} else {

			$divattr = array('id' => 'helpdesk_text');
			$this->content->text .= html_writer::start_tag('div', $divattr);
			$this->content->text .= get_string('link', 'block_helpdesk');
			$this->content->text .= html_writer::end_tag('div');
		}

		$this->content->text .= html_writer::end_tag('div');
		
		$this->content->text .= html_writer::start_tag('div', array(
			'class' => 'content2'
			));

		//success
		$this->content->text .= html_writer::start_tag('div', array(
			'id' => 'helpdesk_success', 
			'style' => 'display: none'
			));

		$this->content->text .= get_string('success', 'block_helpdesk');
		$this->content->text .= html_writer::end_tag('div');

		//failure
		$this->content->text .= html_writer::start_tag('div', array(
			'id' => 'helpdesk_failure', 
			'style' => 'display: none'
			));

		$this->content->text .= get_string('failure', 'block_helpdesk');
		$this->content->text .= html_writer::end_tag('div');

		$this->content->text .= html_writer::end_tag('div');

		return $this->content;
	}

}