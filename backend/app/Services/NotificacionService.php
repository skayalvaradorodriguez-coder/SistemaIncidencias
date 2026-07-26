<?php

namespace App\Services;

use App\Mail\IncidenciaEstadoMail;
use App\Models\Incidencia;
use App\Models\Notificacion;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Centraliza la creación de notificaciones del sistema.
 *
 * Se dispara ante los eventos definidos en el alcance del proyecto:
 * registro de incidencias, cambios de estado, asignación de responsables
 * y nuevos comentarios.
 */
class NotificacionService
{
    /**
     * Crea una notificación para un único usuario.
     */
    public function crear(int $usuarioId, string $titulo, string $mensaje, ?int $incidenciaId = null): Notificacion
    {
        return Notificacion::create([
            'usuario_id' => $usuarioId,
            'incidencia_id' => $incidenciaId,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'leida' => false,
        ]);
    }

    /**
     * Crea la misma notificación para varios usuarios, evitando duplicados
     * y excluyendo opcionalmente al usuario que generó el evento.
     */
    public function crearParaVarios(array $usuarioIds, string $titulo, string $mensaje, ?int $incidenciaId = null, ?int $excluirUsuarioId = null): void
    {
        $destinatarios = collect($usuarioIds)
            ->filter()
            ->unique()
            ->when($excluirUsuarioId, fn ($col) => $col->reject(fn ($id) => (int) $id === (int) $excluirUsuarioId));

        foreach ($destinatarios as $usuarioId) {
            $this->crear($usuarioId, $titulo, $mensaje, $incidenciaId);
        }
    }

    /**
     * Devuelve el conjunto de usuarios interesados en una incidencia:
     * quien la reportó y todos los usuarios asignados a ella.
     */
    public function interesados(Incidencia $incidencia): array
    {
        $ids = $incidencia->asignaciones()->pluck('usuario_id')->all();
        $ids[] = $incidencia->usuario_id;

        return array_values(array_unique($ids));
    }

    /**
     * Notifica a administradores y responsables activos cuando se
     * registra una nueva incidencia en el sistema.
     */
    public function notificarNuevaIncidencia(Incidencia $incidencia, int $usuarioCreaId): void
    {
        $gestores = User::whereHas('rol', fn ($q) => $q->whereIn('nombre', ['Administrador', 'Responsable']))
            ->where('activo', true)
            ->pluck('id')
            ->all();

        $this->crearParaVarios(
            $gestores,
            'Nueva incidencia #' . $incidencia->id,
            "Se registró la incidencia \"{$incidencia->titulo}\" con prioridad {$incidencia->prioridad}.",
            $incidencia->id,
            $usuarioCreaId
        );
    }

    /**
     * Envía al ciudadano el correo de confirmación de que su incidencia
     * fue registrada correctamente (estado inicial "Pendiente").
     */
    public function notificarRegistroPorCorreo(Incidencia $incidencia): void
    {
        $this->enviarCorreoEstado($incidencia, null, $incidencia->estado->nombre ?? 'Pendiente');
    }

    public function notificarCambioEstado(Incidencia $incidencia, string $estadoAnteriorNombre, string $estadoNuevoNombre, int $usuarioQueCambiaId, ?string $observacion = null): void
    {
        $this->crearParaVarios(
            $this->interesados($incidencia),
            'Cambio de estado en incidencia #' . $incidencia->id,
            "La incidencia \"{$incidencia->titulo}\" cambió de \"{$estadoAnteriorNombre}\" a \"{$estadoNuevoNombre}\".",
            $incidencia->id,
            $usuarioQueCambiaId
        );

        $this->enviarCorreoEstado($incidencia, $estadoAnteriorNombre, $estadoNuevoNombre, $observacion);
    }

    public function notificarNuevaAsignacion(Incidencia $incidencia, int $usuarioAsignadoId, string $rol, int $usuarioQueAsignaId): void
    {
        $this->crear(
            $usuarioAsignadoId,
            'Nueva asignación en incidencia #' . $incidencia->id,
            "Fuiste asignado como {$rol} en la incidencia \"{$incidencia->titulo}\"."
            ,
            $incidencia->id
        );
    }

    public function notificarNuevoComentario(Incidencia $incidencia, int $usuarioComentaId): void
    {
        $this->crearParaVarios(
            $this->interesados($incidencia),
            'Nuevo comentario en incidencia #' . $incidencia->id,
            "Se agregó un nuevo comentario en la incidencia \"{$incidencia->titulo}\".",
            $incidencia->id,
            $usuarioComentaId
        );
    }

    /**
     * Envía al ciudadano que reportó la incidencia un correo con el
     * nuevo estado. Se usa tanto para el registro inicial (estado
     * anterior nulo) como para cada transición posterior.
     *
     * Nunca lanza una excepción hacia afuera: un problema de correo
     * (SMTP caído, credenciales vencidas, etc.) no debe impedir que
     * el equipo de gestión pueda seguir cambiando estados. El error,
     * si ocurre, queda registrado en storage/logs para diagnóstico.
     */
    private function enviarCorreoEstado(Incidencia $incidencia, ?string $estadoAnteriorNombre, string $estadoNuevoNombre, ?string $observacion = null): void
    {
        $incidencia->loadMissing('usuario', 'tipo', 'ciudad');

        $destinatario = $incidencia->usuario;

        if (!$destinatario || !$destinatario->email) {
            return;
        }

        try {
            Mail::to($destinatario->email)->send(
                new IncidenciaEstadoMail($incidencia, $estadoAnteriorNombre, $estadoNuevoNombre, $observacion)
            );
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el correo de estado de la incidencia #' . $incidencia->id, [
                'error' => $e->getMessage(),
            ]);
        }
    }
}