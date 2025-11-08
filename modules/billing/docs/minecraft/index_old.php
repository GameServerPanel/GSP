<?php
/**
 * Minecraft Server Documentation
 */
?>
<h1>Minecraft Server Guide</h1>

<h2>Overview</h2>
<p>Minecraft is one of the most popular sandbox games in the world. This guide will help you set up and manage your Minecraft Java Edition server.</p>

<h2>Getting Started</h2>
<p>Once your Minecraft server is provisioned, you can connect to it using the server IP and port provided in your account dashboard.</p>

<h3>Server Details</h3>
<ul>
    <li><strong>Default Port:</strong> 25565</li>
    <li><strong>Protocol:</strong> TCP/UDP</li>
    <li><strong>Supported Versions:</strong> 1.8 - Latest</li>
</ul>

<h2>Configuration</h2>
<p>You can customize your server using the <code>server.properties</code> file. Common settings include:</p>

<h3>Server Properties</h3>
<pre><code># Server name
motd=Welcome to My Minecraft Server

# Game mode (survival, creative, adventure, spectator)
gamemode=survival

# Difficulty (peaceful, easy, normal, hard)
difficulty=normal

# Maximum players
max-players=20

# Enable PvP
pvp=true

# View distance (in chunks)
view-distance=10
</code></pre>

<h2>Installing Plugins</h2>
<p>To add plugins to your server, you'll need to use a modified server like Spigot or Paper:</p>
<ol>
    <li>Download plugins from <a href="https://www.spigotmc.org/resources/" target="_blank">SpigotMC</a> or <a href="https://hangar.papermc.io/" target="_blank">Hangar</a></li>
    <li>Upload the <code>.jar</code> files to your server's <code>plugins</code> folder via FTP</li>
    <li>Restart your server</li>
    <li>Configure plugins in their respective config files in <code>plugins/[PluginName]/</code></li>
</ol>

<h2>Common Issues</h2>

<h3>Players Can't Connect</h3>
<ul>
    <li>Verify the server is running in your control panel</li>
    <li>Check that you're using the correct IP address and port</li>
    <li>Ensure your firewall allows Minecraft traffic on port 25565</li>
</ul>

<h3>Server Lag</h3>
<ul>
    <li>Reduce view distance in <code>server.properties</code></li>
    <li>Limit entity spawning with plugins like ClearLagg</li>
    <li>Upgrade to a server with more RAM if needed</li>
    <li>Use performance-optimized server software like Paper</li>
</ul>

<h3>World Corruption</h3>
<ul>
    <li>Always make regular backups of your world folder</li>
    <li>Stop the server properly before making changes</li>
    <li>Use world management plugins to prevent corruption</li>
</ul>

<h2>Recommended Plugins</h2>
<ul>
    <li><strong>EssentialsX</strong> - Core commands and features</li>
    <li><strong>WorldEdit</strong> - In-game world editing</li>
    <li><strong>LuckPerms</strong> - Advanced permission management</li>
    <li><strong>Vault</strong> - Economy and permissions API</li>
    <li><strong>WorldGuard</strong> - Region protection</li>
</ul>

<h2>Further Resources</h2>
<ul>
    <li><a href="https://minecraft.fandom.com/wiki/Server.properties" target="_blank">Minecraft Wiki - Server Properties</a></li>
    <li><a href="https://www.spigotmc.org/" target="_blank">SpigotMC Community</a></li>
    <li><a href="https://papermc.io/" target="_blank">PaperMC Official Site</a></li>
</ul>
