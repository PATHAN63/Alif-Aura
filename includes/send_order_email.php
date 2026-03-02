<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

function sendOrderEmails($pdo, $orderId, $customerEmail, $customerName, $address, $items, $total, $paymentMethod) {
    $configPath = __DIR__ . '/smtp_config.php';
    if (!file_exists($configPath)) return false;
    $config = include $configPath;
    if (empty($config['username']) || empty($config['password'])) return false;

    $orderLink = SITE_URL . (BASE_PATH ?: '') . '/admin/orders.php';
    $itemsHtml = '';
    foreach ($items as $i) {
        $itemsHtml .= '<tr><td>' . htmlspecialchars($i['name']) . '</td><td>' . $i['quantity'] . '</td><td>' . formatPrice($i['price'] * $i['quantity']) . '</td></tr>';
    }
    $adminHtml = '<h2>New Order #' . $orderId . '</h2><p><strong>' . htmlspecialchars($customerName) . '</strong><br>' . htmlspecialchars($address) . '<br>' . htmlspecialchars($customerEmail) . '</p><p>Payment: ' . htmlspecialchars($paymentMethod) . '</p><table border="1" cellpadding="8"><tr><th>Product</th><th>Qty</th><th>Total</th></tr>' . $itemsHtml . '<tr><td colspan="2"><strong>Grand Total</strong></td><td>' . formatPrice($total) . '</td></tr></table><p><a href="' . $orderLink . '">View Orders</a></p>';
    $custHtml = '<h2>Order Confirmed #' . $orderId . '</h2><p>Thank you, ' . htmlspecialchars($customerName) . '!</p><p>Your order has been received. We will contact you soon.</p><table border="1" cellpadding="8"><tr><th>Product</th><th>Qty</th><th>Total</th></tr>' . $itemsHtml . '<tr><td colspan="2"><strong>Grand Total</strong></td><td>' . formatPrice($total) . '</td></tr></table>';

    $boundary = '----=_NextPart_' . md5(time());
    $headers = "MIME-Version: 1.0\r\nContent-Type: multipart/alternative; boundary=\"$boundary\"\r\n";
    $body = "--$boundary\r\nContent-Type: text/html; charset=UTF-8\r\n\r\n";
    $body .= '<!DOCTYPE html><html><body style="font-family:sans-serif;padding:20px">' . $adminHtml . '</body></html>';
    $body .= "\r\n--$boundary--";

    $adminMail = "From: {$config['from_name']} <{$config['from_email']}>\r\n" . $headers . "\r\n" . str_replace($adminHtml, '<!DOCTYPE html><html><body style="font-family:sans-serif;padding:20px">' . $adminHtml . '</body></html>', $body);
    $custMail = "From: {$config['from_name']} <{$config['from_email']}>\r\n" . $headers . "\r\n" . '<!DOCTYPE html><html><body style="font-family:sans-serif;padding:20px">' . $custHtml . '</body></html>';

    try {
        if (class_exists('PHPMailer\PHPMailer\PHPMailer')) {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $config['host'];
            $mail->Port = $config['port'];
            $mail->SMTPAuth = true;
            $mail->Username = $config['username'];
            $mail->Password = $config['password'];
            $mail->SMTPSecure = $config['encryption'] ?? 'tls';
            $mail->setFrom($config['from_email'], $config['from_name']);
            $mail->isHTML(true);
            $mail->Subject = 'New Order #' . $orderId . ' - ' . SITE_NAME;
            $mail->Body = $adminHtml;
            $mail->addAddress($config['from_email']);
            $mail->send();
            if ($customerEmail) {
                $mail->clearAddresses();
                $mail->addAddress($customerEmail);
                $mail->Subject = 'Order Confirmed #' . $orderId . ' - ' . SITE_NAME;
                $mail->Body = $custHtml;
                $mail->send();
            }
            return true;
        }
        $subj = 'New Order #' . $orderId . ' - ' . SITE_NAME;
        @mail($config['from_email'], $subj, strip_tags($adminHtml), "From: {$config['from_name']} <{$config['from_email']}>\r\nContent-Type: text/html; charset=UTF-8");
        if ($customerEmail) @mail($customerEmail, 'Order Confirmed #' . $orderId, strip_tags($custHtml), "From: {$config['from_name']} <{$config['from_email']}>\r\nContent-Type: text/html; charset=UTF-8");
        return true;
    } catch (Exception $e) { return false; }
}
