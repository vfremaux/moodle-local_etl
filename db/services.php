<?php
// This file is NOT part of Moodle - http://moodle.org/
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
 * Web service for local_etl
 * @package    local_etl
 * @author     Valery Fremaux <valery.fremaux@gmail.com>
 * @copyright  2013 Valery Fremaux
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die;

$functions = array(

    'local_etl_get' => array(
            'classname'   => 'local_etl_external',
            'methodname'  => 'get',
            'classpath'   => 'local/etl/externallib.php',
            'description' => 'Get a feeder output',
            'type'        => 'read'
    ),

    'local_etl_get_sql' => array(
            'classname'   => 'local_etl_external',
            'methodname'  => 'get_sql',
            'classpath'   => 'local/etl/externallib.php',
            'description' => 'Get a customized sql output',
            'type'        => 'read'
    ),
);

$services = array(
    'Moodle Etl' => array(
        'functions' => array ('local_etl_get', 'local_etl_get_sql'), // Web service function names.
        'requiredcapability' => 'local/etl:export',
        'restrictedusers' => 1,
        'enabled' => 0, // Used only when installing the services.
    ),
);