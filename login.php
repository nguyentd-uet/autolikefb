<?php
require_once('configfb.php');
$error = '';
 ?>

 <?php
 if(isset($_SESSION['longToken'])){
 	header("location:index.php");
 }
 else
 {
 	if(isset($_POST['btn'])){
	if($_POST['token']){
		$accessToken = $_POST['token'];
	
	try {
		$longLiveAcessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
		$_SESSION['longToken'] = (string) $longLiveAcessToken;
		header("location:login.php");
	} catch (Facebook\Exceptions\FacebookResponseException $ex) {
		$error = "Token Khong hop le";
	} catch (Facebook\Exceptions\FacebookSDKException $ex) {
		$error = 'Token khong hop le';
	}		
	
	} else {
		$error = "AccessToken is unvailable";
	}
}
 }

 ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<link rel="icon" href="icon.gif">
	<title>Login With AccessToken</title>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<style type="text/css">
	.panel-footer {
		background: #e67e22;
		text-align: center;
		font-style: bold;
		font-size: 20px;
	}
	.panel-footer span {
		color: white;
	}
	#loginForm {
		margin-top: 100px;
	}
	.panel-heading{
		font-weight: bold;
	}
	body {
		background-color: #34495e;
	}
	#linktoken {
		margin-top: 10px;
		margin-left: 100px;	
	}
	</style>

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>

</head>
<body>

		<div class="col-xs-4 col-xs-offset-4" id="loginForm">
		<div class="panel panel-primary">
			<div class="panel-heading text-center">Login With AccessToken</div>
			<div class="panel-body">
			<div id="login">
			<div align="center" class="has success"><span class="glyphicon glyphicon-pencil"></span>Nhập AccessToken</div>
			<hr>
			<div id="error" class="has-success" style="color:red"><?php echo $error ?></div>
			<br>
			<form action="login.php" method="POST" id="form-config">
				<div class="form-group">
					<label for="appid">Access Token</label>
					<input type="text" class="form-control" placeholder="Input Access Token" id="token" name="token">
				</div>
				<div></div>
				<button class="btn btn-primary btn-block" id="config" name="btn">Login</button>
			</form>
			<div id="linktoken"><a href="https://developers.facebook.com/tools/explorer/255778004872509" class="text-center" target="_blank" >---Link Lay token----</a></div>
			</div>
			<div id="result"></div>
			</div>	
			<div class="panel-footer panel-primary"><span>TEEMAZING</span></div>
		</div>
			
		</div>
</body>
</html>
