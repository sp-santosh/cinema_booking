<?php
/**
 * Simple SMTPSender using native PHP sockets.
 * Supports TLS/SSL for purelymail.com.
 */
class SMTPSender
{
    private string $host;
    private int $port;
    private string $username;
    private string $password;
    private string $fromEmail;
    private string $fromName;
    private string $encryption;

    public function __construct(array $config)
    {
        $this->host = $config['host'];
        $this->port = (int)$config['port'];
        $this->username = $config['username'];
        $this->password = $config['password'];
        $this->fromEmail = $config['from']['address'];
        $this->fromName = $config['from']['name'];
        $this->encryption = strtolower($config['encryption'] ?? 'tls');
    }

    public function send(string $to, string $subject, string $htmlBody): bool
    {
        $timeout = 10;
        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ]);

        $remote = ($this->encryption === 'ssl' ? 'ssl://' : '') . $this->host;
        $socket = stream_socket_client($remote . ':' . $this->port, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);

        if (!$socket) {
            error_log("SMTP Connection Error: $errstr ($errno)");
            return false;
        }

        $this->getResponse($socket); // 220

        $host = $_SERVER['HTTP_HOST'] ?? gethostname() ?? 'localhost';
        $this->sendCommand($socket, "EHLO " . $host);
        $this->getResponse($socket);

        if ($this->encryption === 'tls') {
            if (!$this->sendCommand($socket, "STARTTLS")) return false;
            $res = $this->getResponse($socket);
            if (strpos($res, '220') !== 0) {
                error_log("SMTP STARTTLS Failed: " . $res);
                return false;
            }
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_ANY_CLIENT)) {
                error_log("Failed to enable crypto (TLS)");
                return false;
            }
            $host = preg_replace('/[^a-z0-9.-]/i', '', $_SERVER['HTTP_HOST'] ?? gethostname() ?? 'localhost');
            if (!$this->sendCommand($socket, "EHLO " . $host)) return false;
            $this->getResponse($socket);
        }

        $this->sendCommand($socket, "AUTH LOGIN");
        $this->getResponse($socket);

        $this->sendCommand($socket, base64_encode($this->username));
        $this->getResponse($socket);

        $this->sendCommand($socket, base64_encode($this->password));
        $this->getResponse($socket);

        $this->sendCommand($socket, "MAIL FROM:<" . $this->fromEmail . ">");
        $this->getResponse($socket);

        $this->sendCommand($socket, "RCPT TO:<" . $to . ">");
        $this->getResponse($socket);

        $this->sendCommand($socket, "DATA");
        $this->getResponse($socket);

        $boundary = md5(uniqid((string)time()));
        $headers = [
            "MIME-Version: 1.0",
            "Content-Type: text/html; charset=UTF-8",
            "From: \"{$this->fromName}\" <{$this->fromEmail}>",
            "To: <{$to}>",
            "Subject: {$subject}",
            "Date: " . date('r'),
            "Message-ID: <" . time() . "cb@" . $this->host . ">"
        ];

        $message = implode("\r\n", $headers) . "\r\n\r\n" . $htmlBody . "\r\n.\r\n";
        $this->sendCommand($socket, $message, false);
        $this->getResponse($socket);

        $this->sendCommand($socket, "QUIT");
        fclose($socket);

        return true;
    }

    private function sendCommand($socket, $cmd, $newline = true): bool
    {
        $data = $cmd . ($newline ? "\r\n" : "");
        $sent = fwrite($socket, $data);
        if ($sent === false) {
            error_log("SMTP Write Error: Could not send command " . substr($cmd, 0, 10));
            return false;
        }
        return true;
    }

    private function getResponse($socket): string
    {
        $res = "";
        while ($line = fgets($socket, 512)) {
            $res .= $line;
            if (isset($line[3]) && $line[3] == ' ') break;
        }
        
        // Log if it's not a success code (anything not starting with 2 or 3)
        if (!empty($res) && !in_array($res[0], ['2', '3'])) {
            error_log("SMTP Server Error: " . trim($res));
        }
        
        return $res;
    }
}
