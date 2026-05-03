<?php

declare(strict_types=1);

function env_value(string $key, string $default = ''): string
{
    $value = getenv($key);
    return $value === false ? $default : $value;
}

function smtp_read_response($socket): string
{
    $response = '';
    while (!feof($socket)) {
        $line = fgets($socket, 515);
        if ($line === false) {
            break;
        }
        $response .= $line;
        if (preg_match('/^\d{3} /', $line)) {
            break;
        }
    }

    return $response;
}

function smtp_get_code(string $response): int
{
    $lines = preg_split('/\r\n|\n|\r/', trim($response));
    if (!$lines) {
        return 0;
    }

    $last = end($lines);
    return (int) substr((string) $last, 0, 3);
}

function smtp_expect($socket, array $codes): bool
{
    $response = smtp_read_response($socket);
    $code = smtp_get_code($response);
    return in_array($code, $codes, true);
}

function smtp_write($socket, string $command): void
{
    fwrite($socket, $command . "\r\n");
}

function send_email_smtp(string $to, string $subject, string $body): bool
{
    $host = env_value('SMTP_HOST');
    $port = (int) env_value('SMTP_PORT', '587');
    $user = env_value('SMTP_USER');
    $pass = env_value('SMTP_PASS');
    $from = env_value('SMTP_FROM', $user);
    $from_name = env_value('SMTP_FROM_NAME', 'Secure Login');
    $encryption = strtolower(env_value('SMTP_ENCRYPTION', 'tls'));
    $helo = env_value('SMTP_HELO', 'localhost');

    if ($host === '' || $user === '' || $pass === '' || $from === '') {
        return false;
    }

    $remote_host = $host;
    if ($encryption === 'ssl') {
        $remote_host = 'ssl://' . $host;
    }

    $socket = fsockopen($remote_host, $port, $errno, $errstr, 10);
    if (!$socket) {
        return false;
    }

    if (!smtp_expect($socket, [220])) {
        fclose($socket);
        return false;
    }

    smtp_write($socket, 'EHLO ' . $helo);
    if (!smtp_expect($socket, [250])) {
        fclose($socket);
        return false;
    }

    if ($encryption === 'tls') {
        smtp_write($socket, 'STARTTLS');
        if (!smtp_expect($socket, [220])) {
            fclose($socket);
            return false;
        }
        if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
            fclose($socket);
            return false;
        }
        smtp_write($socket, 'EHLO ' . $helo);
        if (!smtp_expect($socket, [250])) {
            fclose($socket);
            return false;
        }
    }

    smtp_write($socket, 'AUTH LOGIN');
    if (!smtp_expect($socket, [334])) {
        fclose($socket);
        return false;
    }
    smtp_write($socket, base64_encode($user));
    if (!smtp_expect($socket, [334])) {
        fclose($socket);
        return false;
    }
    smtp_write($socket, base64_encode($pass));
    if (!smtp_expect($socket, [235])) {
        fclose($socket);
        return false;
    }

    smtp_write($socket, 'MAIL FROM:<' . $from . '>');
    if (!smtp_expect($socket, [250])) {
        fclose($socket);
        return false;
    }

    smtp_write($socket, 'RCPT TO:<' . $to . '>');
    if (!smtp_expect($socket, [250, 251])) {
        fclose($socket);
        return false;
    }

    smtp_write($socket, 'DATA');
    if (!smtp_expect($socket, [354])) {
        fclose($socket);
        return false;
    }

    $safe_body = str_replace("\r\n", "\n", $body);
    $lines = explode("\n", $safe_body);
    foreach ($lines as &$line) {
        if (strpos($line, '.') === 0) {
            $line = '.' . $line;
        }
    }
    unset($line);
    $safe_body = implode("\r\n", $lines);

    $headers = [
        'From: ' . $from_name . ' <' . $from . '>',
        'To: <' . $to . '>',
        'Subject: ' . $subject,
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
    ];

    $message = implode("\r\n", $headers) . "\r\n\r\n" . $safe_body;

    smtp_write($socket, $message . "\r\n.");
    if (!smtp_expect($socket, [250])) {
        fclose($socket);
        return false;
    }

    smtp_write($socket, 'QUIT');
    fclose($socket);
    return true;
}
