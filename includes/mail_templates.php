<?php
function email_wrapper(string $title, string $content): string {
    return '<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
         . '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>'
         . '</head><body style="margin:0;padding:0;background:#0a0a0a;color:#fff;font-family:-apple-system, BlinkMacSystemFont, \"SF Pro Text\", \"Segoe UI\", Roboto, Arial, sans-serif">'
         . '<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#0a0a0a;padding:24px 0">'
         . '<tr><td align="center">'
         . '<table role="presentation" width="600" cellspacing="0" cellpadding="0" style="width:600px;max-width:92%;background:#121212;border:1px solid rgba(255,255,255,0.12);border-radius:12px">'
         . '<tr><td style="padding:20px 24px;border-bottom:1px solid rgba(255,255,255,0.12)"><h1 style="margin:0;color:#ff7a00;font-size:20px">' . htmlspecialchars(SITE_NAME, ENT_QUOTES, 'UTF-8') . '</h1></td></tr>'
         . '<tr><td style="padding:24px 24px 12px 24px">' . $content . '</td></tr>'
         . '<tr><td style="padding:12px 24px 24px 24px;color:#b5b5b7;font-size:12px;border-top:1px solid rgba(255,255,255,0.12)">'
         . 'Cet email vous est envoyé automatiquement, merci de ne pas y répondre.'
         . '</td></tr>'
         . '</table>'
         . '</td></tr></table>'
         . '</body></html>';
}

function tpl_welcome(string $userName): string {
    $content  = '<h2 style="margin:0 0 8px 0;">Bienvenue, ' . htmlspecialchars($userName, ENT_QUOTES, 'UTF-8') . ' 👋</h2>';
    $content .= '<p style="margin:0 0 16px 0;color:#b5b5b7">Votre compte est prêt. Commencez à booster votre présence dès maintenant.</p>';
    $content .= '<a href="' . rtrim(SITE_URL, '/') . '/dashboard.php" '
             . 'style="display:inline-block;padding:12px 18px;background:#ff7a00;color:#0a0a0a;text-decoration:none;border-radius:10px;font-weight:700">Aller au dashboard</a>';
    return email_wrapper('Bienvenue', $content);
}

function tpl_order_confirm(int $orderId, string $amount): string {
    $content  = '<h2 style="margin:0 0 8px 0;">Commande #' . (int)$orderId . '</h2>';
    $content .= '<p style="margin:0 0 16px 0;color:#b5b5b7">Montant: <strong style="color:#fff">' . htmlspecialchars($amount, ENT_QUOTES, 'UTF-8') . '</strong></p>';
    $content .= '<p style="margin:0 0 16px 0;color:#b5b5b7">Veuillez suivre les instructions de paiement et envoyer votre preuve.</p>';
    $content .= '<a href="' . rtrim(SITE_URL, '/') . '/payment.php?order_id=' . (int)$orderId . '" '
             . 'style="display:inline-block;padding:12px 18px;background:#ff7a00;color:#0a0a0a;text-decoration:none;border-radius:10px;font-weight:700">Payer maintenant</a>';
    return email_wrapper('Confirmation de commande', $content);
}

function tpl_payment_received(int $orderId): string {
    $content  = '<h2 style="margin:0 0 8px 0;">Preuve de paiement reçue</h2>';
    $content .= '<p style="margin:0 0 16px 0;color:#b5b5b7">Votre commande #' . (int)$orderId . ' sera traitée sous 24h.</p>';
    $content .= '<a href="' . rtrim(SITE_URL, '/') . '/orders.php" '
             . 'style="display:inline-block;padding:12px 18px;background:#ff7a00;color:#0a0a0a;text-decoration:none;border-radius:10px;font-weight:700">Voir mes commandes</a>';
    return email_wrapper('Preuve reçue', $content);
}