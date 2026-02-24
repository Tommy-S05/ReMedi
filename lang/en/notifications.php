<?php

declare(strict_types=1);

return [
    'medication_reminder' => [
        'email' => [
            'subject' => 'Medication Reminder: :medication',
            'greeting' => 'Hello :name,',
            'line_1' => 'It\'s time to take your :medication (:dosage) at :time.',
            'action' => 'View My Medications',
            'thank_you' => 'Thank you for using ReMedi!',
            'message' => 'Time to take your :medication (:dosage) at :time.',
        ],
    ],
    'resource_shared' => [
        'email' => [
            'subject' => ':ownerName has invited you to view a resource on ReMedi',
            'greeting' => 'Hello!',
            'line_1' => ":ownerName wants to share a **:resourceType** with you called **':resourceName'** on ReMedi.",
            'line_2' => 'By accepting, you will be able to view the details and progress of this treatment to help with its follow-up.',
            'action' => 'View and Accept Invitation',
            'line_3' => 'If you did not expect this invitation, you can safely ignore this email.',
        ],
    ],
    'share_accepted' => [
        'email' => [
            'subject' => ':recipientName has accepted your shared resource',
            'greeting' => 'Hello :name,',
            'line_1' => "Great news! **:recipientName** has accepted access to your **:resourceType** called **':resourceName'**.",
            'line_2' => 'They can now view and track this resource.',
            'action' => 'View Shared Resource',
            'line_3' => 'You can revoke access at any time from the Shared Resources page.',
        ],
    ],
];
