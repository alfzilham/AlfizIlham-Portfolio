<?php
/**
 * Site Configuration
 */
return [
    'name' => 'Alfiz Ilham',
    'title' => 'Alfiz Ilham — Software & AI Engineer',
    'description' => 'Full-stack development, AI workflows, design, and digital solutions that make an impact.',
    'url' => 'https://alfizilham.my.id',
    'email' => 'alfizilham@gmail.com',
    'phone' => '+62 852-1389-6460',
    'whatsapp' => 'https://wa.me/6285213896460',
    'location' => [
        'city' => 'Banda Aceh',
        'country' => 'Indonesia',
        'lat' => 5.5483,
        'lng' => 95.3238,
    ],
    'social' => [
        'instagram' => 'https://www.instagram.com/alfzilham/',
        'linkedin' => 'https://www.linkedin.com/in/alfiz-ilham093a2/',
        'github' => 'https://github.com/alfzilham',
        'tiktok' => 'https://tiktok.com/@alfzilham',
        'youtube' => 'https://www.youtube.com/@alfzilham',
        'facebook' => 'https://www.facebook.com/alfzilham',
        'threads' => 'https://www.threads.com/@alfzilham',
        'pinterest' => 'https://pin.it/3PCoHDPgR',
        'discord' => 'https://discord.com/users/1501786539976425635',
        'upwork' => 'https://www.upwork.com/freelancers/~01fd8d64bfe2a9ab88?mp_source=share',
    ],
    'emailjs' => [
        'service_id' => 'service_tdiat3m',
        'template_id' => 'template_8xhjpd2',
        'template_id_auto_reply' => 'template_xycc4zl',
        'public_key' => '2MWuXtQlMs5Z7lht_',
    ],
    // Editor mode admin password (bcrypt). Default: "admin123" — CHANGE IT:
    // php -r "echo password_hash('YOUR_NEW_PASSWORD', PASSWORD_DEFAULT);"
    'admin_password_hash' => '$2y$12$JYzRq0vKVyYhPhYyKHd0YevgrrvK.vxLlwVs7/7xX6AZ3WIpgo6KW',
    'supported_languages' => ['en', 'id'],
    'default_language' => 'en',
];
