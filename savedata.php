<?php 
set_time_limit(0);
require_once 'configdb.php';
require_once 'configfb.php';

date_default_timezone_set('Asia/Ho_Chi_Minh');
// Lay thong tin Post gui len
if(isset($_SESSION['longToken'])) {
	$postID = $_POST['postId'];
	$namePage = $_POST['namePage'];
	$namePost = $_POST['namePost'];
	$accessToken = $_POST['accessToken'];
	$link = $_POST['link'];
	$picture = $_POST['picture'];


// Lay Long Token cho Page
	try {
		$longLiveAcessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		 $accessToken = $longLiveAcessToken;
	} catch (Facebook\Exceptions\FacebookResponseException $ex) {
		echo "loi";
	
	} catch(Facebook\Exceptions\FacebookSDKException $ex){
		echo 'error';
	}

//
	// Setup cho fb
	 $fb->setDefaultAccessToken($accessToken);
	// Ket noi database

	/* Dem so Comment trong Post*/
	$totalcomment = totalCount($fb,$postID);
	/* Them vao Database*/
	$time = date("Y-m-d H:i");
	add2DB($conn,$postID,$namePost,$picture,$link,$namePage,$accessToken,'1',$totalcomment,'0','',$time);
	// fetchComment($conn,$accessToken,$postID);

	mysqli_close($conn);

} else {
	header("location:login.php");
	mysqli_close($conn);
}


/**
 * function them du lieu vao Table post
 */
function add2DB($conn,$postID,$namePost,$picture,$link,$namePage,$accessToken,$status,$totalcomment,$commentliked,$next,$time){
	$sql = "SELECT * FROM progress WHERE postid = '$postID'";
	$result = mysqli_query($conn,$sql);
	if(mysqli_num_rows($result) == 0){
		$query = 'INSERT INTO progress(postid,namepost,picture,link,namepage,access,status,totalcomment,commentliked,next,time) VALUES '.'("';
		$query .= $postID.'","'.$namePost.'","'.$picture.'","'.$link.'","'.$namePage.'","'.$accessToken.'","'.$status.'","'.$totalcomment.'","'.$commentliked.'","'.$next.'","'.$time.'")';
		//echo $query;
		
		if (mysqli_query($conn, $query)) {
		    echo "Thêm record thành công";
		} else {
		    echo "Lỗi: " . $sql . "<br>" . mysqli_error($conn);
		}
	} else {
		echo 'Post da ton tai';
		exit();
	}
	mysqli_free_result($result);
}

/**
 * Function Dem so comments
 */
function totalCount($fb,$postID){
	$query = '/'.$postID.'/comments?limit=0&summary=true';
	$request = $fb->get($query);
	$info = $request->getGraphEdge();
	return $info->getTotalCount();
};

/**
 *  function Them comment vao Table tuong ung
 */
// function fetchComment($conn,$accessToken,$postID){
// 	$graph = 'https://graph.facebook.com/v2.5/'.$postID.'/comments?limit=300&order=reverse_chronological&access_token='.$accessToken;
// 		$query = "CREATE TABLE IF NOT EXISTS `$postID`(     id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,     idcmt VARCHAR(50) NOT NULL UNIQUE KEY,     name VARCHAR(255),     status tinyint(1), repid VARCHAR(40), ut int(1))";
// 		//echo $graph;
// 		mysqli_query($conn,$query);
// 		$ch = curl_init();
// 		curl_setopt($ch,CURLOPT_URL,$graph);
// 		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
// 		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
// 		$request = curl_exec($ch);
// 		curl_close($ch);
// 		$response = json_decode($request,true);
// 		if(isset($response['paging']['next'])) $next = $response['paging']['next'];
// 		else $next = '';		
// 		echo $next;
// 		$sql = 'UPDATE post SET next = "'.$next.'" WHERE postid="'.$postID.'"';
// 		mysqli_query($conn,$sql);
// 		$data = $response['data'];
// 		foreach ($data as $comment) {
// 			$status = checkComment($comment['id']);
// 			$ut = checkUt($comment['message']);
// 			$insert = "INSERT INTO `$postID` (idcmt,name,status,ut) VALUES ('".$comment['id']."','".$comment['from']['name']."','".$status."','".$ut."')";
// 			//echo $insert;
// 			mysqli_query($conn,$insert);
// 		}
// 		//$insert = "INSERT INTO ".$postID."(idcmt,name,status) VALUES ('1','2','1')";
// 		//mysqli_query($conn,$insert);
// };



// function checkComment($idComment){
// 	$postID = $GLOBALS['postID'];

// 	$request = $GLOBALS['fb']->get("/$postID?fields=from")->getGraphNode()->asArray();
// 	$from = $request['from']['id'];
// 	$request = $GLOBALS['fb']->get('/'.$idComment.'/comments?order=reverse_chronological');
// 	$commentEdge = $request->getGraphEdge()->asArray();
// 	if(isset($commentEdge[0]['from']['id']) && $commentEdge[0]['from']['id'] == $from) return 1;
// 	else return 0;
// };

 ?>