<html>
	<head>
		<title>List viewer</title>
		<!--<link rel="stylesheet" href="view_list.css">-->
	</head>
	<body>
		<form action="view_list.php" method="GET">
			<div class="input">
			<span>User to search:</span> <br><input name="user" type="text" id="user_input" placeholder='search user...' required>
			<input type="submit" id="user_btn" value='Search'><br>
	</div>
			<?php
				//DB connection info
				$server="localhost";
				$username="mal";
				$password="pass";
				$database="myanimelist";

				$conn=new mysqli($server,$username,$password,$database);
				if($conn->connect_errno)
				{
					echo "Failed to connect to the database<br>";
					exit();
				}

				//Log connecting
				$query="insert into accesses(ip,last_access) values('".$_SERVER["REMOTE_ADDR"]."','".date("Y-m-d H:i:s")."') on duplicate key update last_access='".date("Y-m-d H:i:s")."';";
				$conn->query($query);
				echo $conn->error;

				if(!$_GET["user"])
				{
					echo "Type in the username to check the list<br>";
					exit();
				}

				$quser=$conn->real_escape_string($_GET["user"]);
				$query="create temporary table animelist select anime.title,lists.score,anime_types.type,anime.number_episodes,status.status from lists join (anime) on (anime.id=lists.anime_id) join (mal_users) on (mal_users.id=lists.user_id) join (status) on (status.id=lists.status_id) join (anime_types) on (anime_types.id=anime.type_id) where mal_users.name='".$quser."' order by status.status,anime.title;";
				$conn->query($query);
				$query="alter table animelist add entry int not null auto_increment unique first;";
				$conn->query($query);
				$query="select * from animelist;";
				$list=$conn->query($query);
				if($list->num_rows==0)
				{
					echo "No user found with the name ".$_GET["user"]."<br>";
					exit();
				}

				echo "<table id='anime_table'>";
				echo "<tr id='table_header'><td>Entry</td><td>Title</td><td>Score</td><td>Type</td><td>Episodes</td><td>Status</td></tr>";
				for($x=0;$x<$list->num_rows;$x++)
				{
					$entry=$list->fetch_assoc();
					echo "<tr class='anime_col'>";
					echo "<td >".$entry["entry"]."</td>";
					echo "<td >".$entry["title"]."</td>";
					echo "<td >".$entry["score"]."</td>";
					echo "<td >".$entry["type"]."</td>";
					echo "<td >".$entry["number_episodes"]."</td>";
					echo "<td >".$entry["status"]."</td>";
				}
				echo "</table>";
			?>
		</form>
	</body>
</html>
