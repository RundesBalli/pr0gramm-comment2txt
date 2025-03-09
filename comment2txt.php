<?php
/**
 * pr0gramm comment2txt
 * 
 * @author    RundesBalli <rundesballi@rundesballi.com>
 * @copyright 2019 RundesBalli
 * @version   1.0.0 - 14. JUN 2019
 * @see       https://github.com/RundesBalli/pr0gramm-comment2txt
 * @license   MIT-License
 */

/**
 * Einbinden des API-Calls.
 */
require_once('/path/to/apiCall.php');

/**
 * Ausführliche Ausgabe
 * 
 * @var bool
 */
$verbose = TRUE;

/**
 * Function to validate usernames from pr0gramm.com
 * @see https://github.com/RundesBalli/regex-functions/blob/master/pr0gramm/validUsername.php
 * 
 * @param string The username to be checked.
 * 
 * @return string/boolean On success the validated username will be returned, if not FALSE.
 */
function validUsername($username) {
  $regex = '/^[a-zA-Z0-9-_]{2,32}$/';
  return (preg_match($regex, trim($username), $matches) === 1) ? $matches[0] : FALSE;
}

/**
 * Function to validate flags from pr0gramm.com
 * @see https://github.com/RundesBalli/regex-functions/blob/master/pr0gramm/validFlag.php
 * 
 * @param int The flag to be checked.
 * 
 * @return string/boolean On success the validated flag will be returned, if not FALSE.
 */
function validFlag($flag) {
  $regex = '/^([1-9]|1[0-5])$/';
  return (preg_match($regex, trim($flag), $matches) === 1) ? $matches[0] : FALSE;
}

/**
 * Initialisieren des Output
 */
$output = '';
$comments = array();

/**
 * Username aus den Script-Argumenten validieren.
 */
if(isset($argv[1]) AND !empty($argv[1])) {
  $username = validUsername($argv[1]);
  if($username === FALSE) {
    die('Bitte gültigen Username angeben [a-zA-Z0-9-_]{2,32}'."\n");
  }
} else {
  die('Bitte Usernamen angeben.'."\n");
}

/**
 * Flags aus den Script-Argumenten validieren.
 */
if(isset($argv[2]) AND !empty($argv[2])) {
  $flags = validFlag($argv[2]);
  if($flags === FALSE) {
    die('Bitte gültige Flags eingeben.'."\n".'Beispiel: 15 (all)'."\n");
  }
} else {
  die('Bitte Flags eingeben.'."\n");
}

/**
 * Vorbereiten des Output
 */
$output.= '==================================================='."\n";
$output.= '        RundesBalli\'s pr0gramm-comment2txt         '."\n";
$output.= 'https://github.com/RundesBalli/pr0gramm-comment2txt'."\n";
$output.= '==================================================='."\n";
$output.= ($verbose === TRUE) ? 'Crawle '.$username.' (https://pr0gramm.com/user/'.$username.')'."\n" : 'Crawle '.$username."\n";
$flagarray = array(1 => "SFW", 2 => "NSFW", 3 => "SFW+NSFW", 4 => "NSFL", 5 => "SFW+NSFL", 6 => "NSFW+NSFL", 7 => "SFW+NSFW+NSFL", 8 => "NSFP", 9 => "SFW+NSFP", 10 => "NSFW+NSFP", 11 => "SFW+NSFW+NSFP", 12 => "NSFL+NSFP", 13 => "SFW+NSFL+NSFP", 14 => "NSFW+NSFL+NSFP", 15 => "ALL");
$output.= ($verbose === TRUE) ? 'Flags: '.$flags.' ('.$flagarray[$flags].')'."\n" : 'Flags: '.$flags.''."\n";
$output.= '==================================================='."\n";
$output.= 'Starte Crawling... ('.date('d.m.Y, H:i:s').')'."\n";
$output.= '==================================================='."\n";
echo $output;

/**
 * Initialisieren der Suchparameter für die erste comment-API-Abfrage.
 */
$before = 9999999999;
$hasOlder = TRUE;

/**
 * Crawlen...
 */
do {
  $response = apiCall('https://pr0gramm.com/api/profile/comments?name='.$username.'&flags='.$flags.'&before='.$before);
  echo ($verbose === TRUE) ? 'apiCall: "https://pr0gramm.com/api/profile/comments?name='.$username.'&flags='.$flags.'&before='.$before.'"\n' : NULL;
  if($response['hasOlder'] === FALSE) {
    $hasOlder = FALSE;
  }
  foreach($response['comments'] AS $key => $content) {
    $commentoutput = 'https://pr0gramm.com/new/'.$content['itemId'].':comment'.$content['id']."\n";
    $commentoutput.= date('d.m.Y, H:i:s', $content['created']).' Uhr / '.($content['up']-$content['down']).' Score'."\n";
    $commentoutput.= '---------------------------------------------------'."\n";
    $commentoutput.= $content['content']."\n";
    $comments[] = $commentoutput;
    if($content['created'] < $before) {
      $before = $content['created'];
    }
  }
} while($hasOlder == TRUE);

/**
 * Kommentare für Output vorbereiten und übernehmen.
 */
$output.= implode('==================================================='."\n", $comments);
$output.= '==================================================='."\n";
$output.= 'Crawling beendet. ('.date('d.m.Y, H:i:s').')'."\n";
echo 'Crawling beendet. ('.date('d.m.Y, H:i:s').')'."\n";

/**
 * Output in Datei übernehmen.
 */
$fp = fopen(__DIR__.DIRECTORY_SEPARATOR.'comments_'.$username.'_f'.$flags.'_'.date('Ymd_His').'.txt', 'w');
fwrite($fp, $output);
fclose($fp);
?>
