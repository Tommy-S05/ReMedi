<?php

declare(strict_types=1);

namespace Tests\Unit\Enums;

use App\Enums\ShareStatusEnum;

it('has the correct case values and labels', function (ShareStatusEnum $case, string $value, string $label) {
    expect($case->value)->toBe($value);
    expect($case->label())->toBe($label);
})->with([
    'pending' => [ShareStatusEnum::PENDING, 'pending', 'Pending'],
    'accepted' => [ShareStatusEnum::ACCEPTED, 'accepted', 'Accepted'],
    'rejected' => [ShareStatusEnum::REJECTED, 'rejected', 'Rejected'],
    'revoked' => [ShareStatusEnum::REVOKED, 'revoked', 'Revoked'],
    'expired' => [ShareStatusEnum::EXPIRED, 'expired', 'Expired'],
]);

it('contains all expected cases', function () {
    $cases = ShareStatusEnum::cases();

    expect($cases)->toHaveCount(5);

    $values = array_map(fn ($case) => $case->value, $cases);

    expect($values)->toContain('pending', 'accepted', 'rejected', 'revoked', 'expired');
});

it('has the correct colors', function (ShareStatusEnum $case, string $color) {
    expect($case->color())->toBe($color);
})->with([
    'pending' => [ShareStatusEnum::PENDING, 'warning'],
    'accepted' => [ShareStatusEnum::ACCEPTED, 'success'],
    'rejected' => [ShareStatusEnum::REJECTED, 'danger'],
    'revoked' => [ShareStatusEnum::REVOKED, 'danger'],
    'expired' => [ShareStatusEnum::EXPIRED, 'danger'],
]);
