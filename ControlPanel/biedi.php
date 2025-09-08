<?php
    if(isset($_POST['submit_tags']))
	{
		$tagsList=$_POST['tags_list'];
		$arrayElements = explode(";", $tagsList);
		echo "Contains ".sizeof($arrayElements)." elements<br>-----------------<br><br>";
		$counter = 0;
		foreach($arrayElements as $vehicle)
		{
			$vehicleValues = explode(",", $vehicle);			
			echo"
			_vehicle_".$counter."=objNull;<br>
		  _this = createVehicle ".$arrayElements[$counter].";<br>
		  _vehicle_".$counter." = _this;<br>
		    _this setDir ".rtrim($vehicleValues[4],']').";<br>
		  _this setPos ".$vehicleValues[1].",".$vehicleValues[2]."];<br><br><br>";
		  $counter++;
		}
    }
    ?>
	<h1> Array to Biedi Converter</h1>
<form action="" method="post" enctype="multipart/form-data">
<Textarea name="tags_list" textarea rows="20" cols="80"></Textarea></br>
<input type="submit" name="submit_tags">
</form>
<?php
    if(isset($_POST['sqf']))
	{
		$tagsList=$_POST['tags_list'];
		$arrayElements = explode(";", $tagsList);
		echo "Contains ".sizeof($arrayElements)." elements<br>-----------------<br><br>";
		$counter = 0;
		foreach($arrayElements as $vehicle)
		{
			$vehicleValues = explode(",", $vehicle);			
			echo"
			_vehicle_".$counter."=objNull;<br>
		  _this = createVehicle ".$arrayElements[$counter].";<br>
		  _vehicle_".$counter." = _this;<br>
		    _this setDir ".rtrim($vehicleValues[4],']').";<br>
		  _this setPos ".$vehicleValues[1].",".$vehicleValues[2]."];<br><br><br>";
		  $counter++;
		}
    }
	
	if(isset($_POST['biedi']))
	{
		$tagsList=$_POST['tags_list'];
		$arrayElements = explode(";", $tagsList);
		$counter = 0;
		foreach($arrayElements as $vehicle)
		{
			$vehicleValues = explode(",", $vehicle);
			$step1 = str_replace('[', '',$vehicleValues[0]);
			$step2 = str_replace(']', '',$step1);
			$type1=trim($vehicleValues[0],'"');
			echo'<html><body>
			class _vehicle_'.$counter.'<br>
			{<br>
			&nbsp;&nbsp;&nbsp;&nbsp;objectType="vehicle";<br>
			&nbsp;&nbsp;&nbsp;&nbsp;class Arguments <br>
			&nbsp;&nbsp;&nbsp;&nbsp;{<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;POSITION="'.$vehicleValues[1].','.$vehicleValues[2].','.$vehicleValues[3].'";<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TYPE='.$step2.';<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;AZIMUT="'.rtrim($vehicleValues[4],"]").'";<br>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;PARENT="";<br>
			&nbsp;&nbsp;&nbsp;&nbsp;};<br>};<br><br><br></html></body>';
		  $counter++;
		}
    }
    ?>
	<h1> Array to Biedi Converter</h1>
<form action="" method="post" enctype="multipart/form-data">
<Textarea name="tags_list" textarea rows="20" cols="80"></Textarea></br>
<input type="submit" value="Convert to SQF" name="sqf">
<input type="submit" value="Convert to Biedi" name="biedi">

</form>

