<?php
function send_html_mail(string $to, string $subject, string $htmlBody, string $fromEmail = 'no-reply@smmwebsite.com', string $fromName = 'SMM Pro'): bool {
    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= 'From: ' . sprintf('"%s" <%s>', $fromName, $fromEmail) . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

    // Normalize subject to avoid header issues
    $subject = trim(preg_replace('/\s+/', ' ', $subject));

    return @mail($to, $subject, $htmlBody, $headers);
}