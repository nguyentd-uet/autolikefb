<?php 
set_time_limit(0);
require("configfb.php");
require('configdb.php');
//Kiem tra Session
if(isset($_SESSION['longToken'])) {
	$accessToken = $_SESSION['longToken'];
	$fb->setDefaultAccessToken($accessToken);
	$response = $fb->get('/me');
	$info = $response->getGraphNode()->asArray();

	if(!isset($_SESSION['id'])){
		$_SESSION['id'] = $info['id'];
	} else {
		$dbbase = $_SESSION['id'];
	}


	// Lay Id,user,photo

	$response = $fb->get('/me?fields=picture,name,accounts.limit(500){picture,name,access_token}');
	$info = $response->getGraphNode()->asArray();
	$name = $info['name'];
	$url = $info['picture']['url'];
	$accounts = $info['accounts'];
	// var_dump($accounts);
	$pages = '';
	foreach ($accounts as $key => $value) {
		$namePage = $value['name'];
		$pageId = $value['id'];
		$access_token = $value['access_token'];
		$urlPages = $value['picture']['url'];
		$pages.= '<option id="'.$access_token.'" label="'.$pageId.'" value=\''.$urlPages.'\'>'.$namePage.'</option></br>';
	}

	// mysqli_close($conn);


} else {
	header("location:login.php");
	// mysqli_close($conn);

} 


 ?>
<!DOCTYPE html>
<html lang="en">
<head>	
	<meta charset="UTF-8">
	<link rel="icon" href="icon.gif">
	<title>Add Page and Post</title>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>

	<script src="src/jquery-customselect.js"></script>
	<link href="src/jquery-customselect.css" rel="stylesheet" />
	<style type="text/css" media="screen">
		#navbar {
			background-color: #3498db;
		}
		.text a {
			color:white;
			font-style: bold;
		}
		.nav-right {
			margin-right: 10px;
			margin-top: 10px;
			float: right;
			font-size: 20px;
			list-style: none;	
		}
		.bg-green {
			background-color: #2ecc71;
		}
		ul{
			list-style: none;
		}
		.input-group {
			padding-bottom: 10px;
		}
		.hiddenID{
			display: none;
		}
		#postTable{
			display: none;
		}
		.enter-post-id {
			display: none;
		}

		#loading{
			position: fixed;
			left: 0;
			top:0;
			width: 100%;
			height: 100%;
			z-index: 9999;
			background-image: url("squares.gif");
			background-repeat: no-repeat;
			background-position: center;
			display: none;
		}
		#error {
			font-size: 15px;
			font-weight: bold;
		}
		li #selPages {
			width: 100%;
		}
		li>a: hover {
			background-color: blue;
		}

	</style>
</head>
<body>
<nav class="navbar navbar navbar-static-top" id="navbar">
<div class="container-fluid">
	<div class="navbar-header">
		<div class="navbar-brand" style="color: white">Phu Fap Fap</div>
	</div>
	<ul class="nav navbar-nav text">
		<li><a href="index.php"><span class="glyphicon glyphicon-home"></span><span> Add Post</span></a></li>
		<li><a href="progress.php"><span class="glyphicon glyphicon-th-list"></span><span> Progress</span></a></li>
		<li><a href=""><span class="glyphicon glyphicon-cog"></span><span> Config</span></a></li>
		
	</ul>
	 <ul class="nav navbar-nav navbar-right">
	 <li>
	 	<img src="<?php echo $url ?>" alt="" class="img img-circle">
	 	<span style="color:white"><?php echo $name ;?></span>
	 </li>
      <li><a href="logout.php" style="color:white"><span class="glyphicon glyphicon-user"></span> Log Out</a></li>
      </ul>
</div>
</nav>
<div id="loading"></div>
<div class="row">
	<div class="col-xs-8">
		<div class="col-xs-8 col-xs-offset-1">
			<ul id="body">
			<li>
				<label for="pages">Select Page</label>
				<select name="pages" id="selPages" class="custom-select">
					<option value='1'>Select 1 Page</option>
					<?php echo $pages ?>
				</select>
			</li>
			<br> <li>
			<div id="postChoose">
				<label for="post">Select Post</label>
				<input type="text" id="selPost" name="post" class="form-control" value="" placeholder="Select 1 Post From Table" disabled required>
				<div style="height:200px; overflow-y: scroll;" id="postTable">
					<table class="table table-hover" id="table" >
						
					</table>
				</div>
			</div>
			<br>
			<div id="setNamePost">
				<label for="namePost">Set Name Post</label>
				<input type="text" id="namePost" name="namePost" class="form-control" value="" placeholder="Example: Post Sell Clothes" disabled maxlength="255">
			</div>
					
				
				<div id="error" class="bg-danger">	
				</div>
				<br>
				<button type="button" class="btn btn-success"  id="save">Run</button>
			</li>
		</ul>
	</div><!-- main left conner -->
	</div> <!-- end left coner-->
	<div class="col-xs-4">
		<div class="panel panel-primary">
			<div class="panel panel-heading panel-title text-center">Information</div>
			<div class="panel-body">
			<div id="page-infomation">
				<img src="" alt="" id="img-pages">
				<span class="text-center" style="margin-left: 30px" id="pages-name"></span>
			</div>
			<hr>
			<div class="row" id="messagePost">
			<div class="col-xs-4" >
				<img src="" alt="" id="imagePostHere">
				<input type="hidden" id="link">
			</div>
			<div class="col-xs-8" id="messagePostHere">
				
			</div>

			</div>
			</div>
		</div>		
	</div><!-- end right coner -->
	</div>
</body>
</html>
<script>

	$(document).ready(function() {


		// Select Page
		$("#selPages").customselect().change(function(event) {
			var selPages = $("#selPages").val();
			var accessToken = $('#selPages option:selected').attr("id");
			//alert(accessToken);
			var img = $('#selPages option:selected').val();
			var name = $('#selPages option:selected').text();
			if(selPages != 1){
				$("#selPost").removeAttr('disabled');
				$('#img-pages').attr('src', img);
				$('#pages-name').text(name);
				
			} else {
				$("#selPosts").attr('disabled');
			};

		});

		// Select Post form Input 
		$("#selPost").click(function(e) {
			/* Act on the event */
			e.preventDefault();
			$.post('post.php', {accessToken: $('#selPages option:selected').attr("id")}, function(data) {
			/*optional stuff to do after success */
			$("#table").html(data);
			$("#postTable").show();
			$("#table").slideDown('fast');
			return false;	
			});
		});

		// Select on table

		$("#table").on('click', '.showPost', function(event) {
			event.preventDefault();
			/* Act on the event */
			var id = $(this).children('.hiddenID').text();
			$("#postTable").slideUp('fast');
			$("#selPost").val(id);
			var urlImage = $(this).children('.image').children('img').attr('src');
			$("#imagePostHere").attr('src',urlImage);
			var message = $(this).children('.message').text();
			$("#messagePostHere").text(message);
			$("#namePost").removeAttr('disabled');
			$("#link").val("https://www.facebook.com/"+id);
		});

		// Click Link Post id

		// $(".enter-id").click(function(event) {
		// 	/* Act on the event */
		// 	event.preventDefault();
		// 	$(".enter-post-id").slideDown('fast');
		// });


		// Enter Post Id

		// $("#post-id").keypress(function(e){
		// 	var key = e.which;
		// 	if(key == 13){
		// 		$.post('getPage.php', {accessToken: $('#selPages option:selected').attr("id"),postId : $("#post-id").val()}, function(data, textStatus, xhr) {
		// 			optional stuff to do after success 
		// 			var info = data.split("$$$");
		// 			$("#messagePostHere").text(info[0]);
		// 			$("#imagePostHere").attr('src', info[1]);
		// 			$("#selPost").val(info[2]);
		// 			$(".enter-post-id").slideUp('fast');
		// 		});
		// 	}
		// });

		
		// button save
		$("#save").click(function(event) {
			/* Act on the event */
			event.preventDefault();
			// $("#save").hide();
			if($("#namePost").val() == '') {
				alert('Enter the name post');
			} else {
				$("#loading").fadeIn('slow');
			
				var accessToken = $('#selPages option:selected').attr("id");
				var namePage = $('#selPages option:selected').text();
				var postId = $("#selPost").val();
				var namePost = $("#namePost").val();
				var pageId = $('#selPages option:selected').attr("label");
				$.post("savedata.php", {
					postId: postId,
					accessToken : accessToken,
					namePage : namePage,
					namePost : namePost,
					pageId : pageId,
					link : $("#link").val(),
					picture : $("#imagePostHere").attr('src')
				}, function(data, textStatus, xhr) {
									
					console.log(data);
					$('#formSubmit').trigger('reset');
					$("#loading").fadeOut('slow');
					window.location.href="progress.php";
					
				});
			}
			
		});

	});
</script>