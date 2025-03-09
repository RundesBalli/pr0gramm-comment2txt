<?php
/**
 * pr0gramm comment2txt
 * 
 * CLI-Tool to export all comments of a specified user on the German imageboard pr0gramm.com.
 * 
 * @author    RundesBalli <rundesballi@rundesballi.com>
 * @copyright 2025 RundesBalli
 * @version   1.1.0
 * @see       https://github.com/RundesBalli/pr0gramm-comment2txt
 * @license   MIT-License
 */

/**
 * Including the apiCall
 * @see https://github.com/RundesBalli/pr0gramm-apiCall
 */
require_once('/path/to/apiCall.php');

/**
 * Function to validate usernames from pr0gramm.com
 * @see https://github.com/RundesBalli/regex-functions/blob/master/pr0gramm/validUsername.php
 * 
 * @param string The username to be checked.
 * 
 * @return string/boolean On success the validated username will be returned, if not FALSE.
 */
function validUsername(string $username) {
  $regex = '/^[a-zA-Z0-9-_]{2,32}$/';
  return (preg_match($regex, trim($username), $matches) === 1) ? $matches[0] : FALSE;
}

/**
 * Initialize the output.
 */
$output = '';
$comments = [];

/**
 * Validate username from arguments.
 */
if(!isset($argv[1]) OR empty($argv[1])) {
  die('You have to enter a valid username.'."\n");
}

$username = validUsername($argv[1]);
if($username === FALSE) {
  die('You have to enter a valid username.'."\n");
}

$response = apiCall('https://pr0gramm.com/api/profile/info/?name='.$username);
if(!empty($response['code']) AND $response['code'] == 404) {
  die('The username you\'ve entered is not existent on pr0gramm.com.'."\n");
}

/**
 * Validate flags from arguments.
 */
if(!isset($argv[2]) OR empty($argv[2])) {
  die('You have to enter a valid flag (31 for "all").'."\n");
}

$flags = intval($argv[2]);
if($flags == 0 OR $flags < 1 OR $flags > 31) {
  die('You have to enter a valid flag (31 for "all").'."\n");
}

/**
 * Flags and flag names.
 */
$flagNames = [
  1  => 'SFW',
  2  => 'NSFW',
  4  => 'NSFL',
  8  => 'NSFP',
  16 => 'POL',
];
$flagsBin = strrev(str_pad(decbin($flags), 5, 0, STR_PAD_LEFT));
$flagArray = [];
if($flagsBin[0]) { $flagArray[] = $flagNames[1]; }
if($flagsBin[1]) { $flagArray[] = $flagNames[2]; }
if($flagsBin[2]) { $flagArray[] = $flagNames[4]; }
if($flagsBin[3]) { $flagArray[] = $flagNames[8]; }
if($flagsBin[4]) { $flagArray[] = $flagNames[16]; }

/**
 * Heading and informations
 */
$output.= '==================================================='."\n";
$output.= '        RundesBalli\'s pr0gramm-comment2txt         '."\n";
$output.= 'https://github.com/RundesBalli/pr0gramm-comment2txt'."\n";
$output.= '==================================================='."\n";
$output.= 'Crawl '.$username.' (https://pr0gramm.com/user/'.$username.')'."\n";
$output.= 'Flags: '.$flags.' ('.implode('+', $flagArray).')'."\n";
$output.= '==================================================='."\n";
$output.= 'Started crawling at '.date('d.m.Y, H:i:s')."\n";
$output.= '==================================================='."\n\n";
echo $output;

/**
 * Initialise the search parameters for the first API query.
 */
$before = 9999999999;
$hasOlder = TRUE;

/**
 * Crawl...
 */
do {
  $response = apiCall('https://pr0gramm.com/api/profile/comments?name='.$username.'&flags='.$flags.'&before='.$before);
  echo 'apiCall: "https://pr0gramm.com/api/profile/comments?name='.$username.'&flags='.$flags.'&before='.$before."\n";
  if($response['hasOlder'] === FALSE) {
    $hasOlder = FALSE;
  }
  foreach($response['comments'] AS $key => $content) {
    $commentOutput = 'https://pr0gramm.com/new/'.$content['itemId'].':comment'.$content['id']."\n";
    $commentOutput.= date('d.m.Y, H:i:s', $content['created']).' / '.($content['up']-$content['down']).' Score'."\n";
    $commentOutput.= '---------------------------------------------------'."\n";
    $commentOutput.= $content['content']."\n";
    $comments[] = $commentOutput;
    if($content['created'] < $before) {
      $before = $content['created'];
    }
  }
} while($hasOlder == TRUE);

/**
 * Prepare comments for output.
 */
$output.= implode("\n".'==================================================='."\n\n", $comments);
$output.= "\n".'==================================================='."\n\n";
$output.= 'Finished crawling at '.date('d.m.Y, H:i:s')."\n";
echo "\n".'Done. ('.date('d.m.Y, H:i:s').')'."\n";

/**
 * Put output to file.
 */
$fp = fopen(__DIR__.DIRECTORY_SEPARATOR.'comments_'.$username.'_f'.$flags.'_'.date('Ymd_His').'.txt', 'w');
fwrite($fp, str_replace("\r", '', $output));
fclose($fp);
?>
