<?php
require_once __DIR__ . '/vendor/autoload.php';

use Alfred\Workflow;
use Guzzle\Http\Client;

$wf = new Workflow('com.ryanparman.workflow.redbox');
$http = new Client();

$query = stripslashes($argv[1]);

$request = $http->get("https://content.atomz.com/autocomplete/sp10/04/a1/b7/?query=" . urlencode($query));
$r = $request->send()->getBody(true);

// Do some cleanup
$r = trim(substr(trim($r), 1, -1));
$r = preg_replace('/\s+/i', ' ', $r);
$r = json_decode($r, true);

if (count($r) > 0)
{
	foreach ($r as $i => $result)
	{
		$wf->result(array(
			'uid' => sha1($query . $i . time()),
			'arg' => rawurlencode($result),
			'title' => ucwords($result),
			'subtitle' => "Search Redbox for " . ucwords($result),
			'icon' => __DIR__ . '/icon.png',
			'valid' => 'yes',
		));
	}
}
else
{
	$wf->result(array(
		'uid' => sha1($query . $i . time()),
		'arg' => rawurlencode($query),
		'title' => 'No results found for "' . ucwords($query) . '"',
		'subtitle' => "Try using fewer keywords.",
		'icon' => __DIR__ . '/icon.png',
		'valid' => 'no',
	));
}

echo $wf->toXML();
