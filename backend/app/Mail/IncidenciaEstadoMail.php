<?php

namespace App\Mail;

use App\Models\Incidencia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Correo enviado al ciudadano que reportó una incidencia cada vez que
 * esta cambia de estado (incluyendo el registro inicial en "Pendiente").
 *
 * No implementa ShouldQueue a propósito: este proyecto no tiene un
 * worker de colas corriendo en producción todavía (ver docker/render),
 * así que se envía de forma síncrona. Si en el futuro se agrega un
 * "queue:work" al supervisor, basta con añadir la interfaz ShouldQueue
 * aquí para que los envíos dejen de bloquear la respuesta HTTP.
 */
class IncidenciaEstadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Incidencia $incidencia,
        public ?string $estadoAnterior,
        public string $estadoNuevo,
        public ?string $observacion = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asuntoSegunEstado(),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.incidencia-estado',
            with: [
                'incidencia'     => $this->incidencia,
                'estadoAnterior' => $this->estadoAnterior,
                'estadoNuevo'    => $this->estadoNuevo,
                'observacion'    => $this->observacion,
                'colorEstado'    => $this->colorSegunEstado(),
                'urlIncidencia'  => rtrim(config('app.url'), '/') . '/incidencias/' . $this->incidencia->id,
            ],
        );
    }

    private function asuntoSegunEstado(): string
    {
        return match ($this->estadoNuevo) {
            'Pendiente'  => "Recibimos tu incidencia #{$this->incidencia->id}",
            'En Proceso' => "Tu incidencia #{$this->incidencia->id} ya está en proceso",
            'Resuelto'   => "Tu incidencia #{$this->incidencia->id} fue resuelta",
            'Rechazado'  => "Tu incidencia #{$this->incidencia->id} fue rechazada",
            default      => "Actualización de tu incidencia #{$this->incidencia->id}",
        };
    }

    private function colorSegunEstado(): string
    {
        return match ($this->estadoNuevo) {
            'Pendiente'  => '#C9A961',
            'En Proceso' => '#0ea5e9',
            'Resuelto'   => '#22c55e',
            'Rechazado'  => '#ef4444',
            default      => '#6c757d',
        };
    }
}