<?php
return [
  // SQLite DB (auto-created)
  'db_path' => __DIR__ . '/../status.sqlite',

  // Shared secret the machines use when POSTing
  'ingest_token' => '6e93b1e50f5e32c33060c0488eb2d90c8acf132307f3d83abb162e9986808a27',

  // Set if you want Discord alerts (or leave empty to disable)
  'discord_webhook' => 'https://discord.com/api/webhooks/XXXXX/XXXXX',

  // Host considered UP if last sample newer than this many seconds
  'up_grace_seconds' => 10*60, // 10 minutes

  // Alert rules (machine DOWN when no sample for 3 intervals @5min)
  'down_consecutive_threshold' => 3,  // 3 missed = DOWN alert
  'network_issue_failures'     => 2,  // 2 fails in last window => NETWORK ISSUE
  'network_issue_window'       => 20, // last 20 intervals (~100 minutes)

  // CORS for summary API (public site pulls from panel). Adjust to your domain or leave '' to disable CORS.
  'cors_allow_origin' => '',
];

