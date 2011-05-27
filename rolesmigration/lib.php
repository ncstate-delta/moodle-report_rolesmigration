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
 * @package   moodlerolesmigration
 * @copyright 2011 NCSU DELTA | <http://delta.ncsu.edu>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Serves rolesexport xml files.
 *
 * @param object $course
 * @param object $cm
 * @param object $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @return bool false if file not found, does not return if found - just send the file
 */
function report_rolesmigration_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload) {
    global $USER;
    //require_capability('mod/assignment:view', $this->context);

    $fullpath = "/{$context->id}/report_rolesmigration/$filearea/".implode('/', $args);
    
    $fs = get_file_storage();
    if (!$file = $fs->get_file_by_hash(sha1($fullpath)) or $file->is_directory()) {
        send_file_not_found();
    }

    if (($USER->id != $file->get_userid()) && !has_capability('moodle/role:manage', $context)) {
        send_file_not_found();
    }
    
    session_get_instance()->write_close(); // unlock session during fileserving
    if (  !send_stored_file($file, 60*60, 0, true) ) {
        send_file_not_found();
    }

}

/**
* Parses uploaded file (or POST args for xml roles
*/
function roles_migration_get_incoming_roles($xml=false) {
    global $USER;

    if ($xml) {
        if (isset($xml['MOODLE_ROLES_MIGRATION']['#']['ROLES'][0]['#']['ROLE'])) {
            $roles = $xml['MOODLE_ROLES_MIGRATION']['#']['ROLES'][0]['#']['ROLE'];
            foreach($roles as $key => $value) {
                $role_capabilities = array();
                // Add capabilities for role 
                if (isset($value['#']['ROLE_CAPABILITIES'])) {
                    foreach($value['#']['ROLE_CAPABILITIES'][0]['#']['ROLE_CAPABILITY'] as $rck => $rcv) {
                        $capability = new stdClass();
                        $capability->capability     = !empty($rcv['#']['CAPABILITY']) ? $rcv['#']['CAPABILITY'][0]['#'] : '';
                        $capability->permission     = !empty($rcv['#']['PERMISSION']) ? $rcv['#']['PERMISSION'][0]['#'] : '';
                        $role_capabilities[]        = $capability;
                    }
                }
                $role = new stdClass(); 
                $role->id           = !empty($value['#']['ID'][0]['#']) ? $value['#']['ID'][0]['#'] : '';
                $role->name         = !empty($value['#']['NAME'][0]['#']) ? $value['#']['NAME'][0]['#'] : '';
                $role->shortname    = !empty($value['#']['SHORTNAME'][0]['#']) ? $value['#']['SHORTNAME'][0]['#'] : '';
                $role->description  = !empty($value['#']['DESCRIPTION'][0]['#']) ? $value['#']['DESCRIPTION'][0]['#'] : '';
                $role->sortorder    = !empty($value['#']['SORTORDER'][0]['#']) ? $value['#']['SORTORDER'][0]['#'] : '';
                $role->archetype    = !empty($value['#']['ARCHETYPE'][0]['#']) ? $value['#']['ARCHETYPE'][0]['#'] : '';
                $role->capabilities = $role_capabilities;
                $to_return[]        = $role;
            }
        }
    } else if (isset($USER->rolesmigrationimport)) {
        return $USER->rolesmigrationimport;
    }
    if (!empty($to_return)) {
        $USER->rolesmigrationimport = $to_return;
        return $to_return;
    }
    return false;
}
?>
