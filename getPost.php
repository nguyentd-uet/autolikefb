<?php
 header('Content-Type: text/html; charset=utf-8');
require_once 'configfb.php';
//$_POST['accessToken'] = 'CAAVnt9JH4IYBAMWZBuTTgjREzyZBMVrZAIkAl4PplzOM15kkSMGegcIdf6toZBfEMfBIbutHulMBkQT2WOgBkDoTdiA7pkY6RghCSdnQkBZBgk7qi32qmcVCAJYUYe3ZBxYu1YZAKLsJZB7IvANx6SeUetfoj6YQdfgIWMea8JrKZALYq2dmF9hKl3LYu326IlBzRbiPh6tteuwZDZD';
if(isset($_POST['accessToken'])){
	$data ='';
	$accessToken = $_POST['accessToken'];
	$longLiveAcessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
	$fb->setDefaultAccessToken($longLiveAcessToken);
	$query = '/me/feed?fields=picture,message,id&limit=30';
	$request = $fb->get($query);
	$graph = $request->getGraphEdge();
	foreach ($graph as $key => $value) {
		$info = $value->asArray();
		$data .= '<tr class="showPost">';
		$data .='<td class="hiddenID">'.$info['id'].'</td>';
		if(isset($info['picture'])){
			$data .='<td class="image"><img src="'.$info['picture'].'" alt="">'.'</td>';
		} else {
			$data .='<td class="image"><img src="noimage.png" alt="">'.'</td>';
		}
		if(isset($info['message'])){
			if(strlen($info['message']) > 250){
			$data .= '<td class="message">'.substr($info['message'],0,200).'</br>.....</td>';
		} else {
			$data .= '<td class="message">'.$info['message'].'</td>';
		}
	} else {
		$data .= '<td class="message">no message</td>';
	}
		
		
		$data .= '</tr>';
	}
}

 ?>

<!DOCTYPE html>
<html>
<head>
	<title></title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>
<body>

</body>
</html>
	
		<thead>
			<tr>
				<th class="hiddenID">ID</th>
				<th class="imgage">Image</th>
				<th class="message">Message</th>
			</tr>
		</thead>
		<tbody>
			<?php echo $data ?>
		</tbody>
