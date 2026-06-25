<?php

final class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function tenantId(): string
    {
        $tenantId = self::user()['tenant_id'] ?? null;
        if (!$tenantId) {
            $tenantId = self::fallbackTenantId();
        }

        if (!$tenantId) {
            self::logout();
            flash('No hay ningun centro configurado para este usuario.', 'error');
            redirect('login');
        }

        return $tenantId;
    }

    public static function requireUser(): array
    {
        $user = self::user();
        if (!$user) {
            redirect('login');
        }

        return $user;
    }

    public static function attempt(string $email, string $password): bool
    {
        $pdo = Database::connection();
        UserRepository::ensureAvatarColumn();
        TenantRepository::ensureSettingsColumns();
        EmpresaRepository::ensureTables();
        EmpresaRepository::ensurePlatformAdmin();
        $stmt = $pdo->prepare(
            'SELECT users.*, tenants.name AS tenant_name, tenants.primary_color AS tenant_primary_color, roles.key AS role_key
             FROM users
             LEFT JOIN tenants ON tenants.id = users.tenant_id
             INNER JOIN roles ON roles.id = users.role_id
             WHERE users.email = :email
             LIMIT 1'
        );
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if (!$user || $user['status'] !== 'ACTIVE' || !password_verify($password, $user['password_hash'])) {
            return false;
        }

        $isPlatformAdmin = in_array(strtoupper((string) $user['role_key']), ['SUPER_ADMIN', 'SUPERADMIN'], true);

        if (!$user['tenant_id'] && !$isPlatformAdmin) {
            $tenant = self::fallbackTenant();
            $user['tenant_id'] = $tenant['id'] ?? null;
            $user['tenant_name'] = $tenant['name'] ?? 'Membora CRM';
            $user['tenant_primary_color'] = $tenant['primary_color'] ?? '#0754d6';
        } elseif (!$user['tenant_id'] && $isPlatformAdmin) {
            $user['tenant_name'] = 'Membora CRM';
            $user['tenant_primary_color'] = '#0754d6';
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'tenant_id' => $user['tenant_id'],
            'tenant_name' => $user['tenant_name'],
            'tenant_primary_color' => $user['tenant_primary_color'] ?: '#0754d6',
            'name' => $user['name'],
            'email' => $user['email'],
            'avatar_path' => $user['avatar_path'] ?? null,
            'role' => $user['role_key'],
        ];

        $update = $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
        $update->execute(['id' => $user['id']]);

        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION['user']);
    }

    private static function fallbackTenantId(): ?string
    {
        $tenant = self::fallbackTenant();

        if ($tenant && isset($_SESSION['user'])) {
            $_SESSION['user']['tenant_id'] = $tenant['id'];
            $_SESSION['user']['tenant_name'] = $tenant['name'];
            $_SESSION['user']['tenant_primary_color'] = $tenant['primary_color'] ?? '#0754d6';
        }

        return $tenant['id'] ?? null;
    }

    private static function fallbackTenant(): ?array
    {
        TenantRepository::ensureSettingsColumns();
        $stmt = Database::connection()->query('SELECT id, name, primary_color FROM tenants ORDER BY created_at ASC LIMIT 1');
        $tenant = $stmt->fetch();

        return $tenant ?: null;
    }
}
