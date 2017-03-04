<?php
session_start();
set_time_limit(0);
require_once 'src/Facebook/autoload.php';
$fb = new Facebook\Facebook([
			'app_id' => '255778004872509',
			'app_secret' => '3dff8b00119ab28e894d0c76e5f5e390',
			'default_graph_version' => 'v2.5',
			]);
$oAuth2Client = $fb->getOAuth2Client();
 ?>