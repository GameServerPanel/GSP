<?php
/**
 * Common Issues & Troubleshooting Documentation
 */
?>
<h1>Common Issues & Solutions</h1>

<h2>Server Won't Start</h2>

<h3>Symptoms</h3>
<ul>
    <li>Server status shows "Stopped" even after clicking Start</li>
    <li>Server starts but immediately crashes</li>
    <li>Error messages in the console</li>
</ul>

<h3>Solutions</h3>
<ol>
    <li><strong>Check Server Logs:</strong> Review the console output for error messages</li>
    <li><strong>Verify Configuration:</strong> Ensure config files have correct syntax</li>
    <li><strong>Check Port Conflicts:</strong> Make sure the port isn't already in use</li>
    <li><strong>Memory Issues:</strong> Verify you have enough RAM allocated</li>
    <li><strong>File Permissions:</strong> Ensure server files have correct permissions</li>
</ol>

<h2>Can't Connect to Server</h2>

<h3>Symptoms</h3>
<ul>
    <li>Connection timeout when trying to join</li>
    <li>"Server not responding" errors</li>
    <li>Can't find server in server list</li>
</ul>

<h3>Solutions</h3>
<ol>
    <li><strong>Verify Server is Running:</strong> Check control panel status</li>
    <li><strong>Check IP and Port:</strong> Ensure you're using the correct address</li>
    <li><strong>Firewall Settings:</strong> Make sure firewall allows the server port</li>
    <li><strong>Server Whitelist:</strong> Check if server has whitelist enabled</li>
    <li><strong>Game Version:</strong> Ensure your game version matches the server</li>
</ol>

<h2>Server Lag</h2>

<h3>Symptoms</h3>
<ul>
    <li>Delayed responses to player actions</li>
    <li>Rubber-banding or teleporting players</li>
    <li>High ping times</li>
</ul>

<h3>Solutions</h3>
<ol>
    <li><strong>Check Server Resources:</strong> Monitor CPU and RAM usage in control panel</li>
    <li><strong>Reduce View Distance:</strong> Lower render distance in server config</li>
    <li><strong>Limit Entities:</strong> Use plugins to limit mob spawning</li>
    <li><strong>Optimize Plugins/Mods:</strong> Remove or update poorly performing addons</li>
    <li><strong>Upgrade Plan:</strong> Consider upgrading to a higher-tier server</li>
</ol>

<h2>File Upload Issues</h2>

<h3>Symptoms</h3>
<ul>
    <li>FTP connection refused</li>
    <li>Can't upload files</li>
    <li>Files upload but don't appear on server</li>
</ul>

<h3>Solutions</h3>
<ol>
    <li><strong>Check FTP Credentials:</strong> Verify username and password are correct</li>
    <li><strong>FTP Mode:</strong> Try switching between active and passive FTP mode</li>
    <li><strong>File Size Limits:</strong> Check if file exceeds maximum upload size</li>
    <li><strong>Directory Permissions:</strong> Ensure you have write permissions</li>
    <li><strong>Stop Server First:</strong> Some files can't be modified while server runs</li>
</ol>

<h2>Mods/Plugins Not Working</h2>

<h3>Symptoms</h3>
<ul>
    <li>Mods don't load</li>
    <li>Plugin commands don't work</li>
    <li>Server crashes when loading mods</li>
</ul>

<h3>Solutions</h3>
<ol>
    <li><strong>Check Compatibility:</strong> Ensure mod/plugin matches server version</li>
    <li><strong>Verify Dependencies:</strong> Install required dependency mods</li>
    <li><strong>Check Installation Path:</strong> Files must be in correct folder</li>
    <li><strong>Review Logs:</strong> Check for mod/plugin loading errors</li>
    <li><strong>Update Software:</strong> Make sure mods and server are up to date</li>
</ol>

<h2>World Data Loss</h2>

<h3>Prevention</h3>
<ul>
    <li>Make regular backups via FTP or control panel</li>
    <li>Always stop server properly before shutting down</li>
    <li>Don't force-stop unless absolutely necessary</li>
    <li>Test configuration changes on backup worlds first</li>
</ul>

<h3>Recovery</h3>
<ol>
    <li>Check if control panel has automatic backups</li>
    <li>Restore from your most recent manual backup</li>
    <li>Contact support if no backups are available</li>
</ol>

<h2>Getting Further Help</h2>
<p>If these solutions don't resolve your issue:</p>
<ul>
    <li>Check the specific documentation for your game server type</li>
    <li>Review server logs for detailed error messages</li>
    <li>Contact support with:
        <ul>
            <li>Detailed description of the problem</li>
            <li>Steps to reproduce the issue</li>
            <li>Recent changes made to the server</li>
            <li>Relevant error messages or screenshots</li>
        </ul>
    </li>
</ul>
