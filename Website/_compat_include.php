<?php
function website_billing_runtime_file(string $relative): string
{
    $target = realpath(__DIR__ . '/../Panel/modules/billing/' . ltrim($relative, '/'));
    if ($target === false || strpos($target, realpath(__DIR__ . '/../Panel/modules/billing')) !== 0) {
        http_response_code(500);
        echo 'Billing runtime file not found.';
        exit;
    }
    return $target;
}
