<?php
// Mail config for event inscriptions
// Fill these with your app password credentials
if (!defined('MAIL_SMTP_HOST')) {
    define('MAIL_SMTP_HOST', 'smtp.gmail.com');
}
if (!defined('MAIL_SMTP_PORT')) {
    define('MAIL_SMTP_PORT', 587);
}
if (!defined('MAIL_SMTP_USER')) {
    define('MAIL_SMTP_USER', 'peaceconnect3@gmail.com');
}
if (!defined('MAIL_SMTP_PASS')) {
    define('MAIL_SMTP_PASS', 'azfyrpaluphkdmio');
}
if (!defined('MAIL_FROM_ADDRESS')) {
    define('MAIL_FROM_ADDRESS', MAIL_SMTP_USER);
}
if (!defined('MAIL_FROM_NAME')) {
    define('MAIL_FROM_NAME', 'PeaceConnect');
}
if (!defined('BASE_URL')) {
    // Adjust to your local or production base URL
    define('BASE_URL', 'http://localhost/PeaceConnect');
}
