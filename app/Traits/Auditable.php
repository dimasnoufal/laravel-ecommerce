<?php

namespace App\Traits;

use App\Enums\AuditLogAction;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    /**
     * Boot the Auditable trait for the model.
     */
    public static function bootAuditable(): void
    {
        static::created(function (Model $model) {
            static::auditRecordCreation($model);
        });

        static::updated(function (Model $model) {
            static::auditRecordUpdate($model);
        });

        static::deleted(function (Model $model) {
            static::auditRecordDeletion($model);
        });
    }

    /**
     * Audit model creation event.
     */
    protected static function auditRecordCreation(Model $model): void
    {
        $attributes = $model->getAuditableAttributes($model->getAttributes());
        $modelName = class_basename($model);
        $name = $model->getAuditableDisplayName();

        AuditLog::log(
            AuditLogAction::CREATE,
            "Menambahkan {$modelName}: '{$name}'",
            $model,
            [
                'action' => 'CREATE',
                'attributes' => $attributes,
            ]
        );
    }

    /**
     * Audit model update event with structured diff.
     */
    protected static function auditRecordUpdate(Model $model): void
    {
        $dirty = $model->getDirty();
        $ignored = $model->getAuditableIgnoredColumns();

        $diff = [];
        foreach ($dirty as $key => $newValue) {
            if (in_array($key, $ignored, true)) {
                continue;
            }

            $oldValue = $model->getOriginal($key);

            // Ignore if values are equivalent or both null/empty string
            if ($oldValue === $newValue || ($oldValue === null && $newValue === '')) {
                continue;
            }

            $diff[$key] = [
                'old' => $oldValue,
                'new' => $newValue,
            ];
        }

        if (empty($diff)) {
            return;
        }

        $modelName = class_basename($model);
        $name = $model->getAuditableDisplayName();
        $fieldsCount = count($diff);

        AuditLog::log(
            AuditLogAction::UPDATE,
            "Memperbarui {$modelName}: '{$name}' ({$fieldsCount} atribut diubah)",
            $model,
            [
                'action' => 'UPDATE',
                'changes' => $diff,
            ]
        );
    }

    /**
     * Audit model deletion event.
     */
    protected static function auditRecordDeletion(Model $model): void
    {
        $modelName = class_basename($model);
        $name = $model->getAuditableDisplayName();

        AuditLog::log(
            AuditLogAction::DELETE,
            "Menghapus {$modelName}: '{$name}'",
            $model,
            [
                'action' => 'DELETE',
                'attributes' => $model->getAuditableAttributes($model->getOriginal()),
            ]
        );
    }

    /**
     * Get a human-readable display name for this model instance.
     */
    public function getAuditableDisplayName(): string
    {
        return $this->name 
            ?? $this->title 
            ?? $this->sku 
            ?? $this->order_number 
            ?? $this->tracking_number 
            ?? "#{$this->id}";
    }

    /**
     * List of columns to ignore from audit diff.
     */
    public function getAuditableIgnoredColumns(): array
    {
        return [
            'updated_at',
            'created_at',
            'deleted_at',
            'remember_token',
            'password',
            'two_factor_secret',
            'two_factor_recovery_codes',
        ];
    }

    /**
     * Sanitize attributes removing sensitive fields.
     */
    public function getAuditableAttributes(array $attributes): array
    {
        $ignored = $this->getAuditableIgnoredColumns();
        foreach ($ignored as $key) {
            unset($attributes[$key]);
        }
        return $attributes;
    }
}
