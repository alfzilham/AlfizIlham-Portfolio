<?php
session_start();
require_once dirname(__DIR__) . '/bootstrap.php';
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'en';
if (!in_array($lang, ['en', 'id'], true)) $lang = 'en';
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Privacy Policy — Alfiz Ilham</title>
  <meta name="robots" content="noindex, nofollow" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/global.css" />
  <link rel="stylesheet" href="assets/css/components.css" />
  <link rel="stylesheet" href="assets/css/responsive.css" />
</head>
<body>
  <div class="container" style="max-width:720px;padding-top:80px;padding-bottom:80px;">
    <p class="eyebrow">Legal</p>
    <h1 style="font-size:clamp(28px,4vw,48px);font-weight:800;margin-bottom:32px;">Privacy Policy</h1>
    <p style="color:var(--color-text-muted);margin-bottom:24px;font-size:14px;">Last updated: August 2026</p>

    <div style="font-size:15px;line-height:1.7;color:var(--color-text);">
      <h2 style="font-size:1.2rem;font-weight:700;margin:28px 0 12px;">1. Information We Collect</h2>
      <p style="margin-bottom:16px;">When you visit this website, we may automatically collect certain information including your IP address, browser type, operating system, and pages visited. This data is used solely for analytics purposes to understand how visitors interact with the site.</p>
      <p style="margin-bottom:16px;">If you submit a contact form, we collect your name, email address, phone number, and any information you provide in your message. This information is used only to respond to your inquiry.</p>

      <h2 style="font-size:1.2rem;font-weight:700;margin:28px 0 12px;">2. How We Use Your Information</h2>
      <p style="margin-bottom:16px;">We use collected information to: respond to your inquiries and messages, improve the website experience, monitor site usage and performance, and detect and prevent security threats.</p>

      <h2 style="font-size:1.2rem;font-weight:700;margin:28px 0 12px;">3. Third-Party Services</h2>
      <p style="margin-bottom:16px;">This website uses the following third-party services:</p>
      <ul style="margin-bottom:16px;padding-left:20px;">
        <li style="margin-bottom:6px;"><strong>EmailJS</strong> — for contact form email delivery (client-side)</li>
        <li style="margin-bottom:6px;"><strong>Leaflet.js</strong> — for map display (OpenStreetMap tiles)</li>
        <li style="margin-bottom:6px;"><strong>ip-api.com</strong> — for visitor geolocation (country detection only)</li>
      </ul>
      <p style="margin-bottom:16px;">These services may collect data according to their own privacy policies.</p>

      <h2 style="font-size:1.2rem;font-weight:700;margin:28px 0 12px;">4. Cookies</h2>
      <p style="margin-bottom:16px;">This website uses only session cookies for language preference and visitor tracking. No third-party tracking cookies or advertising cookies are used.</p>

      <h2 style="font-size:1.2rem;font-weight:700;margin:28px 0 12px;">5. Data Security</h2>
      <p style="margin-bottom:16px;">We take reasonable measures to protect your personal information. However, no method of electronic transmission or storage is 100% secure.</p>

      <h2 style="font-size:1.2rem;font-weight:700;margin:28px 0 12px;">6. Your Rights</h2>
      <p style="margin-bottom:16px;">You have the right to request access to, correction of, or deletion of your personal data. To exercise these rights, please contact us at alfizilham@gmail.com.</p>

      <h2 style="font-size:1.2rem;font-weight:700;margin:28px 0 12px;">7. Changes to This Policy</h2>
      <p style="margin-bottom:16px;">We may update this Privacy Policy from time to time. Any changes will be posted on this page with an updated date.</p>

      <h2 style="font-size:1.2rem;font-weight:700;margin:28px 0 12px;">8. Contact</h2>
      <p style="margin-bottom:16px;">If you have questions about this Privacy Policy, please contact us at <a href="mailto:alfizilham@gmail.com" style="color:var(--color-text);text-decoration:underline;">alfizilham@gmail.com</a>.</p>
    </div>

    <div style="margin-top:48px;padding-top:24px;border-top:1px solid var(--color-border);">
      <a href="/" style="color:var(--color-text);text-decoration:none;font-weight:600;">&larr; Back to portfolio</a>
    </div>
  </div>
</body>
</html>
