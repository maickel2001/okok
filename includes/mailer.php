<?php
function derive_default_from(): array {
    $envFromEmail = getenv('MAIL_FROM_EMAIL');
    $envFromName = getenv('MAIL_FROM_NAME');
    if (!empty($envFromEmail) && !empty($envFromName)) {
        return [$envFromEmail, $envFromName];
    }

    $host = 'localhost';
    if (defined('SITE_URL')) {
        $parts = parse_url(SITE_URL);
        if ($parts && isset($parts['host'])) {
            $host = $parts['host'];
        }
    } elseif (!empty($_SERVER['HTTP_HOST'])) {
        $host = $_SERVER['HTTP_HOST'];
    }
    $email = 'no-reply@' . preg_replace('/^www\./i', '', $host);
    $name = defined('SITE_NAME') ? SITE_NAME : 'SMM Pro';
    return [$email, $name];
}

function mail_log(string $line): void {
    $dir = defined('LOGS_DIR') ? LOGS_DIR : __DIR__ . '/../logs/';
    if (!is_dir($dir)) { @mkdir($dir, 0777, true); }
    $file = rtrim($dir, '/').'/mail.log';
    $ts = date('Y-m-d H:i:s');
    @file_put_contents($file, "[$ts] $line\n", FILE_APPEND);
}

function send_html_mail(string $to, string $subject, string $htmlBody, string $fromEmail = '', string $fromName = ''): bool {
    if ($fromEmail === '' || $fromName === '') {
        list($dEmail, $dName) = derive_default_from();
        if ($fromEmail === '') { $fromEmail = $dEmail; }
        if ($fromName === '') { $fromName = $dName; }
    }

    // Encode subject for UTF-8 safety if mbstring is available
    if (function_exists('mb_encode_mimeheader')) {
        $encodedSubject = mb_encode_mimeheader(trim(preg_replace('/\s+/', ' ', $subject)), 'UTF-8');
    } else {
        $encodedSubject = trim(preg_replace('/\s+/', ' ', $subject));
    }

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . sprintf('"%s" <%s>', $fromName, $fromEmail) . "\r\n";
    $headers .= 'Reply-To: ' . $fromEmail . "\r\n";
    $bcc = getenv('MAIL_BCC');
    if (!empty($bcc)) {
        $headers .= 'Bcc: ' . $bcc . "\r\n";
    }
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    $returnPath = getenv('MAIL_RETURN_PATH');
    if (!empty($returnPath)) {
        $ok = @mail($to, $encodedSubject, $htmlBody, $headers, '-f ' . escapeshellarg($returnPath));
    } else {
        $ok = @mail($to, $encodedSubject, $htmlBody, $headers);
    }
    mail_log(($ok ? 'OK' : 'FAIL') . " to=$to subj=" . str_replace(["\r","\n"], ' ', $subject));
    return $ok;
}