<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shCore.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushAppleScript.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushAS3.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushBash.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushColdFusion.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushCpp.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushCSharp.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushCss.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushDelphi.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushDiff.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushErlang.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushGroovy.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushJavaFX.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushJava.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushJScript.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushPerl.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushPhp.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushPlain.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushPowerShell.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushPython.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushRuby.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushSass.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushScala.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushSql.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushVb.js"></script>
<script type="text/javascript" src="modules/faq/syntaxhighlighter/scripts/shBrushXml.js"></script>
<script type="text/javascript" src="modules/faq/jcfilter.min.js"></script>
<script type="text/javascript" src="modules/faq/faq.js"></script>
<link type="text/css" rel="stylesheet" href="modules/faq/syntaxhighlighter/styles/shCoreDefault.css"/>
<?php
function exec_ogp_module()
{
	echo '<h2>F.A.Q.</h2>';
	echo '<div class="maincategory"><img class="headerimage" src="modules/faq/faq.png">Categories<div style="float:right" >'.
		 '<input class=search name=search id=search type=text placeholder="Search"/></div><br></div>';

	// Load FAQ data from new HTML structure
	require_once 'modules/faq/faq_data.php';
	$entries = getFaqData();
	
	$categories = "";
	$accordion_entries = "<div id=\"accordion\">\n";
	
	foreach($entries as $category_name => $category_entries)
	{
		// Create category navigation link with clean ID
		$category_id = preg_replace('/[^a-z0-9_]/', '_', strtolower($category_name));
		$categories .= "<li class='faqblock'><a class='faqcategory' href=\"#$category_id\">$category_name</a></li>";
		
		// Create category section
		$accordion_entries .= "<div class=\"category\" id=\"$category_id\"><img class='headerimage' src='modules/faq/faqlower.png'>$category_name</div>";
		
		// Add items for this category
		foreach($category_entries as $item)
		{
			$accordion_entries .= "\t<div class=\"accordion-toggle\">".
								  htmlspecialchars($item['title']) . "</div>\n".
								  "\t<div class=\"accordion-content\">\n\t\t<div class=\"faqanswer\">" . $item['content'] . "</div>\n\t</div>\n";
		}
	}
	$categories .= "</ul>";
	$accordion_entries .= "</div>";
	
	echo $categories . $accordion_entries;
	
	echo "<div class='footer' >".
			"<div style='display:block;float:left' >".
				"<b class='imagetext'></b><br>".
			"</div>".
			"<div class='credits' style='display:block;float:right' >".
				"<b>Credits:</b><br>".
				"<div class='credittext'>Open Game Panel - Enhanced HTML FAQ System<br>".
			"</div>".
		 "</div>";
}
?>

