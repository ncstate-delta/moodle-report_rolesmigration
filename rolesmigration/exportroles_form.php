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
 * The form for the export roles process.
 * @package   moodlerolesmigration
 * @copyright 2011 NCSU DELTA | Developed by Glenn Ansley <glenn_ansley@ncsu.edu>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir.'/formslib.php');

class export_roles_form extends moodleform {
    function definition() {
        $mform =& $this->_form;
        $contextid = $this->_customdata['contextid'];

        $mform->addElement('hidden', 'export', ''); // Will be overwritten below

        $table = new html_table();
        $table->tablealign = 'center';
        $table->align = array('right', 'left', 'left', 'left');
        $table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
        $table->cellpadding = 5;
        $table->cellspacing = 0;
        $table->width = '90%';
        $table->data = array();

        $table->head = array(get_string('name'),
        get_string('description'),
        get_string('shortname'),
        get_string('export', 'report_rolesmigration'));

        $roles = get_all_roles();
        foreach ($roles as $role) {

        $row = array();
        $roleurl = new moodle_url('/admin/roles/define.php', array('roleid' => $role->id, 'action' => 'view'));
        $row[0] = '<a href="'.$roleurl.'">'.format_string($role->name).'</a>';
        $row[1] = format_text($role->description, FORMAT_HTML);
        $row[2] = ($role->shortname);
        //$row[3] = $mform->addElement('checkbox', 'export[]', '', '', array('value' => $role->shortname));
        $row[3] = '<input type="checkbox" name="export[]" value="'.$role->shortname.'" />';

        $table->data[] = $row;
        }

        $table = html_writer::table($table);
                                                   
        $mform->addElement('html', $table);
        $mform->addElement('hidden', 'contextid', $contextid);
        $mform->addElement('hidden', 'action', 'export');
        $submit_string = get_string('export', 'report_rolesmigration');
        $this->add_action_buttons(false, $submit_string);
    }
}
