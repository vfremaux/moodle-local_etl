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
 * @package     local_etl
 ù @category    local
 * @author      Valery Fremaux <valery@valeisti.fr>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright   (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 */
defined('MOODLE_INTERNAL') || die;

if ($hassiteconfig) {
    $settings = new admin_settingpage('local_etl', get_string('pluginname', 'local_etl'));
    $ADMIN->add('localplugins', $settings);

    $label = get_string('pluginname', 'local_etl');
    $pageurl = new moodle_url('/local/etl/index.php"');
    $ADMIN->add('reports', new admin_externalpage('localetlext', $label, $pageurl, 'moodle/site:config'));

    $key = 'local_etl/maxxmlrecordsperget';
    $label = get_string('configmaxxmlrecordsperget', 'local_etl');
    $desc = get_string('configmaxxmlrecordsperget_desc', 'local_etl');
    $default = 5000;
    $settings->add(new admin_setting_configtext($key, $label, $desc, $default));
}
