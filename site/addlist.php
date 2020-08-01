<?php
	//DB connection data
	$server="";
	$username="";
	$password="";
	$database="";

	if(!$_FILES["list"]["error"]==UPLOAD_ERR_OK)
	{
		echo "There was an error with the file to upload, please retry<br>";
		exit();
	}

	$xmlfile=fopen($_FILES["list"]["tmp_name"],"r");
	$xmldata=fread($xmlfile,filesize($_FILES["list"]["tmp_name"]));
	fclose($xmlfile);
	$list=simplexml_load_string($xmldata);

	$conn=new mysqli($server,$username,$password,$database);
	if($conn->connect_errno)
	{
		echo "Failed to connecto to database<br>";
		exit();
	}

	//Add user to database if it isnt registered yet
	$query="insert into mal_users (name) values ('".$list->myinfo->user_name."');";
	$conn->query($query);
	
	$query="select * from mal_users where name='".$list->myinfo->user_name."';";
	echo $query."<br>";
	$mal_user=$conn->query($query);
	$mal_user=$mal_user->fetch_assoc();
	
	//Add anime
	foreach($list->anime as $anime1)
	{
		//Add type
		$query="insert into anime_types (type) values('".$anime1->series_type."');";
		echo $query."<br>";
		$conn->query($query);
	
		//Add anime in the database, if it is already present the query will just fail
		$query="select * from anime_types where type='".$anime1->series_type."';";
		$type=$conn->query($query);
		$type=$type->fetch_assoc();
	
		$query="insert into anime (id,title,type_id,number_episodes) values (".$anime1->series_animedb_id.",'".$anime1->series_title."',".$type["id"].",".$anime1->series_episodes.");";
		echo $query."<br>";
		$conn->query($query);
	
		//Add status
		$query="insert into status (status) values ('".$anime1->my_status."');";
		$conn->query($query);
	
		//Check if it's already present then add the entry in the list
		$query="select * from status where status='".$anime1->my_status."';";
		$status=$conn->query($query);
		$status=$status->fetch_assoc();
	
		$query="insert into lists (user_id,anime_id,score,status_id) values (".$mal_user["id"].",".$anime1->series_animedb_id.",".$anime1->my_score.",".$status["id"].") on duplicate key update score=".$anime1->my_score.",status_id=".$status["id"].";";
		echo $query."<br>";
		$conn->query($query);
	}
?>
