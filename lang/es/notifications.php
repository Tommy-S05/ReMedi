<?php

declare(strict_types=1);

return [
    'medication_reminder' => [
        'email' => [
            'subject' => 'Recordatorio de medicamento: :medication',
            'greeting' => 'Hola :name,',
            'line_1' => 'Es hora de tomar tu :medication (:dosage) a la :time.',
            'action' => 'Ver mis medicamentos',
            'thank_you' => '¡Gracias por utilizar ReMedi!',
            'message' => 'Es hora de tomar tu :medication (:dosage) a la :time.',
        ],
    ],
    'resource_shared' => [
        'email' => [
            'subject' => ':ownerName te ha invitado a ver un recurso en ReMedi',
            'greeting' => '¡Hola!',
            'line_1' => ":ownerName quiere compartir un(a) **:resourceType** contigo llamado(a) **':resourceName'** en ReMedi.",
            'line_2' => 'Al aceptar, podrás ver los detalles y el progreso de este tratamiento para ayudar en su seguimiento.',
            'action' => 'Ver y Aceptar Invitación',
            'line_3' => 'Si no esperabas esta invitación, puedes ignorar este correo electrónico de forma segura.',
        ],
    ],
    'share_accepted' => [
        'email' => [
            'subject' => ':recipientName ha aceptado tu recurso compartido',
            'greeting' => 'Hola :name,',
            'line_1' => "¡Buenas noticias! **:recipientName** ha aceptado el acceso a tu **:resourceType** llamado(a) **':resourceName'**.",
            'line_2' => 'Ahora pueden ver y hacer seguimiento de este recurso.',
            'action' => 'Ver Recurso Compartido',
            'line_3' => 'Puedes revocar el acceso en cualquier momento desde la página de Recursos Compartidos.',
        ],
    ],
];
