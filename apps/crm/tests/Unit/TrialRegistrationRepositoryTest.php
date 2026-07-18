<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TrialRegistrationRepositoryTest extends TestCase
{
    public function testValidTrialPayloadHasNoValidationErrors(): void
    {
        self::assertSame([], TrialRegistrationRepository::validationErrors([
            'nombre' => 'Ana Martín',
            'empresa' => 'Centro Norte',
            'email' => 'ana@example.com',
            'acepta_rgpd' => '1',
        ]));
    }

    public function testTrialPayloadRequiresIdentityCompanyEmailAndConsent(): void
    {
        $errors = TrialRegistrationRepository::validationErrors([
            'nombre' => '',
            'empresa' => '',
            'email' => 'correo-invalido',
            'acepta_rgpd' => '',
        ]);

        self::assertCount(4, $errors);
        self::assertContains('Indica tu nombre.', $errors);
        self::assertContains('Indica el nombre de tu gimnasio.', $errors);
        self::assertContains('Indica un email válido.', $errors);
        self::assertContains('Debes aceptar la política de privacidad.', $errors);
    }

    public function testHoneypotIsAcceptedWithoutProvisioningAnything(): void
    {
        self::assertSame(
            ['success' => true, 'message' => 'Revisa tu correo para continuar.'],
            TrialRegistrationRepository::request(['website' => 'https://spam.example'])
        );
    }

    public function testProvisioningCreatesLinkedCustomerCompanyAndFourteenDayTrial(): void
    {
        $data = TrialRegistrationRepository::provisioningData([
            'name' => 'Ana Martín',
            'company_name' => 'Centro Norte',
            'email' => 'ana@example.com',
            'delivery_email' => 'ana.real@gmail.com',
        ], 'client_trial_1', 'temporary-secret');

        self::assertSame('client_trial_1', $data['client_id']);
        self::assertSame('Centro Norte', $data['name']);
        self::assertSame('ana.real@gmail.com', $data['contact_email']);
        self::assertSame('TRIAL', $data['plan']);
        self::assertSame('TRIAL', $data['status']);
        self::assertSame('TRIAL', $data['payment_status']);
        self::assertSame('0', $data['monthly_price']);
        self::assertSame('14', $data['trial_days']);
        self::assertSame('1', $data['create_tenant']);
        self::assertSame('Ana Martín', $data['admin_name']);
        self::assertSame('temporary-secret', $data['admin_password']);
    }

    public function testGeneratedInitialPasswordIsStrongAndReadable(): void
    {
        $password = TrialRegistrationRepository::generateInitialPassword();

        self::assertMatchesRegularExpression('/^Mb-[a-f0-9]{6}-[a-f0-9]{6}-[a-f0-9]{6}$/', $password);
        self::assertGreaterThanOrEqual(20, strlen($password));
    }

    public function testInterruptedActivationStatesCanBeRetriedWithoutDuplicatingTheAccount(): void
    {
        foreach (['PENDING', 'PROVISION_FAILED', 'PROVISIONED', 'EMAIL_FAILED'] as $status) {
            self::assertTrue(TrialRegistrationRepository::activationStatusCanBeRetried($status));
        }

        self::assertFalse(TrialRegistrationRepository::activationStatusCanBeRetried('ACTIVATING'));
        self::assertFalse(TrialRegistrationRepository::activationStatusCanBeRetried('ACTIVATED'));
    }

    public function testTrialRateLimitIsDisabledByDefaultAndCanBeEnabled(): void
    {
        $previous = getenv('TRIAL_RATE_LIMIT_ENABLED');
        putenv('TRIAL_RATE_LIMIT_ENABLED');

        try {
            self::assertFalse(TrialRegistrationRepository::trialRateLimitEnabled());
            putenv('TRIAL_RATE_LIMIT_ENABLED=true');
            self::assertTrue(TrialRegistrationRepository::trialRateLimitEnabled());
        } finally {
            $previous === false
                ? putenv('TRIAL_RATE_LIMIT_ENABLED')
                : putenv('TRIAL_RATE_LIMIT_ENABLED=' . $previous);
        }
    }

    public function testPublicTrialLinksAlwaysPointToTheCrmPath(): void
    {
        self::assertSame('https://membora.es/app', TrialRegistrationRepository::publicAppUrl());
    }

    public function testLegacyWebDomainCannotLeakIntoTrialLinks(): void
    {
        $previous = getenv('WEB_APP_URL');
        putenv('WEB_APP_URL=https://app.web.josehurtado.dev');

        try {
            self::assertSame('https://membora.es/app', TrialRegistrationRepository::publicAppUrl());
        } finally {
            $previous === false ? putenv('WEB_APP_URL') : putenv('WEB_APP_URL=' . $previous);
        }
    }
}
