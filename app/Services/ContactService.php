<?php
/**
 * ContactService — Email submission handler (fallback for EmailJS)
 */
class ContactService
{
    /**
     * Process contact form submission
     *
     * @param array $data Form data
     * @return array ['success' => bool, 'message' => string]
     */
    public static function submit($data)
    {
        // Validate
        $errors = self::validate($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        // Save to database
        $db = Database::getInstance();
        $db->insert('contact_submissions', [
            'name' => sanitize($data['name']),
            'email' => sanitize($data['email']),
            'phone' => sanitize($data['phone'] ?? ''),
            'service' => sanitize($data['service'] ?? ''),
            'budget' => sanitize($data['budget'] ?? ''),
            'timeline' => sanitize($data['timeline'] ?? ''),
            'message' => sanitize($data['message']),
            'submitted_at' => date('Y-m-d H:i:s'),
        ]);

        // Try to send email (PHP mail as fallback)
        $sent = self::sendEmail($data);

        if ($sent) {
            return ['success' => true, 'message' => 'Message sent successfully!'];
        } else {
            return [
                'success' => true,
                'message' => 'Message received! We will get back to you soon.',
            ];
        }
    }

    /**
     * Validate form data
     */
    private static function validate($data)
    {
        $errors = [];

        if (empty($data['name']) || strlen(trim($data['name'])) < 2) {
            $errors['name'] = 'Name is required (min 2 characters)';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Valid email is required';
        }

        if (empty($data['phone']) || strlen(trim($data['phone'])) < 5) {
            $errors['phone'] = 'Phone number is required';
        }

        if (empty($data['service'])) {
            $errors['service'] = 'Please select a service';
        }

        if (empty($data['message']) || strlen(trim($data['message'])) < 10) {
            $errors['message'] = 'Message is required (min 10 characters)';
        }

        return $errors;
    }

    /**
     * Send email via PHP mail()
     */
    private static function sendEmail($data)
    {
        $to = config('email');
        $subject = "[Portfolio Contact] " . $data['name'] . " — " . ($data['service'] ?? 'General');

        $body = "Name: {$data['name']}\n";
        $body .= "Email: {$data['email']}\n";
        $body .= "Phone: " . ($data['phone'] ?? 'N/A') . "\n";
        $body .= "Service: " . ($data['service'] ?? 'N/A') . "\n";
        $body .= "Budget: " . ($data['budget'] ?? 'N/A') . "\n";
        $body .= "Timeline: " . ($data['timeline'] ?? 'N/A') . "\n\n";
        $body .= "Message:\n{$data['message']}\n";

        $headers = "From: {$data['email']}\r\n";
        $headers .= "Reply-To: {$data['email']}\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion();

        return @mail($to, $subject, $body, $headers);
    }
}
