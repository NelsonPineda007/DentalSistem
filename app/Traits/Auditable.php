<?php

namespace App\Traits;

use App\Models\LogSistema;
use Illuminate\Support\Facades\Auth;

trait Auditable
{
    /**
     * Se ejecuta automáticamente cuando un modelo usa este Trait.
     */
    public static function bootAuditable()
    {
        // Vigila cuando se CREA un registro
        static::created(function ($model) {
            self::registrarLogAuditoria('CREAR', $model);
        });

        // Vigila cuando se ACTUALIZA un registro
        static::updated(function ($model) {
            self::registrarLogAuditoria('ACTUALIZAR', $model);
        });

        // Vigila cuando se ELIMINA un registro
        static::deleted(function ($model) {
            self::registrarLogAuditoria('ELIMINAR', $model);
        });
    }

    /**
     * Procesa y guarda el log en la base de datos.
     */
    protected static function registrarLogAuditoria($accion, $model)
    {
        $valoresAnteriores = null;
        $valoresNuevos = null;

        if ($accion === 'CREAR') {
            $valoresNuevos = $model->getAttributes();
        } elseif ($accion === 'ACTUALIZAR') {
            $valoresAnteriores = array_intersect_key($model->getOriginal(), $model->getChanges());
            $valoresNuevos = $model->getChanges();
        } elseif ($accion === 'ELIMINAR') {
            $valoresAnteriores = $model->getOriginal();
        }

        LogSistema::create([
            'usuario_id'         => Auth::check() ? Auth::id() : null,
            'accion'             => $accion,
            'tabla_afectada'     => $model->getTable(),
            'registro_id'        => $model->getKey(),
            'valores_anteriores' => $valoresAnteriores,
            'valores_nuevos'     => $valoresNuevos,
            'ip_address'         => request()->ip(),
            'user_agent'         => request()->userAgent(),
        ]);
    }
}