<?php
/**
 * Terraria Server Documentation
 */
?>
<h1>Terraria Server Guide</h1>

<h2>Overview</h2>
<p><strong>Terraria</strong> is available for hosting on our platform. This guide covers the basics of setting up and managing your Terraria server.</p>

<div style="background: #1e3a5f; padding: 20px; border-left: 4px solid #3b82f6; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;">Quick Info</h3>
    <ul style="color: #e5e7eb; line-height: 1.8;">
        <li><strong style="color: #ffffff;">Game Key:</strong> terraria_win64</li>
        <li><strong style="color: #ffffff;">Startup Command:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">%IP% %PORT% %PLAYERS% %CONTROL_PASSWORD% -world%HOME_PATH%\Save\Worlds\%HOSTNAME%.wld %AUTOCREATE% -worldname %HOSTNAME% %SEED% -secure -worldpath%HOME_PATH%\Save\Worlds\ -banlist%HOME_PATH%\banlist.txt -savedirectory%HOME_PATH%\Save</code></li>
        <li><strong style="color: #ffffff;">Log File:</strong> <code style="background: #0f172a; padding: 2px 6px; border-radius: 3px; color: #a5b4fc;">ServerLog.txt</code></li>
        <li><strong style="color: #ffffff;">Default Port:</strong> ServerPort":\s\d*</li>
        <li><strong style="color: #ffffff;">Max Players:</strong> 8</li>
    </ul>
</div>

<h2>Getting Started</h2>
<p>To create a Terraria server:</p>
<ol>
    <li>Navigate to the <a href="/serverlist.php">Game Servers</a> page</li>
    <li>Find <strong>Terraria</strong> in the list</li>
    <li>Select your preferred configuration (slots, duration, etc.)</li>
    <li>Add to cart and complete checkout</li>
    <li>Your server will be automatically provisioned within minutes</li>
</ol>

<h2>Server Configuration</h2>
<p>After your server is created, you can configure it through the control panel:</p>
<ul>
    <li>Server settings and parameters</li>
    <li>Player slots and limits</li>
    <li>RCON/remote control access</li>
    <li>FTP file access</li>
</ul>

<h2>Common Tasks</h2>

<h3>Starting Your Server</h3>
<p>Servers are automatically started after creation. You can stop/start your server from the control panel.</p>

<h3>Connecting to Your Server</h3>
<p>Use your server's IP address and port to connect from the game client.</p>

<h3>Managing Files</h3>
<p>Access your server files via FTP using the credentials provided in your control panel.</p>

<h2>Support</h2>
<p>If you need assistance with your Terraria server:</p>
<ul>
    <li>Check our <a href="/docs.php?action=view&doc=common-issues">Common Issues</a> guide</li>
    <li>Contact support through your account dashboard</li>
    <li>Visit the official Terraria community for game-specific help</li>
</ul>

<div style="background: #78350f; padding: 20px; border-left: 4px solid #f59e0b; margin: 20px 0; border-radius: 4px;">
    <h3 style="color: #ffffff; margin-top: 0;"><i class="fas fa-exclamation-triangle" style="color: #fbbf24; margin-right: 8px;"></i>Important Notes</h3>
    <ul style="color: #fef3c7; line-height: 1.8;">
        <li>Always keep your server updated to the latest version</li>
        <li>Make regular backups of your server configuration</li>
        <li>Review and follow the game's End User License Agreement (EULA)</li>
    </ul>
</div>