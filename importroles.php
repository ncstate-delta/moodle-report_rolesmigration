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
 * Prints the Import Roles page along with appropriate forms and / or actions
 * @package   moodlerolesmigration
 * @copyright 2011 NCSU DELTA | <http://delta.ncsu.edu>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once('importroles_form.php');
require_once('lib.php');

// Grab site course and coursecontext
$course = clone($SITE);
$context = get_context_instance(CONTEXT_SYSTEM, $course->id);   // Course context
$contextid = $context->id;
$filecontextid = optional_param('filecontextid', 0, PARAM_INT);

// Import Stage
$import_stage   = optional_param('import_stage', 'uploading_import_file', PARAM_ALPHAEXT);

// file parameters
// non js interface may require these parameters
$component  = optional_param('component', null, PARAM_ALPHAEXT);
$filearea   = optional_param('filearea', null, PARAM_ALPHAEXT);
$itemid     = optional_param('itemid', null, PARAM_INT);
$filepath   = optional_param('filepath', null, PARAM_PATH);
$filename   = optional_param('filename', null, PARAM_FILE);

list($context, $course, $cm) = get_context_info_array($contextid);

//require_login($course, false, $cm);
require_capability('moodle/role:manage', $context);
admin_externalpage_setup('importroles');

// check if tmp dir exists
$tmpdir = $CFG->dataroot . '/admin/report/rolesmigration/temp/';
if (!check_dir_exists($tmpdir, true, true)) {
    throw new restore_controller_exception('cannot_create_backup_temp_dir');
}

echo $OUTPUT->header();

// require uploadfile cap to use file picker
if (has_capability('moodle/restore:uploadfile', $context)) {
    echo $OUTPUT->heading(get_string('importfile', 'backup'));
    echo $OUTPUT->container_start();

    // Delegate form steps
    switch ($import_stage) {
        case 'performing_import' :
            require_once(dirname(__FILE__).'/do-import.php');
            $r = $CFG->wwwroot . '/' . $CFG->admin . '/roles/manage.php'; 
            echo '<p>'.get_string('link_to_define_roles', 'report_rolesmigration', $r), '</p>';
            break;
        case 'uploading_import_file' :
        default :
          require_once(dirname(__FILE__).'/upload.php');
          break;
    }

    echo $OUTPUT->container_end();
}

echo $OUTPUT->footer();
