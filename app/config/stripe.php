<?php
/**
 * Stripe payment gateway configuration.
 */

return [
    'secret_key'      => getenv('STRIPE_SECRET_KEY')      ?: 'sk_test_REPLACE_ME',
    'publishable_key' => getenv('STRIPE_PUBLISHABLE_KEY') ?: 'pk_test_REPLACE_ME',
    'webhook_secret'  => getenv('STRIPE_WEBHOOK_SECRET')  ?: '',
    'currency'        => 'gbp',
];
