<?php

switch (HOSTNAME) {
    case ('yggdrasil');
        define('DB_TYPE', 'postgres');
        define('DB_USER', 'loup');
        define('DB_PASS', 'postgresTest');
        define('DB_HOST', 'localhost');
        define('DB', 'aneksWMS');
        define('DB_PORT', 5432);
        define('RENDER_PREFIX', '');
        define('RECAPTCHA_PRIVATE_KEY', '');
        define('RECAPTCHA_PUBLIC_KEY', '');

        define('STAGE', 'DEV');
        define('CONTENT_EDITABLE', true);

        //define('DEFAULT_LANG', 'GB');
        define('SERVER_NAME', 'aneks2.dev.localhost');
        break;

    case ('ratatosk');
        define('DB_USER', 'root');
        define('DB_PASS', 'rootdb');
        define('DB_HOST', 'localhost');
        define('DB', 'ragnacode');
        define('RENDER_PREFIX', '');
        define('RECAPTCHA_PRIVATE_KEY', '');
        define('RECAPTCHA_PUBLIC_KEY', '');

        define('MAIN_EMAIL_ADDRESS', 'info@ragnacode.eu');
        define('CONTACT_FORM_EMAIL_ADDRESS', 'contact-form@ragnacode.eu');

        define('DEFAULT_LANG', 'GB');
        break;

    case ('ragnacode');
        define('DB_USER', 'postgres');
        define('DB_PASS', 'r4gna$postgre%pA$$!');
        define('DB_HOST', 'localhost');
        define('DB', 'aneks');
        define('RENDER_PREFIX', '');

        define('MAIN_EMAIL_ADDRESS', 'info@ragnacode.eu');
        define('CONTACT_FORM_EMAIL_ADDRESS', 'contact-form@ragnacode.eu');

        define('DEFAULT_LANG', 'GB');

        define('SERVER_NAME', 'aneks.dev.ragnacode.eu');
        break;
    
    case ('heroku');
        define('DB_TYPE', 'postgres');
        define('DB_USER', 'tqnttxzisxsuoo');
        define('DB_PASS', 'qNqKnFPr0s5Kw-knfYfvnSw5jc');
        define('DB_HOST', 'ec2-54-195-242-93.eu-west-1.compute.amazonaws.com');
        define('DB', 'd3av89t2tmi8ri');
        define('DB_PORT', 5432);
        define('RENDER_PREFIX', '');

        define('MAIN_EMAIL_ADDRESS', 'info@ragnacode.eu');
        define('CONTACT_FORM_EMAIL_ADDRESS', 'contact-form@ragnacode.eu');

        define('DEFAULT_LANG', 'GB');

        define('SERVER_NAME', 'aneks.herokuapp.com');
        break;
    
}
