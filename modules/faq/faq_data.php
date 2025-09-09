<?php
/**
 * FAQ Data Structure - HTML Format
 * Replaces RSS-based FAQ with structured PHP array for better maintainability
 */

function getFaqData() {
    return array(
        'Panel Overview' => array(
            array(
                'title' => 'What is the Open Game Panel?',
                'content' => 'The Open Game Panel (OGP) is a web-based control panel for managing game servers. It provides an easy-to-use interface for starting, stopping, configuring, and monitoring your game servers without needing command-line access.'
            ),
            array(
                'title' => 'How do I navigate the panel?',
                'content' => 'The panel is organized into several main sections accessible from the navigation menu:<br>
                • <strong>Dashboard</strong> - Overview of your servers and system status<br>
                • <strong>Game Servers</strong> - Manage your game server instances<br>
                • <strong>File Manager</strong> - Browse and edit server files<br>
                • <strong>User Management</strong> - Control user access and permissions<br>
                • <strong>Administration</strong> - System settings and configuration'
            ),
            array(
                'title' => 'What browsers are supported?',
                'content' => 'The panel works best with modern browsers including Chrome, Firefox, Safari, and Edge. JavaScript must be enabled for full functionality.'
            )
        ),

        'Dashboard' => array(
            array(
                'title' => 'What information is shown on the Dashboard?',
                'content' => 'The Dashboard provides:<br>
                • <strong>Server Status</strong> - Quick view of which servers are online/offline<br>
                • <strong>System Resources</strong> - CPU, memory, and disk usage<br>
                • <strong>Recent Activity</strong> - Latest server actions and events<br>
                • <strong>Quick Actions</strong> - Fast access to common server management tasks'
            ),
            array(
                'title' => 'How do I refresh the dashboard data?',
                'content' => 'Dashboard widgets automatically refresh every few minutes. You can also manually refresh by clicking the refresh button on individual widgets or refreshing your browser page.'
            )
        ),

        'Server Management' => array(
            array(
                'title' => 'How do I start my game server?',
                'content' => 'Navigate to <strong>Game Manager</strong> → <strong>Server Monitor</strong>, then click the <strong>Start</strong> button. Wait for the status to change to "Online" before attempting to connect.'
            ),
            array(
                'title' => 'How do I stop my game server?',
                'content' => 'In the Server Monitor, click the <strong>Stop</strong> button. The server will shut down gracefully, saving any necessary data before stopping.'
            ),
            array(
                'title' => 'How do I restart my game server?',
                'content' => 'Use the <strong>Restart</strong> button in Server Monitor for a clean restart, or use <strong>Stop</strong> followed by <strong>Start</strong> if you need more control over the process.'
            ),
            array(
                'title' => 'What if my server won\'t start?',
                'content' => 'Check the server logs for error messages by clicking <strong>View Log</strong>. Common issues include:<br>
                • Misconfigured settings<br>
                • Missing or corrupted files<br>
                • Port conflicts<br>
                • Insufficient system resources<br>
                If the problem persists, check the troubleshooting section or contact support.'
            ),
            array(
                'title' => 'How do I update my game server?',
                'content' => 'Use the <strong>Install/Update</strong> button in Server Monitor. For Steam games, this will validate and download any updates automatically. Make sure to stop the server before updating.'
            )
        ),

        'File Management' => array(
            array(
                'title' => 'How do I access server files?',
                'content' => 'Use the <strong>File Manager</strong> (LiteFM) to browse, edit, upload, and download server files through your web browser. You can also use the <strong>FTP</strong> module for bulk file transfers.'
            ),
            array(
                'title' => 'How do I edit configuration files?',
                'content' => 'Navigate to <strong>Edit Config Files</strong> for game-specific configuration editing, or use the File Manager to edit any text file directly in your browser.'
            ),
            array(
                'title' => 'Can I upload files to my server?',
                'content' => 'Yes! You can upload files using:<br>
                • <strong>File Manager</strong> - For individual files through web interface<br>
                • <strong>FTP</strong> - For bulk uploads using an FTP client<br>
                • <strong>Fast Download</strong> - For maps and custom content'
            ),
            array(
                'title' => 'Why can\'t I delete some files?',
                'content' => 'Some files are protected to prevent accidental server breakage:<br>
                • Server executables<br>
                • Startup scripts<br>
                • Critical system files<br>
                If you need to modify these files, contact your administrator.'
            ),
            array(
                'title' => 'How do I backup my server files?',
                'content' => 'Use the <strong>Backup & Restore</strong> module to create automated or manual backups of your server data. You can also download individual files through the File Manager.'
            )
        ),

        'User Management' => array(
            array(
                'title' => 'What are subusers and how do I add them?',
                'content' => 'Subusers are additional users who can access specific servers with limited permissions. To add subusers:<br>
                1. Go to <strong>Subusers</strong><br>
                2. Click <strong>Add Subuser</strong><br>
                3. Set their username, password, and permissions<br>
                4. Assign them to specific servers<br>
                Subusers can only access servers you explicitly assign to them.'
            ),
            array(
                'title' => 'What are user groups?',
                'content' => 'User groups allow you to manage permissions for multiple users at once. Instead of setting permissions for each user individually, you can create groups with specific access levels and add users to those groups.'
            ),
            array(
                'title' => 'How do I change my password?',
                'content' => 'Go to <strong>Settings</strong> → <strong>User Settings</strong> to change your password, email, and other account preferences.'
            ),
            array(
                'title' => 'What permissions can I assign to subusers?',
                'content' => 'You can control subuser access to:<br>
                • Server start/stop/restart<br>
                • File management<br>
                • Configuration editing<br>
                • Log viewing<br>
                • RCON console access<br>
                • Backup creation'
            )
        ),

        'Administration' => array(
            array(
                'title' => 'How do I access admin functions?',
                'content' => 'Admin functions are available in the <strong>Administration</strong> menu for users with admin privileges. This includes server management, user administration, and system settings.'
            ),
            array(
                'title' => 'How do I install new game modules?',
                'content' => 'Use the <strong>Module Manager</strong> to install, update, or remove panel modules. Only administrators can manage modules.'
            ),
            array(
                'title' => 'How do I configure system settings?',
                'content' => 'Access <strong>Settings</strong> from the administration menu to configure global panel settings, security options, and system preferences.'
            ),
            array(
                'title' => 'How do I add new game servers?',
                'content' => 'Administrators can add new servers through <strong>Administration</strong> → <strong>Add Server</strong>. This includes configuring the game type, ports, and initial settings.'
            )
        ),

        'Monitoring & Status' => array(
            array(
                'title' => 'How do I check server status?',
                'content' => 'The <strong>Status</strong> module provides real-time information about server status, player counts, and system resources. You can also view individual server status in the Server Monitor.'
            ),
            array(
                'title' => 'What is LGSL and how does it work?',
                'content' => 'LGSL (Live Game Server List) queries game servers to display real-time status including player count, map, and server information. It supports most major game server types.'
            ),
            array(
                'title' => 'How do I view server logs?',
                'content' => 'Access server logs through <strong>Game Manager</strong> → <strong>View Log</strong> to see server output, error messages, and player activity. Logs help diagnose server issues.'
            ),
            array(
                'title' => 'What monitoring tools are available?',
                'content' => 'The panel includes:<br>
                • Real-time server status monitoring<br>
                • Resource usage tracking<br>
                • Player activity logs<br>
                • System performance metrics<br>
                • Alert notifications for critical issues'
            )
        ),

        'Communication & Support' => array(
            array(
                'title' => 'How do I get support?',
                'content' => 'Support is available through:<br>
                • <strong>Tickets</strong> - Create support tickets for technical issues<br>
                • <strong>Support</strong> module - Access knowledge base and documentation<br>
                • Community forums and Discord channels<br>
                • This FAQ section for common questions'
            ),
            array(
                'title' => 'How do I create a support ticket?',
                'content' => 'Go to <strong>Tickets</strong> → <strong>New Ticket</strong>, describe your issue in detail, and include relevant server information. Support staff will respond as soon as possible.'
            ),
            array(
                'title' => 'What is TeamSpeak integration?',
                'content' => 'The <strong>TeamSpeak3</strong> module allows you to manage TeamSpeak servers alongside your game servers, including user permissions, channel management, and server settings.'
            )
        ),

        'Game-Specific Features' => array(
            array(
                'title' => 'How do I use Steam Workshop integration?',
                'content' => 'The <strong>Steam Workshop</strong> module allows you to easily install and manage Steam Workshop items for supported games. Simply browse or search for content and click install.'
            ),
            array(
                'title' => 'What is the Mods system?',
                'content' => 'The <strong>Mods</strong> module helps you install and manage game modifications. Different games support different mod systems, and the panel provides tools for the most common mod frameworks.'
            ),
            array(
                'title' => 'How do I use RCON?',
                'content' => 'The <strong>RCON</strong> module provides remote console access to your game servers. You can execute server commands, kick/ban players, change maps, and perform administrative tasks without joining the game.'
            ),
            array(
                'title' => 'What is TShock integration?',
                'content' => 'The <strong>TShock</strong> module provides enhanced Terraria server management with features like player permissions, world protection, and advanced administrative commands.'
            )
        ),

        'Troubleshooting' => array(
            array(
                'title' => 'My server won\'t start - "Failed to start remote server" error',
                'content' => 'This error usually means:<br>
                • The server crashed during startup<br>
                • Configuration files have syntax errors<br>
                • Required files are missing or corrupted<br>
                • Port conflicts with other services<br>
                <strong>Solution:</strong> Check the server logs for specific error messages, verify your configuration files, and ensure all required game files are present.'
            ),
            array(
                'title' => 'Players can\'t connect to my server',
                'content' => 'Common connection issues:<br>
                • Server not actually running (check status)<br>
                • Firewall blocking the port<br>
                • Incorrect IP address or port<br>
                • Game version mismatch<br>
                <strong>Solution:</strong> Verify the server is online, check firewall settings, and confirm players are using the correct connection information.'
            ),
            array(
                'title' => 'File upload fails or times out',
                'content' => 'Large file upload issues can be caused by:<br>
                • File size limits in PHP configuration<br>
                • Network timeouts<br>
                • Browser limitations<br>
                <strong>Solution:</strong> Use FTP for large files, check file size limits, or break large uploads into smaller chunks.'
            ),
            array(
                'title' => 'I forgot my password',
                'content' => 'Use the <strong>Lost Password</strong> link on the login page to reset your password. You\'ll need access to the email address associated with your account.'
            ),
            array(
                'title' => 'The panel is slow or unresponsive',
                'content' => 'Performance issues may be due to:<br>
                • High server load<br>
                • Network connectivity problems<br>
                • Browser cache issues<br>
                <strong>Solution:</strong> Try refreshing the page, clearing browser cache, or waiting for server load to decrease. Contact support if problems persist.'
            )
        ),

        'Game-Specific Information' => array(
            array(
                'title' => 'Ark - Query and RCON Ports',
                'content' => 'For ARK: Survival Evolved servers:<br>
                • <strong>Query Port:</strong> Server Port + 2<br>
                • <strong>RCON Port:</strong> Usually Server Port + 1<br>
                Example: If server port is 7777, query port is 7779'
            ),
            array(
                'title' => 'Arma - Query Port',
                'content' => 'For ARMA servers:<br>
                • <strong>Query Port:</strong> Server Port + 1<br>
                Example: If server port is 2302, query port is 2303'
            ),
            array(
                'title' => 'Conan Exiles - Ports Configuration',
                'content' => 'For Conan Exiles servers:<br>
                • <strong>Query Port:</strong> Server Port + 2<br>
                • <strong>RCON Port:</strong> Server Port + 3<br>
                Example: If server port is 7777, query port is 7779, RCON port is 7780'
            ),
            array(
                'title' => 'DayZ - Query Ports',
                'content' => 'DayZ port configuration depends on the version:<br>
                • <strong>DayZ Mod:</strong> Query Port = Server Port + 1<br>
                • <strong>DayZ Standalone:</strong> Query Port = Server Port + 3<br>
                Check which version you\'re running to use the correct ports.'
            ),
            array(
                'title' => 'Killing Floor - Query Port',
                'content' => 'For Killing Floor servers:<br>
                • <strong>Query Port:</strong> Server Port + 1<br>
                Example: If server port is 7707, query port is 7708'
            )
        ),

        'Database & Backups' => array(
            array(
                'title' => 'How do I backup my game server database?',
                'content' => 'To backup your game server database:<br>
                1. Go to <strong>Game Manager</strong> → <strong>MySQL</strong><br>
                2. Click on <strong>phpMyAdmin</strong> to access the database<br>
                3. Select your database and click <strong>Export</strong><br>
                4. Save the file to your local computer<br>
                <strong>Tip:</strong> Regular backups prevent data loss from server issues.'
            ),
            array(
                'title' => 'How do I restore a database backup?',
                'content' => 'To restore a database backup:<br>
                1. Access phpMyAdmin through the MySQL module<br>
                2. Select your database<br>
                3. <strong>Important:</strong> Delete all existing tables first<br>
                4. Click <strong>Import</strong> and select your backup file<br>
                5. Click <strong>Go</strong> to restore the data'
            ),
            array(
                'title' => 'How do I validate Steam files?',
                'content' => 'To validate/verify Steam game files:<br>
                1. Stop your server<br>
                2. Go to <strong>Server Monitor</strong><br>
                3. Click <strong>Install/Update</strong><br>
                This will check and download any missing or corrupted files.<br>
                <strong>For complete reinstall:</strong> Delete all files in File Manager, then run Install/Update.'
            )
        ),

        'FTP & File Transfer' => array(
            array(
                'title' => 'I get TLS/SSL errors when connecting to FTP',
                'content' => 'If your FTP client shows TLS or secure connection errors:<br>
                • Try disabling TLS/SSL encryption in your FTP client<br>
                • Use "Plain FTP" or "No encryption" settings<br>
                • Our FTP servers typically don\'t require TLS encryption<br>
                <strong>FTP Clients:</strong> FileZilla, WinSCP, or the built-in File Manager work well.'
            ),
            array(
                'title' => 'What are the FTP connection details?',
                'content' => 'FTP connection information is available in the <strong>FTP</strong> module:<br>
                • <strong>Server:</strong> Your panel\'s IP address<br>
                • <strong>Username:</strong> Your panel username<br>
                • <strong>Password:</strong> Your panel password<br>
                • <strong>Port:</strong> Usually 21 (standard FTP)<br>
                • <strong>Encryption:</strong> None/Plain FTP'
            )
        )
    );
}
?>