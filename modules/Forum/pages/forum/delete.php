<?php 
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-dev
 *
 *  License: MIT
 *
 *  Delete topic
 */
 
if(!$user->isLoggedIn()){
	Redirect::to(URL::build('/forum'));
	die();
}

require('modules/Forum/classes/Forum.php');

// Always define page name
define('PAGE', 'forum');

$forum = new Forum();

// Check params are set
if(!isset($_GET["tid"]) || !is_numeric($_GET["tid"])){
	Redirect::to(URL::build('/forum'));
	die();
} else {
	$topic_id = $_GET["tid"];
}

// Check topic exists
$topic = $queries->getWhere('topics', array('id', '=', $topic_id));

if(!count($topic)){
	Redirect::to(URL::build('forum'));
	die();
}

$topic = $topic[0];

if($forum->canModerateForum($user->data()->group_id, $topic->forum_id)){
	try {
		$queries->update('topics', $topic_id, array(
			'deleted' => 1
		));
		
		$posts = $queries->getWhere('posts', array('topic_id', '=', $topic_id));
		
		if(count($posts)){
			foreach($posts as $post){
				$queries->update('posts', $post->id, array(
					'deleted' => 1
				));
			}
		}

		// Update latest posts in forums
		$forum->updateForumLatestPosts();

		Redirect::to(URL::build('/forum'));
		die();
	} catch(Exception $e) {
		die($e->getMessage());
	}
} else {
	Redirect::to(URL::build('/forum'));
	die();
}