<?php
/***********************
 * Assistant Chat (Full History) — PHP + cURL
 * - Persistent thread in session
 * - Full history render with Question / Answer labels
 * - SSL verification disabled (your hosting constraint)
 * - Citations: filename + page (when available)
 ***********************/

// Debug (disable on production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* ------------------- CONFIG ------------------- */
$OPENAI_API_KEY = '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';

$ASSISTANT_ID     = 'asst_RAhtGzcy6higJeMwomZSqVjM';  // <-- set to your existing assistant
$OPENAI_BASE_URL  = 'https://api.openai.com/v1';
$OPENAI_BETA_HDR  = 'assistants=v2';                  // required for Assistants v2
$REQUEST_TIMEOUT  = 30;                               // seconds for cURL calls
$RUN_POLL_DELAY   = 500000;                           // microseconds between run polls (0.5s)
$RUN_POLL_MAX     = 40;                               // max polls (~20s total); adjust as needed
/* ---------------------------------------------- */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['thread_id'])) {
    $_SESSION['thread_id'] = null;
}

/** HTML escape helper */
function h($v) { return htmlspecialchars((string)$v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

/** Low-level OpenAI request helper */
function openai_request($method, $endpoint, $payload = null, $query = []) {
    global $OPENAI_API_KEY;
    $url = "https://api.openai.com/v1" . $endpoint;
    if (!empty($query)) $url .= '?' . http_build_query($query);

    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer {$OPENAI_API_KEY}",
        "OpenAI-Beta: assistants=v2"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Host requires SSL verification disabled
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    if (!is_null($payload)) curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $resp = curl_exec($ch);
    if ($resp === false) {
        $err = curl_error($ch);
        curl_close($ch);
        throw new RuntimeException("cURL error: {$err}");
    }
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $data = json_decode($resp, true);
    if ($code >= 400) {
        $msg = isset($data['error']['message']) ? $data['error']['message'] : 'Unknown API error';
        throw new RuntimeException("OpenAI API error ({$code}): {$msg}");
    }
    return is_array($data) ? $data : [];
}

/** Create or reuse a per-visitor thread */
function ensure_thread_id() {
    if (!empty($_SESSION['thread_id'])) return $_SESSION['thread_id'];
    $created = openai_request('POST', '/threads', ['metadata' => ['site' => $_SERVER['HTTP_HOST'] ?? 'unknown']]);
    $tid = $created['id'] ?? null;
    if (!$tid) throw new RuntimeException('Failed to create thread.');
    $_SESSION['thread_id'] = $tid;
    return $tid;
}

/** Add a user message */
function add_user_message($thread_id, $text) {
    openai_request('POST', "/threads/{$thread_id}/messages", [
        'role' => 'user',
        'content' => $text,
    ]);
}

/** Start a run */
function start_run($thread_id, $assistant_id) {
    $run = openai_request('POST', "/threads/{$thread_id}/runs", [
        'assistant_id' => $assistant_id,
    ]);
    $run_id = $run['id'] ?? null;
    if (!$run_id) throw new RuntimeException('Failed to start run.');
    return $run_id;
}

/** Wait for completion (or fail/timeout) */
function wait_for_run($thread_id, $run_id, $max_tries, $delay_us) {
    $terminal = ['completed', 'failed', 'requires_action', 'cancelled', 'expired'];
    for ($i = 0; $i < $max_tries; $i++) {
        usleep($delay_us);
        $run = openai_request('GET', "/threads/{$thread_id}/runs/{$run_id}");
        $status = $run['status'] ?? '';
        if (in_array($status, $terminal, true)) return $run;
    }
    return ['status' => 'timeout'];
}

/** Cache of file_id => filename (per request) */
$_FILE_NAME_CACHE = [];

/** Resolve file name from file_id (API returns "filename" or sometimes "display_name") */
function get_file_name_by_id($file_id) {
    global $_FILE_NAME_CACHE;
    if (isset($_FILE_NAME_CACHE[$file_id])) return $_FILE_NAME_CACHE[$file_id];
    $file = openai_request('GET', "/files/{$file_id}");
    $name = $file['filename'] ?? ($file['display_name'] ?? ($file['name'] ?? $file_id));
    $_FILE_NAME_CACHE[$file_id] = $name;
    return $name;
}

/**
 * Extract message text + citations (filename + page if available).
 * Returns an array of entries: ['role' => 'user|assistant', 'text' => '...', 'refs' => [['filename'=>'','page'=>'','file_id'=>'']]]
 */
function normalize_messages($messages) {
    $out = [];
    if (empty($messages['data']) || !is_array($messages['data'])) return $out;

    // The API returns newest first by default if not specifying; we request 'asc' in fetch.
    foreach ($messages['data'] as $m) {
        $role = $m['role'] ?? '';
        if (!in_array($role, ['user', 'assistant', 'system'], true)) continue;

        if (empty($m['content']) || !is_array($m['content'])) continue;

        $all_text = [];
        $refs = [];
        foreach ($m['content'] as $part) {
            if (($part['type'] ?? '') === 'text' && !empty($part['text']['value'])) {
                $all_text[] = $part['text']['value'];

                // Parse annotations for citations (file_citation)
                $anns = $part['text']['annotations'] ?? [];
                if (is_array($anns)) {
                    foreach ($anns as $ann) {
                        if (($ann['type'] ?? '') === 'file_citation' && !empty($ann['file_citation']['file_id'])) {
                            $fid = $ann['file_citation']['file_id'];
                            $page = null;

                            // Page can appear under different shapes depending on backend. Try common keys:
                            if (isset($ann['file_citation']['page'])) {
                                $page = $ann['file_citation']['page'];
                            } elseif (isset($ann['file_citation']['page_range']) && is_array($ann['file_citation']['page_range'])) {
                                // Example: ['start' => 5, 'end' => 6]
                                $start = $ann['file_citation']['page_range']['start'] ?? null;
                                $end   = $ann['file_citation']['page_range']['end'] ?? null;
                                if ($start && $end && $start !== $end) $page = "{$start}-{$end}";
                                elseif ($start) $page = (string)$start;
                            }
                            // Fetch filename
                            try {
                                $filename = get_file_name_by_id($fid);
                            } catch (Throwable $e) {
                                $filename = $fid;
                            }
                            $refs[] = [
                                'file_id'  => $fid,
                                'filename' => $filename,
                                'page'     => $page ?? 'n/a',
                            ];
                        }
                    }
                }
            }
        }

        if (!empty($all_text)) {
            $out[] = [
                'role' => $role,
                'text' => implode("\n", $all_text),
                'refs' => $refs,
            ];
        }
    }
    return $out;
}

/** Fetch conversation (ascending) */
function fetch_history($thread_id) {
    $messages = openai_request('GET', "/threads/{$thread_id}/messages", null, ['order' => 'asc', 'limit' => 50]);
    return normalize_messages($messages);
}

/* ------------------- HANDLE POST ------------------- */
$error = null;
$history = [];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!empty($_POST['reset_thread'])) {
            $_SESSION['thread_id'] = null;
        } elseif (isset($_POST['user_input'])) {
            $user_text = trim((string)$_POST['user_input']);
            if ($user_text !== '') {
                $thread_id = ensure_thread_id();
                add_user_message($thread_id, $user_text);
                $run_id = start_run($thread_id, $ASSISTANT_ID);
                $run = wait_for_run($thread_id, $run_id, $POLL_MAX_TRIES, $POLL_DELAY_US);

                if (($run['status'] ?? '') === 'failed') {
                    $error = 'Assistant run failed.';
                } elseif (($run['status'] ?? '') === 'requires_action') {
                    // If you later support tool calls, handle them here then submit outputs.
                } elseif (($run['status'] ?? '') === 'timeout') {
                    $error = 'Assistant timed out. Please try again.';
                }
            }
        }
    }

    if (!empty($_SESSION['thread_id'])) {
        $history = fetch_history($_SESSION['thread_id']);
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
}
?>
<!-- UI -->
<div style="max-width:760px; margin:20px auto; font-family:Arial, sans-serif;">
  <h3>Site Assistant</h3>
  <p>Type a question below. Press <b>Enter</b> to send, <b>Shift+Enter</b> for a new line.</p>

  <?php if ($error): ?>
    <div style="margin:10px 0; padding:8px; border:1px solid #c00; border-radius:6px;">
      <strong>Error:</strong> <?php echo h($error); ?>
    </div>
  <?php endif; ?>

  <?php if (!empty($_SESSION['thread_id'])): ?>
    <div style="margin:4px 0; font-size:12px;">Thread: <?php echo h($_SESSION['thread_id']); ?></div>
  <?php endif; ?>

  <form id="chat-form" method="post" style="margin:12px 0;">
    <textarea id="chat-input" name="user_input" rows="3" style="width:100%; padding:6px;" placeholder="Ask your question..."></textarea>
    <div style="margin-top:8px; display:flex; gap:8px;">
      <button type="submit">Send</button>
      <button type="submit" name="reset_thread" value="1">Reset Conversation</button>
    </div>
  </form>

  <?php if (!empty($history) && is_array($history)): ?>
    <div style="margin-top:16px; padding:10px; border:1px solid #ccc; border-radius:8px;">
      <?php foreach ($history as $msg):
        // Label mapping: user => Question, assistant => Answer, system => (optional)
        $role = $msg['role'] ?? 'assistant';
        if ($role === 'user') $label = 'Question';
        elseif ($role === 'assistant') $label = 'Answer';
        else $label = ucfirst($role); // e.g., System
        $text = str_replace("\r\n", "\n", $msg['text'] ?? '');
        $refs = $msg['refs'] ?? [];
      ?>
        <div style="margin-bottom:14px;">
          <div style="font-weight:bold;"><?php echo h($label); ?></div>
          <div style="white-space:pre-wrap;"><?php echo nl2br(h($text)); ?></div>

          <?php if (!empty($refs)): ?>
            <div style="margin-top:6px; font-size:12px;">
              <em>References:</em>
              <ul style="margin:6px 0 0 18px; padding:0;">
                <?php foreach ($refs as $r): 
                  $fname = $r['filename'] ?? 'file';
                  $page  = $r['page'] ?? 'n/a';
                  // If you have your own document links, replace '#' with a real URL.
                ?>
                  <li>
                    <a href="#" title="file_id: <?php echo h($r['file_id']); ?>">
                      <?php echo h($fname); ?> — page <?php echo h($page); ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div style="margin-top:10px; color:#666;">No messages yet.</div>
  <?php endif; ?>

  <div style="margin-top:10px; font-size:12px; color:#555;">
    Conversation persists until you click “Reset Conversation”.
  </div>
</div>

<!-- Submit on Enter (Shift+Enter = newline) -->
<script>
(function(){
  var form = document.getElementById('chat-form');
  var input = document.getElementById('chat-input');

  input.addEventListener('keydown', function(e){
    if (e.key === 'Enter') {
      if (!e.shiftKey) {
        e.preventDefault();
        form.submit();
      }
      // if Shift+Enter, allow newline
    }
  });
})();
</script>

