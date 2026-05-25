<?php

declare(strict_types=1);

$smtpLastError = '';

function smtpSetLastError(string $message): void
{
    $GLOBALS['smtpLastError'] = $message;
}

function smtpLastError(): string
{
    return (string) ($GLOBALS['smtpLastError'] ?? '');
}

function smtpRead($socket): string
{
    $response = '';

    while (($line = fgets($socket, 515)) !== false) {
        $response .= $line;
        if (strlen($line) >= 4 && $line[3] === ' ') {
            break;
        }
    }

    return $response;
}

function smtpCommand($socket, string $command, array $expectedCodes): string
{
    fwrite($socket, $command . "\r\n");
    $response = smtpRead($socket);
    $code = (int) substr($response, 0, 3);

    if (!in_array($code, $expectedCodes, true)) {
        throw new RuntimeException('SMTP error: ' . trim($response));
    }

    return $response;
}

function encodeHeader(string $value): string
{
    return '=?UTF-8?B?' . base64_encode($value) . '?=';
}

function sendSmtpMail(string $to, string $subject, string $body): bool
{
    smtpSetLastError('');
    $config = require __DIR__ . '/../config/mail.php';

    if (empty($config['username']) || empty($config['password']) || empty($config['from_email'])) {
        smtpSetLastError('Configuration SMTP incomplete : renseignez username, password et from_email dans config/mail.php.');
        return false;
    }

    $host = (string) $config['host'];
    $port = (int) $config['port'];
    $transportHost = ($config['secure'] ?? 'ssl') === 'ssl' ? 'ssl://' . $host : $host;
    $socket = @fsockopen($transportHost, $port, $errno, $errstr, 15);

    if (!$socket) {
        smtpSetLastError("Connexion SMTP impossible vers {$host}:{$port} - {$errstr} ({$errno}).");
        return false;
    }

    stream_set_timeout($socket, 15);

    try {
        $greeting = smtpRead($socket);
        if ((int) substr($greeting, 0, 3) !== 220) {
            throw new RuntimeException('SMTP greeting failed: ' . trim($greeting));
        }

        smtpCommand($socket, 'EHLO localhost', [250]);

        if (($config['secure'] ?? '') === 'tls') {
            smtpCommand($socket, 'STARTTLS', [220]);
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new RuntimeException('SMTP TLS failed.');
            }
            smtpCommand($socket, 'EHLO localhost', [250]);
        }

        smtpCommand($socket, 'AUTH LOGIN', [334]);
        smtpCommand($socket, base64_encode((string) $config['username']), [334]);
        $smtpPassword = str_replace(' ', '', (string) $config['password']);
        smtpCommand($socket, base64_encode($smtpPassword), [235]);

        $fromEmail = (string) $config['from_email'];
        $fromName = (string) ($config['from_name'] ?? 'NOCIBE S.A');
        smtpCommand($socket, 'MAIL FROM:<' . $fromEmail . '>', [250]);
        smtpCommand($socket, 'RCPT TO:<' . $to . '>', [250, 251]);
        smtpCommand($socket, 'DATA', [354]);

        $headers = [
            'From: ' . encodeHeader($fromName) . ' <' . $fromEmail . '>',
            'To: <' . $to . '>',
            'Subject: ' . encodeHeader($subject),
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'Content-Transfer-Encoding: 8bit',
        ];
        $message = implode("\r\n", $headers) . "\r\n\r\n" . str_replace("\n.", "\n..", $body);

        smtpCommand($socket, $message . "\r\n.", [250]);
        smtpCommand($socket, 'QUIT', [221]);
        fclose($socket);

        return true;
    } catch (Throwable $exception) {
        smtpSetLastError($exception->getMessage());
        fclose($socket);
        return false;
    }
}
