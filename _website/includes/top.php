<?php
// Top include for all _website pages: logo + site name
?>
<link rel="stylesheet" href="css/header.css">
<?php
// Optionally set a background image from config
if (isset($SITE_BACKGROUND) && $SITE_BACKGROUND) {
  $bg = htmlspecialchars($SITE_BACKGROUND, ENT_QUOTES, 'UTF-8');
  echo "<style>body{background-image:url('". $bg ."');background-size:cover;background-position:center fixed;}</style>\n";
}
?>

<div class="gsw-top">
  <div class="gsw-top-left">
    <img src="images/logo-sm.png" alt="Gameservers World logo">
  </div>
  <div class="gsw-site-name">Gameservers World</div>
</div>
