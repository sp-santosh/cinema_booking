<?php
/**
 * SMTP / mailer configuration.
 */

return [
    'host'       => getenv('MAIL_HOST')       ?: 'smtp.purelymail.com',
    'port'       => getenv('MAIL_PORT')       ?: 465, // SSL/TLS port
    'encryption' => getenv('MAIL_ENCRYPTION') ?: 'ssl',
    'username'   => getenv('MAIL_USERNAME')   ?: 'cinema@asknep.com', // Fill your email here
    'password'   => getenv('MAIL_PASSWORD')   ?: 'BN01J44A4mlND5', // Fill your password here
    'from' => [
        'address' => getenv('MAIL_FROM_ADDRESS') ?: 'cinema@asknep.com',
        'name'    => getenv('MAIL_FROM_NAME')    ?: 'CineBook',
    ],
];
