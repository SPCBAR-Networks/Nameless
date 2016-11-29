<?php 
/*
 *	Made by Samerton
 *  https://github.com/NamelessMC/Nameless/
 *  NamelessMC version 2.0.0-dev
 *
 *  License: MIT
 *
 *  React to a post
 */

// Maintenance mode?
// Todo: cache this
$maintenance_mode = $queries->getWhere('settings', array('name', '=', 'maintenance'));
if($maintenance_mode[0]->value == 'true'){
	// Maintenance mode is enabled, only admins can view
	if(!$user->isLoggedIn() || !$user->canViewACP($user->data()->id)){
		require('modules/Forum/pages/forum/maintenance.php');
		die();
	}
}

require('modules/Forum/classes/Forum.php');
$forum = new Forum();

// User must be logged in to proceed
if(!$user->isLoggedIn()){
	Redirect::to(URL::build('/forum'));
	die();
}

// Deal with input
if(Input::exists()){
	// Validate form input
	if(!isset($_POST['post']) || !is_numeric($_POST['post']) || !isset($_POST['reaction']) || !is_numeric($_POST['reaction'])){
		Redirect::to(URL::build('/forum'));
		die();
	}
	
	// Get post information
	$post = $queries->getWhere('posts', array('id', '=', $_POST['post']));
	
	if(!count($post)){
		Redirect::to(URL::build('/forum'));
		die();
	}
	
	$post = $post[0];
	$topic_id = $post->topic_id;
	
	// Check user can actually view the post
	if(!($forum->forumExist($post->forum_id, $user->data()->group_id))){
		Redirect::to(URL::build('/forum/error/', 'error=not_exist'));
		die();
	}
	
	if(Token::check(Input::get('token'))){
		// Check if the user has already reacted to this post
		$user_reacted = $queries->getWhere('forums_reactions', array('post_id', '=', $post->id));
		if(count($user_reacted)){
			foreach($user_reacted as $reaction){
				if($reaction->user_given == $user->data()->id){
					if($reaction->reaction_id == $_POST['reaction']){
						// Undo reaction
						$queries->delete('forums_reactions', array('id', '=', $reaction->id));
					} else {
						// Change reaction
						$queries->update('forums_reactions', $reaction->id, array(
							'reaction_id' => $_POST['reaction'],
							'time' => date('U')
						));
					}
					
					$changed = true;
					break;
				}
			}
		}
		
		if(!isset($changed)){
			// Input new reaction
			$queries->create('forums_reactions', array(
				'post_id' => $post->id,
				'user_received' => $post->post_creator,
				'user_given' => $user->data()->id,
				'reaction_id' => $_POST['reaction'],
				'time' => date('U')
			));
		}

		// Redirect
		Redirect::to(URL::build('/forum/view_topic/', 'tid=' . $topic_id . '&pid=' . $post->id));
		die();
	} else {
		// Invalid token
		Redirect::to(URL::build('/forum/view_topic/', 'tid=' . $topic_id . '&pid=' . $post->id));
	}
} else {
	Redirect::to(URL::build('/forum'));
	die();
}