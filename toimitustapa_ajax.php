<?php
	include "yhteys.php";
	$sql = mysql_query("
		SELECT * FROM toimitustavat
		WHERE yritysid='".$_POST['yritysid']."' AND osasto='".$_POST['osasto']."'
	");

	$body = '<link rel="stylesheet" type="text/css" href="css/maksu.css">';
	while($row = mysql_fetch_array($sql))
	{
		$body .= '<p><span class="valiko" id="tp_'.$row['id'].'_'.$_POST['osasto'].'">'.$row['toimitustapa'].'</span></p>';
	}

	if(!empty($body))
		echo json_encode($body);
	else
		echo json_encode('empty');

?>
