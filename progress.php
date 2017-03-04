<?php 
set_time_limit(0);
require_once 'configdb.php';
require_once 'configfb.php';
if(isset($_SESSION['longToken'])) {
$sql = "SELECT * FROM progress ORDER BY postid ASC";
$result = mysqli_query($conn,$sql);
$data = '';

while($rows = mysqli_fetch_assoc($result)){
	$active = '<a href="progress.php?active='.$rows['postid'].'" class="btn btn-xs btn-success" ><span class="glyphicon glyphicon-play"></span><span> Active</span></a>';
	$pause = '<a onclick="return confirm(\' Ban co chac muon Pause '.$rows['namepost'].'?\');" class="btn btn-xs btn-warning" href="progress.php?pause='.$rows['postid'].'"><span class="glyphicon glyphicon-pause" data-toggle="tooltip" title="Hooray!"></span><span> Pause</span></a>';
	$delete = '<a onclick="return confirm(\' Ban co chac muon Delete '.$rows['namepost'].'?\');" class="btn btn-xs btn-danger" href="progress.php?delete='.$rows['postid'].'"><span class="glyphicon glyphicon-remove"></span><span> Delete</span></a>';
	$picture = $rows['picture'];
	if ($picture == '') $picture = 'noimage.png';
	$data .='<tr><td><a href="http://facebook.com/'.$rows['postid'].'" target="_blank" >';
	$data .= '<img src="'.$picture.'" alt="" width="60px"></a></td>';
	$data .= '<td class="postid">'.$rows['postid'].'</td>';
	$data .= '<td>'.$rows['namepage'].'</td>';
	$data .= '<td>'.$rows['namepost'].'</td>';
	$data .= '<td>'.$rows['link'].'</td>';
	$data .= '<td>'.$rows['time'].'</td>';

	if($rows['status'] == 1) $status = "Running....";
	else $status = "pause....";
	$data .= '<td>'.$status.'</td>';
	$data .= '<td>'.$active.$pause.$delete.'</td></tr>';
	// /mysqli_free_result($result);
}
mysqli_free_result($result);
} else {
	$data = "Ban Chua dang nhap";
	header("location:login.php");
}

 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
 	<meta charset="UTF-8">
 	<title>Progress Auto Commnent</title>
	<link rel="icon" href="icon.gif">
 	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
 	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
 	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.11/css/dataTables.bootstrap.min.css"/>
 	<script type="text/javascript" src="https://cdn.datatables.net/r/bs-3.3.5/jqc-1.11.3,dt-1.10.8/datatables.min.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				$('#table').DataTable();
			} );
		</script>
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
		table thead {
			background-color: #8e44ad;
			color:white;
			font-size: 12px;
			font-weight: bold;
		}
		.btn {
			padding-left: 5px;
			margin-left: 10px;
			margin-top: 20px;
		}

		.postid{
			display: none;
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
      <li><a href="logout.php" style="color:white"><span class="glyphicon glyphicon-user"></span> Log Out</a></li>
      </ul>
</div>
</nav>

 	<div id="container">
 		<div class="col-xs-12">
 			<table class="table table-striped table-bordered text-center" id="table">
 				<thead>
 					<tr>
 						<td>Image</td>
 						<td class="postid">Post ID</td>
 						<td>Page Name</td>
 						<td>Post name</td>
 						<td>Link</td>
 						<td>Created Time</td>
 						<td>Status</td>
 						<td>Action</td>
 					</tr>
 				</thead>
 				<tbody>
 					<?php echo $data ?>
 				</tbody>
 			</table>
 		</div>
 	</div>
 </body>
 </html>


 <?php
 if(isset($_GET['delete'])){
 	//echo $_POST['delete'];
 	deletePost($_GET['delete']);

 }

 if(isset($_GET['pause'])){
 	//echo $_POST['delete'];
 	pausePost($_GET['pause']);
 	
 }

  if(isset($_GET['active'])){
 	//echo $_POST['delete'];
 	activePost($_GET['active']);
 	
 }


 function deletePost($postid){
 	// Xoa trong table Post
 	$sql = "DELETE FROM progress WHERE postid='".$postid."'";
 	//echo '<br>'.$sql;
 	mysqli_query($GLOBALS['conn'],$sql);
 }

 function pausePost($postid){

 	$sql = "UPDATE progress SET status = 0 WHERE postid = '".$postid."'";
 	mysqli_query($GLOBALS['conn'],$sql);

 }


 function activePost($postid){

 	$sql = "UPDATE progress SET status = 1 WHERE postid = '".$postid."'";
 	mysqli_query($GLOBALS['conn'],$sql);
 }

  ?>