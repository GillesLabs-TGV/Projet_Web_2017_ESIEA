<?php
/**
 * Created by PhpStorm.
 * User: Taiki
 * Date: 12/09/2017
 * Time: 14:27
 */

function logError($log=null)
{
    if($log !== null)
    {
        if($file = fopen('security.log', 'a+'))
        {
            if(fprintf($file, '"'.date('H:i - d/M/Y - ').'","'.$_SERVER['REMOTE_ADDR'].'","'.$log."\"\n"))
            {
                fclose($file);
                return $log;
            }
        }
    }
    return false;
}

function organizeMessage($bdd, $result)
{
    $messages = array();
    $lastMessageID = 0;

    if($bdd == null)
    	$bdd = connectDB();
    
    if (count($result) > 0)
    {
    	//The DB access isn't hidden in a dedicated function so we can reuse more efficiently the request.
	    //Ideally, we would also cache the result as the chatbox probably only has a handful of active users.

        $lastMessageID = $result[0]['messageID'];
        $req = $bdd->prepare('SELECT `name` FROM `user` WHERE `ID` = ?1');

        foreach ($result as $row) 
        {
            if($req->execute(array('?1' => $row['userID'])))
            {
            	$metadata = $req->fetch();

	            $messages[] = array(
		            'messageText' => $row['messageText'],
		            'user' => $metadata['name'],
		            'time' => $row['time']
	            );
            }
        }
        $req->closeCursor();
    }

    if(!empty($messages))
    {
	    $chatBox = array(
	    	'status' => 'success',
		    'lastMessage' => $lastMessageID,
		    'messages' => array_reverse($messages)
	    );
    }
    else
    {
	    $chatBox = array(
		    'status' => 'error',
		    'error' => 'no new message'
	    );
    }

    return json_encode($chatBox);
}

function hashPasswordForUser($password)
{
	return password_hash($password, PASSWORD_BCRYPT);
}
