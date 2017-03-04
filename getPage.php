<?php
 header('Content-Type: text/html; charset=utf-8');
require_once 'configfb.php';
if(isset($_POST['accessToken'])){
	if(isset($_POST['postId'])) {
		$data = '';
		$postId = $_POST['postId'];
		$accessToken = $_POST['accessToken'];
		$longLiveAcessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		$fb->setDefaultAccessToken($longLiveAcessToken);
		$query = $postId.'?fields=name,page_story_id,picture';
		$request = $fb->get($query);
		$info = $request->getGraphNode()->asArray();
		$message = $info['name'];
		$urlImage = $info['picture'];
		if(isset($info['page_story_id'])) $id = $info['page_story_id'];
		else $id = $postId;
		echo $message.'$$$'.$urlImage.'$$$'.$id;
	}
}

 ?>