<?php

declare(strict_types=1);

final class UserDeletionPolicy
{
    private const ACTIVITY_RELATIONS = [
        ['table' => 'audit_logs', 'column' => 'user_id'],
        ['table' => 'auth_tokens', 'column' => 'user_id'],
        ['table' => 'trial_credential_deliveries', 'column' => 'user_id'],
        ['table' => 'demo_users', 'column' => 'user_id'],
        ['table' => 'lead_notes', 'column' => 'user_id'],
    ];

    public static function activityRelations(): array
    {
        return self::ACTIVITY_RELATIONS;
    }

    public static function relationMode(string $table, string $column, bool $nullable): string
    {
        foreach (self::ACTIVITY_RELATIONS as $relation) {
            if ($relation['table'] === $table && $relation['column'] === $column) {
                return 'delete';
            }
        }

        return $nullable ? 'detach' : 'delete';
    }
}
