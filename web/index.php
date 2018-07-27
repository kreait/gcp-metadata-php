<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$metadata = new \Kreait\GcpMetadata();

$result = ['is_available' => false];

if ($metadata->isAvailable()) {
    $result = [
        'is_available' => true,
        'instance' => $metadata->instance(),
        'project' => $metadata->project(),
        'service_account_email' => $metadata->instance('service-accounts/default/email'),
        'project_id' => $metadata->project('project-id'),
    ];
}

echo json_encode($result, JSON_PRETTY_PRINT);
