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
}
