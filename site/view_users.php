<?php
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

$query="select * from mal_users order by name;";
$list=$conn->query($query);
for($x=0;$x<$list->num_rows;$x++)
{
	echo $list->fetch_assoc()["name"]."<br>";
}
?>
