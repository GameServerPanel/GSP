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

    /** Mark an invoice as paid. */
    public function markInvoicePaid(int $invoiceId, string $txid, string $method, string $paidAt): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE `{$this->prefix}billing_invoices`
             SET payment_status='paid', payment_txid=?, payment_method=?, paid_date=?
             WHERE invoice_id = ? LIMIT 1"
        );
        if (!$stmt) return false;
        $stmt->bind_param('sssi', $txid, $method, $paidAt, $invoiceId);
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
    // Transaction (payment log) helpers
    // ---------------------------------------------------------------

    /** Insert a row into gsp_billing_transactions. Returns new transaction_id. */
    public function logTransaction(array $data): int
    {
        $stmt = $this->db->prepare(
            "INSERT INTO `{$this->prefix}billing_transactions`
                (invoice_id, user_id, home_id, payment_method, transaction_external_id,
                 amount, currency, status, raw_response)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        if (!$stmt) return 0;
        $rawJson = is_array($data['raw_response']) ? json_encode($data['raw_response']) : (string)($data['raw_response'] ?? '');
        $stmt->bind_param(
            'iiissdsss',
            $data['invoice_id'],
            $data['user_id'],
            $data['home_id'],
            $data['payment_method'],
            $data['transaction_external_id'],
            $data['amount'],
            $data['currency'],
            $data['status'],
            $rawJson
        );
        if (!$stmt->execute()) { $stmt->close(); return 0; }
        $id = (int)$stmt->insert_id;
        $stmt->close();
        return $id;
    }

    /** Get all transactions, optionally filtered. */
    public function getTransactions(array $filter = [], int $limit = 100, int $offset = 0): array
    {
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
