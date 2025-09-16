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
];
