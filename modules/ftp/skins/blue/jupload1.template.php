<?php defined("NET2FTP") or die("Direct access to this location is not allowed."); ?>
<!-- Template /skins/blue/jupload1.template.php begin -->
<applet
	code="JUpload/startup.class"
	archive="plugins/jupload/jupload.jar"
	width="650"
	height="350"
	name="mayscript"
	alt="JUpload applet">
	<!-- Java Plug-In Options -->
	<param name="progressbar"                 value="true">
	<param name="boxmessage"                  value="Loading the applet, please wait...">
	<param name="mainSplitpaneLocation"       value="450">
	<param name="leftSplitpaneLocation"       value="150">
	<param name="showStatusPanel"             value="true">
	<param name="labelFiles"                  value="<?php echo __("Number of files:"); ?>">
	<param name="labelBytes"                  value="<?php echo __("Size of files:"); ?>">
	<param name="labelAdd "                   value="<?php echo __("Add"); ?>">
	<param name="labelRemove"                 value="<?php echo __("Remove"); ?>">
	<param name="labelUpload"                 value="<?php echo __("Upload"); ?>">
	<param name="addToolTip"                  value="<?php echo __("Add files to the upload queue"); ?>">
	<param name="removeToolTip"               value="<?php echo __("Remove files from the upload queue"); ?>">
	<param name="uploadToolTip"               value="<?php echo __("Upload the files which are in the upload queue"); ?>">
 	<param name="actionURL"                   value="<?php echo $actionURL; ?>">
	<param name="overwriteContentType"        value="true">
	<param name="useRecursivePaths"           value="true">
	<param name="useAbsolutePaths"            value="false">
	<param name="checkResponse"               value="true">
	<param name="realTimeResponse"            value="true">
	<param name="maxFreeSpaceOnServer"        value="<?php echo $maxFreeSpaceOnServer; ?>">
	<param name="maxFreeSpaceOnServerTitle"   value="maxFreeSpaceOnServerTitle">
	<param name="maxFreeSpaceOnServerWarning" value="Maximum server space exceeded. Please select less/smaller files.">
	<param name="maxTotalRequestSize"         value="<?php echo $maxTotalRequestSize; ?>">
	<param name="maxTotalRequestSizeTitle"    value="maxTotalRequestSizeTitle">
	<param name="maxTotalRequestSizeWarning"  value="Total size of the files is too big. Please select less/smaller files.">
	<param name="maxNumberFiles"              value="<?php echo $maxNumberFiles; ?>">
	<param name="maxNumberFilesTitle"         value="maxNumberFilesTitle">
	<param name="maxNumberFilesWarning"       value="Total number of files is too high. Please select fewer files.">
	<param name="maxFilesPerRequest"          value="<?php echo $maxFilesPerRequest; ?>">
 	<param name="debug"                       value="true">
<script type="text/javascript">
	document.write('<param name=browserCookie value="');
	document.write(document.cookie);
	document.writeln('">');
</script>
<div style="font-size: 90%">
<ul>
</ul>
</div><br />
<!-- Template /skins/blue/jupload1.template.php end -->
