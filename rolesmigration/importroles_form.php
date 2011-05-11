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
 * The form for step one of the Import Roles process.
 * @package   moodlerolesmigration
 * @copyright 2011 NCSU DELTA | Developed by Glenn Ansley <glenn_ansley@ncsu.edu>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php');

class rolesmigration_uploadimport_form extends moodleform {
    
    // Called when the form class is instantiated
    function definition() {
        $uploadimport_form =& $this->_form;

        // File Picker
        $uploadimport_form->addElement('filepicker', 'rolesimportfile', get_string('files'), null, array('accepted_types' => 'xml'));
        // Hidden field to identify stage of import
        $uploadimport_form->addElement('hidden', 'import_stage', 'uploading_import_file');
        // Submit buttons
        $this->add_action_buttons(false, get_string('next'));
        // Rules
        $uploadimport_form->addRule( 'rolesimportfile', null, 'required', null, 'server');
    }

}
