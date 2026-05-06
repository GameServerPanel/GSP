<?php
/**
 * BillingRepository — data layer for the billing module.
 * All SQL lives here. Accepts a mysqli connection.
 */
class BillingRepository
{
    private mysqli $db;
    private string $prefix;

    public function __construct(mysqli $db, string $prefix = 'gsp_')
    {
        $this->db     = $db;
        $this->prefix = $prefix;
    }

    // ---------------------------------------------------------------
    // Invoice helpers
    // ---------------------------------------------------------------

    /** Find a single 'unpaid' invoice by ID, owned by $userId. */
    public function getUnpaidInvoice(int $invoiceId, int $userId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->prefix}billing_invoices`
             WHERE invoice_id = ? AND user_id = ? AND payment_status IN ('unpaid','due') LIMIT 1"
        );
        if (!$stmt) return null;
        $stmt->bind_param('ii', $invoiceId, $userId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** Get all unpaid invoices for a user. */
    public function getUnpaidInvoicesForUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->prefix}billing_invoices`
             WHERE user_id = ? AND payment_status IN ('unpaid','due')
             ORDER BY invoice_id ASC"
        );
        if (!$stmt) return [];
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /** Mark an invoice as paid. Also sets status='paid' so it disappears from cart queries. */
    public function markInvoicePaid(int $invoiceId, string $txid, string $method, string $paidAt): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `{$this->prefix}billing_invoices`
             SET payment_status='paid', status='paid', payment_txid=?, payment_method=?, paid_date=?
             WHERE invoice_id = ? LIMIT 1"
        );
        if (!$stmt) return false;
        $stmt->bind_param('sssi', $txid, $method, $paidAt, $invoiceId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /**
     * Create a billing_orders row from invoice/payment data.
     * Returns new order_id (0 on failure).
     *
     * @param array $data Keys: user_id, service_id, home_name, ip, qty, invoice_duration,
     *                         max_players, price, remote_control_password, ftp_password,
     *                         status, end_date, payment_txid, paid_ts, coupon_id
     */
    public function createOrder(array $data): int
    {
        $now      = date('Y-m-d H:i:s');
        $status   = (string)($data['status'] ?? 'Active');
        $endDate  = $data['end_date'] ?? null;
        $txid     = (string)($data['payment_txid'] ?? '');
        $paidTs   = (string)($data['paid_ts'] ?? $now);
        $couponId = intval($data['coupon_id'] ?? 0);
        $ip       = (string)($data['ip'] ?? '0');
        $qty      = intval($data['qty'] ?? 1);
        $maxPl    = intval($data['max_players'] ?? 0);
        $price    = (float)($data['price'] ?? 0);
        $userId   = intval($data['user_id']);
        $svcId    = intval($data['service_id']);
        $homeName = (string)($data['home_name'] ?? '');
        $invDur   = (string)($data['invoice_duration'] ?? 'month');
        $rcp      = (string)($data['remote_control_password'] ?? '');
        $ftp      = (string)($data['ftp_password'] ?? '');

        $stmt = $this->db->prepare(
            "INSERT INTO `{$this->prefix}billing_orders`
                (user_id, service_id, home_name, ip, qty, invoice_duration, max_players,
                 price, remote_control_password, ftp_password, home_id, status,
                 order_date, end_date, payment_txid, paid_ts, coupon_id)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '0', ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return 0;
        $stmt->bind_param(
            'iissiisdsssssssi',
            $userId, $svcId, $homeName, $ip, $qty, $invDur, $maxPl,
            $price, $rcp, $ftp,
            $status, $now, $endDate, $txid, $paidTs, $couponId
        );
        if (!$stmt->execute()) { $stmt->close(); return 0; }
        $id = (int)$stmt->insert_id;
        $stmt->close();
        return $id;
    }

    /**
     * Link a billing_invoice row to its corresponding billing_orders row.
     * Called after createOrder() so the capture endpoint can be idempotent.
     */
    public function updateInvoiceOrderId(int $invoiceId, int $orderId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `{$this->prefix}billing_invoices` SET order_id = ? WHERE invoice_id = ? LIMIT 1"
        );
        if (!$stmt) return false;
        $stmt->bind_param('ii', $orderId, $invoiceId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    /** Create a new invoice record. Returns new invoice_id or 0 on failure. */
    public function createInvoice(array $data): int
    {
        $fields = [
            'user_id', 'service_id', 'home_id', 'home_name',
            'customer_name', 'customer_email',
            'rate_type', 'rate_per_player', 'players',
            'period_start', 'period_end',
            'subtotal', 'total_due',
            'currency', 'payment_status', 'payment_method', 'description',
        ];
        $cols   = implode(',', array_map(fn($f) => "`$f`", $fields));
        $places = implode(',', array_fill(0, count($fields), '?'));
        $types  = 'iiissssssiissssss';

        $stmt = $this->db->prepare(
            "INSERT INTO `{$this->prefix}billing_invoices` ({$cols}) VALUES ({$places})"
        );
        if (!$stmt) return 0;

        $vals = [];
        foreach ($fields as $f) {
            $vals[] = $data[$f] ?? null;
        }
        $stmt->bind_param($types, ...$vals);
        if (!$stmt->execute()) { $stmt->close(); return 0; }
        $id = (int)$stmt->insert_id;
        $stmt->close();
        return $id;
    }

    // ---------------------------------------------------------------
    // Safe table-creation helpers (idempotent, check INFORMATION_SCHEMA first)
    // ---------------------------------------------------------------

    /**
     * Ensure billing_transactions table exists.
     * Safe to call on every request; uses INFORMATION_SCHEMA to skip if already present.
     */
    public function ensureBillingTransactionsTable(): bool
    {
        $res = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = '{$this->prefix}billing_transactions'"
        );
        if ($res && (int)$res->fetch_assoc()['cnt'] > 0) {
            return true;
        }
        return (bool)$this->db->query(
            "CREATE TABLE IF NOT EXISTS `{$this->prefix}billing_transactions` (
                `transaction_id`          INT(11)       NOT NULL AUTO_INCREMENT,
                `invoice_id`              INT(11)       NOT NULL DEFAULT 0,
                `user_id`                 INT(11)       NOT NULL DEFAULT 0,
                `home_id`                 INT(11)       NOT NULL DEFAULT 0,
                `payment_method`          VARCHAR(50)   NOT NULL DEFAULT 'paypal',
                `transaction_external_id` VARCHAR(255)  NOT NULL DEFAULT '',
                `amount`                  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
                `currency`                VARCHAR(3)    NOT NULL DEFAULT 'USD',
                `status`                  ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
                `raw_response`            MEDIUMTEXT    NULL,
                `created_at`              DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `updated_at`              DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`transaction_id`),
                KEY `invoice_id`     (`invoice_id`),
                KEY `user_id`        (`user_id`),
                KEY `home_id`        (`home_id`),
                KEY `status`         (`status`),
                KEY `payment_method` (`payment_method`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    /**
     * Ensure billing_paypal_errors table exists.
     * Safe to call on every request; uses INFORMATION_SCHEMA to skip if already present.
     */
    public function ensureBillingPaypalErrorsTable(): bool
    {
        $res = $this->db->query(
            "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = '{$this->prefix}billing_paypal_errors'"
        );
        if ($res && (int)$res->fetch_assoc()['cnt'] > 0) {
            return true;
        }
        return (bool)$this->db->query(
            "CREATE TABLE IF NOT EXISTS `{$this->prefix}billing_paypal_errors` (
                `id`                INT         NOT NULL AUTO_INCREMENT,
                `context`           VARCHAR(64) NOT NULL DEFAULT '',
                `error_code`        VARCHAR(128) NOT NULL DEFAULT '',
                `message`           TEXT        NULL,
                `paypal_debug_id`   VARCHAR(128) NULL,
                `order_id`          VARCHAR(128) NULL,
                `capture_id`        VARCHAR(128) NULL,
                `billing_order_id`  INT         NULL,
                `user_id`           INT         NULL,
                `raw_json`          LONGTEXT    NULL,
                `created_at`        DATETIME    NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                KEY `idx_context`    (`context`),
                KEY `idx_created_at` (`created_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    // ---------------------------------------------------------------
    // Transaction (payment log) helpers
    // ---------------------------------------------------------------

    /** Insert a row into billing_transactions. Returns new transaction_id. */
    public function logTransaction(array $data): int
    {
        $this->ensureBillingTransactionsTable();
        $stmt = $this->db->prepare(
            "INSERT INTO `{$this->prefix}billing_transactions`
                (invoice_id, user_id, home_id, payment_method, transaction_external_id,
                 amount, currency, status, raw_response)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return 0;
        $rawJson = is_array($data['raw_response']) ? json_encode($data['raw_response']) : (string)($data['raw_response'] ?? '');
        $invoiceId = intval($data['invoice_id'] ?? 0);
        $userId    = intval($data['user_id']    ?? 0);
        $homeId    = intval($data['home_id']    ?? 0);
        $method    = (string)($data['payment_method']          ?? 'paypal');
        $extId     = (string)($data['transaction_external_id'] ?? '');
        $amount    = (float)($data['amount']   ?? 0);
        $currency  = (string)($data['currency'] ?? 'USD');
        $status    = (string)($data['status']   ?? 'completed');
        $stmt->bind_param(
            'iiissdsss',
            $invoiceId, $userId, $homeId, $method, $extId, $amount, $currency, $status, $rawJson
        );
        if (!$stmt->execute()) { $stmt->close(); return 0; }
        $id = (int)$stmt->insert_id;
        $stmt->close();
        return $id;
    }

    /** Get all transactions, optionally filtered. Creates the table if missing. */
    public function getTransactions(array $filter = [], int $limit = 100, int $offset = 0): array
    {
        if (!$this->ensureBillingTransactionsTable()) {
            return [];
        }
        $where  = '1=1';
        $params = [];
        $types  = '';
        if (!empty($filter['user_id'])) {
            $where   .= ' AND t.user_id = ?';
            $params[] = intval($filter['user_id']);
            $types   .= 'i';
        }
        if (!empty($filter['home_id'])) {
            $where   .= ' AND t.home_id = ?';
            $params[] = intval($filter['home_id']);
            $types   .= 'i';
        }
        if (!empty($filter['payment_method'])) {
            $where   .= ' AND t.payment_method = ?';
            $params[] = $filter['payment_method'];
            $types   .= 's';
        }
        $sql = "SELECT t.*, u.users_login, u.users_email
                FROM `{$this->prefix}billing_transactions` t
                LEFT JOIN `{$this->prefix}users` u ON u.user_id = t.user_id
                WHERE {$where}
                ORDER BY t.transaction_id DESC
                LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types   .= 'ii';
        $stmt = $this->db->prepare($sql);
        if (!$stmt) return [];
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ---------------------------------------------------------------
    // PayPal error log helpers
    // ---------------------------------------------------------------

    /**
     * Insert a row into billing_paypal_errors. Never logs client secrets.
     * Returns new error log id (0 on failure).
     */
    public function logPaypalError(array $data): int
    {
        $this->ensureBillingPaypalErrorsTable();
        $stmt = $this->db->prepare(
            "INSERT INTO `{$this->prefix}billing_paypal_errors`
                (context, error_code, message, paypal_debug_id, order_id, capture_id,
                 billing_order_id, user_id, raw_json)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return 0;
        $context        = substr((string)($data['context']         ?? ''), 0, 64);
        $errorCode      = substr((string)($data['error_code']      ?? ''), 0, 128);
        $message        = (string)($data['message']        ?? '');
        $debugId        = isset($data['paypal_debug_id'])  ? substr((string)$data['paypal_debug_id'],  0, 128) : null;
        $orderId        = isset($data['order_id'])         ? substr((string)$data['order_id'],         0, 128) : null;
        $captureId      = isset($data['capture_id'])       ? substr((string)$data['capture_id'],       0, 128) : null;
        $billingOrderId = isset($data['billing_order_id']) ? intval($data['billing_order_id']) : null;
        $userId         = isset($data['user_id'])         ? intval($data['user_id'])         : null;
        $rawJson        = isset($data['raw_json'])
            ? (is_array($data['raw_json']) ? json_encode($data['raw_json']) : (string)$data['raw_json'])
            : null;
        // Truncate large payloads to avoid LONGTEXT bloat
        if ($rawJson !== null && strlen($rawJson) > 65536) {
            $rawJson = substr($rawJson, 0, 65536) . '…[truncated]';
        }
        $stmt->bind_param(
            'ssssssiis',
            $context, $errorCode, $message, $debugId, $orderId, $captureId,
            $billingOrderId, $userId, $rawJson
        );
        if (!$stmt->execute()) { $stmt->close(); return 0; }
        $id = (int)$stmt->insert_id;
        $stmt->close();
        return $id;
    }

    /**
     * Return the $limit most recent rows from billing_paypal_errors.
     * Returns empty array if the table does not exist.
     */
    public function getRecentPaypalErrors(int $limit = 10): array
    {
        if (!$this->ensureBillingPaypalErrorsTable()) {
            return [];
        }
        $stmt = $this->db->prepare(
            "SELECT id, created_at, context, error_code, message,
                    paypal_debug_id, order_id, capture_id, billing_order_id, user_id
             FROM `{$this->prefix}billing_paypal_errors`
             ORDER BY id DESC
             LIMIT ?"
        );
        if (!$stmt) return [];
        $stmt->bind_param('i', $limit);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ---------------------------------------------------------------
    // Server home (billing state) helpers
    // ---------------------------------------------------------------

    /** Get server home billing info by home_id. */
    public function getServerHomeBilling(int $homeId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT home_id, home_name, user_id_main, billing_status, billing_expires_at,
                    billing_price, billing_rate_type, billing_players, billing_enabled,
                    next_invoice_date, server_expiration_date, billing_invoice_sent_at
             FROM `{$this->prefix}server_homes`
             WHERE home_id = ? LIMIT 1"
        );
        if (!$stmt) return null;
        $stmt->bind_param('i', $homeId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** Update billing state fields on server_homes. */
    public function updateServerHomeBilling(int $homeId, array $data): bool
    {
        $allowed = [
            'billing_status', 'billing_expires_at', 'billing_price',
            'billing_rate_type', 'billing_players', 'billing_enabled',
            'next_invoice_date', 'server_expiration_date', 'billing_invoice_sent_at',
        ];
        $set    = [];
        $params = [];
        $types  = '';
        foreach ($allowed as $col) {
            if (array_key_exists($col, $data)) {
                $set[]    = "`{$col}` = ?";
                $params[] = $data[$col];
                $val      = $data[$col];
                if ($val === null) {
                    $types .= 's'; // NULL binds safely as string in mysqli
                } elseif (is_int($val)) {
                    $types .= 'i';
                } elseif (is_float($val)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
            }
        }
        if (empty($set)) return false;
        $params[] = $homeId;
        $types   .= 'i';
        $stmt = $this->db->prepare(
            "UPDATE `{$this->prefix}server_homes` SET " . implode(', ', $set) . " WHERE home_id = ? LIMIT 1"
        );
        if (!$stmt) return false;
        $stmt->bind_param($types, ...$params);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }

    // ---------------------------------------------------------------
    // Service helpers
    // ---------------------------------------------------------------

    /** Get a billing service by ID. Returns null if not found / disabled. */
    public function getService(int $serviceId, bool $mustBeEnabled = true): ?array
    {
        $extra = $mustBeEnabled ? ' AND enabled = 1' : '';
        $stmt  = $this->db->prepare(
            "SELECT * FROM `{$this->prefix}billing_services` WHERE service_id = ?{$extra} LIMIT 1"
        );
        if (!$stmt) return null;
        $stmt->bind_param('i', $serviceId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** Get enabled services (for storefront listing). */
    public function getEnabledServices(): array
    {
        $res = $this->db->query(
            "SELECT * FROM `{$this->prefix}billing_services` WHERE enabled = 1 ORDER BY service_name"
        );
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }

    // ---------------------------------------------------------------
    // Legacy billing_orders helpers (kept for backward compat during migration)
    // ---------------------------------------------------------------

    /** Get an active order by order_id. */
    public function getOrder(int $orderId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM `{$this->prefix}billing_orders` WHERE order_id = ? LIMIT 1"
        );
        if (!$stmt) return null;
        $stmt->bind_param('i', $orderId);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }

    /** Extend an existing order's end_date. */
    public function extendOrder(int $orderId, string $newEndDate, string $txid, string $now): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `{$this->prefix}billing_orders`
             SET status='Active', end_date=?, payment_txid=?, paid_ts=?
             WHERE order_id=? LIMIT 1"
        );
        if (!$stmt) return false;
        $stmt->bind_param('sssi', $newEndDate, $txid, $now, $orderId);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok;
    }
}
