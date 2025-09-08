<?php
/*
 * Component of the circular module
 */
include 'modules/circular/functions.php';
function exec_ogp_module()
{
	$circulars = get_circulars();
	if(isset($_GET['list']))
	{
		echo '<h2>'.get_lang('your_circulars').'</h2>';
		rsort($circulars);
		echo "<table id='circular_list'>\n".
			 "<thead><tr><th>".get_lang('status')."</th><th>".get_lang('subject')."</th><th>".get_lang('date')."</th></tr></thead><tbody>\n";
		foreach($circulars as $key => $circular)
		{
			echo '<tr><td><i class="status_'.$circular['status'].'"></i></td><td><a href="?m=circular&p=show_circular&read_circular='.$circular['circular_id'].'">'.$circular['subject']."</a></td><td>".$circular['timestamp']."</td></tr>\n";
		}
		echo "</tbody></table>\n";
	}
	elseif(isset($_GET['read_circular']) and is_numeric($_GET['read_circular']))
	{
		foreach($circulars as $circular)
			if($circular['circular_id'] == $_GET['read_circular'])
				break;
		echo '<div id="circular_message">'.$circular['message']."</div>\n".
			 '<p><a href="?m=circular&p=show_circular&list">&lt;&lt; '.get_lang('back')."</a></p>\n";
		if($circular['status'] == "0")
			set_circular_readed($circular['circular_id']);
	}
	else
	{
		foreach($circulars as $key => $circular)
			if($circular['status'] == "1")
				unset($circulars[$key]);
		sort($circulars);
		header('Content-Type: application/json');
		echo json_encode($circulars);
	}
}
?>