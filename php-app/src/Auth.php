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
        $stmt = $pdo->prepare(
            'SELECT users.*, tenants.name AS tenant_name, roles.key AS role_key
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

        if (!$user['tenant_id']) {
            $tenant = self::fallbackTenant();
            $user['tenant_id'] = $tenant['id'] ?? null;
            $user['tenant_name'] = $tenant['name'] ?? 'Membora CRM';
        }

        $_SESSION['user'] = [
            'id' => $user['id'],
            'tenant_id' => $user['tenant_id'],
            'tenant_name' => $user['tenant_name'],
            'name' => $user['name'],
            'email' => $user['email'],
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
        }

        return $tenant['id'] ?? null;
    }

    private static function fallbackTenant(): ?array
    {
        $stmt = Database::connection()->query('SELECT id, name FROM tenants ORDER BY created_at ASC LIMIT 1');
        $tenant = $stmt->fetch();

        return $tenant ?: null;
    }
}
