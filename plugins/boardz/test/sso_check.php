<?php
/**
 * @package    local_etl
 * @author     Valery Fremaux <valery.Fremaux@club-internet.fr>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright  (C) 1999 onwards Martin Dougiamas  http://dougiamas.com
 *
 * This utility file allows to : 
 * - generate a certificate to test keyed extraction credentials
 * - generate a crypted test ticket to be used on extractor
 * - give a link to test the GET webservice
 */

require_once('../../../../../config.php');
require_once($CFG->dirroot.'/local/etl/lib.php');
require_once($CFG->dirroot.'/local/etl/plugins/boardz/locallib.php');
require_once($CFG->dirroot.'/local/etl/plugins/boardz/extractor.class.php');

$systemcontext = context_system::instance();
$url = new moodle_url('/local/etl/plugins/boardz/tests/sso_check.php');
$PAGE->set_context($systemcontext);

// Security checks. Non admins CANNOT use as backdoor
require_login();
require_capability('moodle/site:config', $systemcontext);

$PAGE->set_url($url);
$PAGE->set_heading(get_string('sslcheck', 'localetl_boardz'));
$PAGE->set_pagelayout('admin');
$PAGE->navbar->add('BOARDZ ETL Access check');

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('sslcheck', 'localetl_boardz'));

echo $OUTPUT->box_start();
echo get_string('sslcheckadvice', 'localetl_boardz');
echo $OUTPUT->box_end();

$id = optional_param('boardz', 1, PARAM_INT);

$new_key = openssl_pkey_new(array('private_key_bits' => 2048, 'config' => $CFG->opensslcnf));

$dn = array(
   "countryName" => "FR",
   "stateOrProvinceName" => "VO",
   "localityName" => "Paris",
   "organizationName" => "ActiveProLearn",
   "organizationalUnitName" => 'Moodle',
   "commonName" => $CFG->wwwroot,
   "emailAddress" => "valery@activeprolearn.com" );
$dn["commonName"] = preg_replace(':/$:', '', $dn["commonName"]);

// make a certificate for signing and making public part

$csr_rsc = openssl_csr_new($dn, $new_key, array('private_key_bits' => 2048, 'config' => $CFG->opensslcnf));
$selfSignedCert = openssl_csr_sign($csr_rsc, null, $new_key, 365, array('private_key_bits' => 2048, 'config' => $CFG->opensslcnf));
unset($csr_rsc); // Free up the resource

openssl_x509_export($selfSignedCert, $string_cert);
openssl_x509_free($selfSignedCert);

$priv = openssl_pkey_get_private($new_key);
openssl_pkey_export($priv, $outp);

echo "<pre>";
echo $outp;
echo "</pre>";
echo "<pre>";
echo $string_cert;
echo "</pre>";

if (!$DB->record_exists('localetl_boardz', array('id' => $id))) {
    $boardz = new StdClass();
    $boardz->id = $id;
    $boardz->publickey = $string_cert;
    $boardz->wwwroot = $CFG->wwwroot;
    $DB->insert_record('localetl_boardz', $boardz);
} else {
    $DB->set_field('localetl_boardz', 'publickey', $string_cert, array('id' => $id));
}

$info = new StdClass();
$info->from = 0;
$info->query = optional_param('query', 'special_test', PARAM_TEXT);
$ticket = boardz_make_ticket($info, $priv, 'rsa');

echo '<pre>';
echo chunk_split(bin2hex($ticket),64, "\n");
echo '</pre>';

echo '<br/>';
$actionurl = new moodle_url('/local/etl/get.php');
echo '<form name="launchform" method="POST" action="'.$actionurl.'">';
echo '<input type="hidden" name="key" value="'.urlencode($ticket).'" />';
echo '<input type="hidden" name="id" value="'.$id.'" />';
echo '<input type="hidden" name="plugin" value="boardz" />';
echo '<input type="hidden" name="method" value="rsa" />';
echo '<input type="hidden" name="query" value="extract_actions" />';
echo '<input type="checkbox" name="mode" value="test" /> '.get_string('testmode', 'localetl_boardz').'<br/>';
echo '<input type="submit" name="go_btn" value="'.get_string('testextraction', 'localetl_boardz').'" />';
echo '</form>';

echo $OUTPUT->footer();