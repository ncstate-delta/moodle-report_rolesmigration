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
 * Displays the form for the second stage of the Import Roles process
 * @package   moodlerolesmigration
 * @copyright 2011 NCSU DELTA | Developed by Glenn Ansley <glenn_ansley@ncsu.edu>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Existing roles in this installation
$existing_roles = $DB->get_records('role');
$incoming_roles = roles_migration_get_incoming_roles($xml);

// POST Values
$roles_to_create    = optional_param('to_create', array(), PARAM_RAW_TRIMMED);
$roles_to_replace   = optional_param('to_replace', array(), PARAM_RAW_TRIMMED);

$pre_import_form = '<form method="post" action="" >';

$table = new html_table();
$table->tablealign = 'center';
$table->align = array('right', 'left', 'left', 'left');
$table->wrap = array('nowrap', '', 'nowrap', 'nowrap');
$table->cellpadding = 5;
$table->cellspacing = 0;
$table->width = '90%';
$table->data = array();

$table->head = array(
    get_string('name'),
    get_string('shortname'),
    get_string('action')
);

if (is_array($incoming_roles)) {

    foreach ($incoming_roles as $role) {

        $row = array();
        $row[0] = $role->name;
        $row[1] = $role->shortname;

        $skip_checked = (isset($actions[$role->shortname]) && 'skip' ==  $actions[$role->shortname]) ? 'checked="checked"' : '';
        $create_checked = (isset($actions[$role->shortname]) && 'create' == $actions[$role->shortname]) ? 'checked="checked"' : '';
        $replace_checked = (isset($actions[$role->shortname]) && 'replace' == $actions[$role->shortname]) ? 'checked="checked"' : '';

        $new_value = isset($roles_to_create[$role->shortname]) ? $roles_to_create[$role->shortname] : $role->shortname;

        $options = '';
        $replace_options = '';
        foreach ($existing_roles as $er) {
            if (isset($incoming_roles[$role->shortname])) {
                if ($incoming_roles[$role->shortname] == $er->shortname) {
                    $selected = ' selected="selected" ';
                }
            } elseif ($role->shortname == $er->shortname) {
                $selected = ' selected="selected" ';
            } else {
                $selected = '';
            }
            $options .= "<option {$selected} value=\"{$er->shortname}\"> {$er->name} ({$er->shortname})</option>";
        }

        $row[2]  =   '<ul style="list-style-type: none;">';
        $row[2] .=       '<li>';
        $row[2] .=           '<input type="radio" '.$skip_checked.' id="skip'.$role->id.'" name="actions['.$role->shortname.']" value="skip" />';
        $row[2] .=           '<label for="skip'.$role->id.'">'.get_string('do_not_import', 'report_rolesmigration').'</label>';
        $row[2] .=       '</li>';
        $row[2] .=       '<li>';
        $row[2] .=           '<input type="radio" '.$create_checked.' id="create'.$role->id.'" name="actions['.$role->shortname.']" value="create" />';
        $row[2] .=           '<label for="create'.$role->id.'">'.get_string('import_new', 'report_rolesmigration').'</label>';
        $row[2] .=           '<input type="text" name="to_create['.$role->shortname.']" value="'.$new_value.'" />';
        $row[2] .=       '</li>';
        $row[2] .=       '<li>';
        $row[2] .=           '<input type="radio" '.$replace_checked.' id="replace'.$role->id.'" name="actions['.$role->shortname.']" value="replace" />';
        $row[2] .=           '<label for="replace'.$role->id.'">'.get_string('import_replacing', 'report_rolesmigration').'</label>';
        $row[2] .=           '<select name="to_replace['.$role->shortname.']" >'.$options.'</select>';
        $row[2] .=       '</li>';
        $row[2] .=   '</ul>';

        $table->data[] = $row;
    }
    $pre_import_form .= html_writer::table($table); 
    $pre_import_form .= '<input type="submit" value="'.get_string('next_step', 'report_rolesmigration').'" />';
    $pre_import_form .= '<input type="hidden" name="import_stage" value="performing_import"/>';
    $pre_import_form .= '</form>';

} else {
    $pre_import_form = 'No roles found in import file.';
}
