<?php
session_start();
set_time_limit(0);
require_once 'src/Facebook/autoload.php';
$fb = new Facebook\Facebook([
			'app_id' => '',
			'app_secret' => '',
			'default_graph_version' => 'v2.5',
			]);
$oAuth2Client = $fb->getOAuth2Client();
 ?>
