<?php
header('Content-Type: text/html; charset=utf-8');
require 'configfb.php';
require 'configdb.php';

if(!isset($_SESSION['count'])){
	$_SESSION['count'] = 0;
}
if(isset($_SESSION['longToken'])){
	$sql = "SELECT * FROM progress WHERE status = 1";
	$result = mysqli_query($conn,$sql);
	$num = mysqli_num_rows($result);
		//Kiem tra databse co Post dang running khong
	if($num == 0){
		echo 'Khong co database';
	} else {
		if($_SESSION['count'] >= $num){
			$_SESSION['count'] = 0;
		}
		$sql = "SELECT * FROM progress WHERE status = 1 ORDER BY postid ASC LIMIT {$_SESSION['count']},5";
		//echo $sql."</br>";
		$result = mysqli_query($conn,$sql);
		while($rows = mysqli_fetch_assoc($result)){
			$namePost = $rows['namepost'];
			autoUpdate($rows['access'],$rows['postid'],$rows['totalcomment']);
			//sleep(30);
			autoLike($rows['access'],$rows['postid']);
		}
		$_SESSION['count'] = $_SESSION['count'] + 5;
	}

	mysqli_close($conn);
	

	$fb->setDefaultAccessToken($_SESSION['longToken']);
		

} else {
	echo 'Ban khong co quyen truy cap trang nay';
}



function getTotalComment($fb, $postid) 
{
	$query = '/'.$postid.'/comments?limit=0&summary=true';
	$request = $fb->get($query);
	$info = $request->getGraphEdge();	
	$totalcomment = $info->getTotalCount();
	return $totalcomment;
}


function autoUpdate($accessToken,$postid,$lastTotalComment){
	$GLOBALS['fb']->setDefaultAccessToken($accessToken);	
	$currentComment = getTotalComment($GLOBALS['fb'], $postid);
	if($currentComment > $lastTotalComment){
		echo $GLOBALS['namePost']." : Have ".($currentComment - $lastTotalComment)." new comment</br>";
		$sqlUpdate = "UPDATE progress SET totalcomment = ".$currentComment." WHERE postid = '".$postid."'";
		mysqli_query($GLOBALS['conn'],$sqlUpdate);
	} else {
		//$sqlUpdate = "UPDATE post SET totalcmt = ".$currentComment." WHERE postid = '".$postid."'";
		echo "Don't have new comment";
	}
}

function autoLike($accessToken,$postid){
	$GLOBALS['fb']->setDefaultAccessToken($accessToken);
	$sql = "SELECT * FROM progress WHERE postid = '".$postid."'";
	//echo $sql."</br>";
	$result = mysqli_query($GLOBALS['conn'],$sql);
	if(mysqli_num_rows($result) != 0){
		while($rows = mysqli_fetch_assoc($result) and $rows['totalcomment'] > $rows['commentliked']){		
			try {
				$index = $rows['totalcomment'] -$rows['commentliked'];
				$surplus = $index % 20;
				while($index >= 20) {
					postLike($rows['access'], $postid, 20);
				}
				if($index < 20) {
					postLike($rows['access'], $postid, $surplus);
				}
				
			} catch (Facebook\Exceptions\FacebookResponseException $ex) {
				echo $ex->getMessage()."</br>";
			} catch (Facebook\Exceptions\FacebookSDKException $ex) {
				echo $ex->getMessage();
			}
			
		}
	}
}

function postLike($accessToken, $postid, $limit)
{
	$GLOBALS['fb']->setDefaultAccessToken($accessToken);
	$sql = "SELECT * FROM progress WHERE postid ='".$postid."'";
	$result = mysqli_query($GLOBALS['conn'],$sql);
	$rows = mysqli_fetch_assoc($result);

	//Neu next = '' thi like 20 comments dau
	if($rows['next'] == '') {
		$query = '/'.$postid.'?fields=comments.limit('.$limit.'){likes{id}}';
		$request = $GLOBALS['fb']->get($query);
		$info = $request->getGraphNode()->asArray();
		$comments = $info['comments'];

		foreach ($comments as $comment) {
			
			$request = "/".$comment['id']."/likes";
			$response = $GLOBALS['fb']->post($request,[], $accessToken);
			usleep(rand(1500000,2500000));
				
		}
		$graph = 'https://graph.facebook.com/v2.5/'.$postid.'/comments?limit='.$limit.'&order=reverse_chronological&access_token='.$accessToken;
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$graph);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$request = curl_exec($ch);
		curl_close($ch);
		$response = json_decode($request,true);
		if(isset($response['paging']['next'])) {
			$next = $response['paging']['next'];
		} else $next = '';
	} else {
		$ch = curl_init();
		curl_setopt($ch,CURLOPT_URL,$rows['next']);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$request = curl_exec($ch);
		curl_close($ch);
		$result = json_decode($request,true); 
		foreach ($result['data'] as $comment) {
			
			$request = "/".$comment['id']."/likes";
			$response = $GLOBALS['fb']->post($request,[], $accessToken);
			usleep(rand(1500000,2500000));
				
		}
		if(isset($result['paging']['next'])) {
			$next = $result['paging']['next'];
		} else $next = '';
	}

	$commentliked = $rows['commentliked'] + $limit;
	$sql = 'UPDATE progress SET next = "'.$next.'",commentliked = "'.$commentliked.'" WHERE postid="'.$postid.'"';
	mysqli_query($GLOBALS['conn'],$sql);
	

}

?>