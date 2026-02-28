<?php
/**
 * SMTP / mailer configuration.
 */

return [
    'host'       => getenv('MAIL_HOST')       ?: 'smtp.mailtrap.io',
    'port'       => getenv('MAIL_PORT')       ?: 587,
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'tls',   // 'tls' | 'ssl' | ''
    'username'   => getenv('MAIL_USERNAME')   ?: '',
    'password'   => getenv('MAIL_PASSWORD')   ?: '',
    'from' => [
        'address' => getenv('MAIL_FROM_ADDRESS') ?: 'noreply@cinebook.local',
        'name'    => getenv('MAIL_FROM_NAME')    ?: 'CineBook',
    ],
];
