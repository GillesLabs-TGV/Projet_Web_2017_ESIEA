<?php
/**
 * Created by PhpStorm.
 * User: Taiki
 * Date: 23/10/2017
 * Time: 16:12
 */

include_once('../db.inc.php');
include_once('../config.inc.php');
include_once('../utils.inc.php');

function getMessages($input)
{
	$nbMessages = $input['nbSuggestions'];
	if(!is_int($nbMessages))
		$nbMessages = $GLOBALS['defaultNbMessages'];

	$wantOnlyNewMessages = !emtpy($input['lastMaxId']);

	$bdd = connectDB();

	if($wantOnlyNewMessages)
	{
		$req =$bdd->prepare("SELECT * FROM `chatbox` WHERE `messageID` > :1 ORDER BY `messageID` DESC LIMIT $nbMessages");
		$req->execute(array(':1' => $input['lastMaxId']));
	}
	else
	{
		$req = $bdd->prepare("SELECT * FROM `chatbox` ORDER BY `messageID` DESC LIMIT $nbMessages");
		$req->execute();
	}

	echo organizeMessage($bdd, $req);

	$req->closeCursor();
}

function sendMessage($input)
{
	if(empty($input['message']))
		return;

	insertMessageIntoDB($input['username'], $input['message']);
	exit('{"status":"success"}');
}

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'),true);

switch ($method)
{
	case 'GET':
	{
		getMessages($input);
		break;
	}
	case 'POST':
	{
		sendMessage($input);
		break;
	}
}