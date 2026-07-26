<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background:#f1f5f9; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f1f5f9; padding: 28px 12px;">
<tr>
<td align="center">

<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background:#ffffff; border-radius:14px; overflow:hidden; box-shadow:0 6px 20px rgba(10,17,40,0.12);">

    <!-- Encabezado de marca -->
    <tr>
        <td style="background:linear-gradient(135deg, #0A1128 0%, #16233F 62%, #C9A961 145%); background-color:#0A1128; padding:26px 28px;">
            <span style="color:#ffffff; font-size:18px; font-weight:700; letter-spacing:0.2px;">
                &#128205; Sistema de Incidencias
            </span>
        </td>
    </tr>

    <!-- Insignia de estado -->
    <tr>
        <td style="padding:28px 28px 0 28px;">
            <span style="display:inline-block; background:{{ $colorEstado }}; color:#ffffff; font-size:12px; font-weight:700; letter-spacing:0.4px; text-transform:uppercase; border-radius:20px; padding:5px 14px;">
                {{ $estadoNuevo }}
            </span>
        </td>
    </tr>

    <!-- Saludo y mensaje principal -->
    <tr>
        <td style="padding:14px 28px 0 28px; color:#1f2937;">
            <p style="margin:0 0 6px 0; font-size:15px;">
                Hola {{ $incidencia->usuario->name ?? 'ciudadano' }},
            </p>

            @if($estadoAnterior === null)
                <p style="margin:0 0 16px 0; font-size:15px; line-height:1.55;">
                    Recibimos tu incidencia <strong>#{{ $incidencia->id }}</strong> y ya quedó registrada en el sistema.
                    Te avisaremos por este mismo medio cada vez que su estado cambie.
                </p>
            @elseif($estadoNuevo === 'Resuelto')
                <p style="margin:0 0 16px 0; font-size:15px; line-height:1.55;">
                    Buenas noticias: tu incidencia <strong>#{{ $incidencia->id }}</strong> pasó de
                    "<strong>{{ $estadoAnterior }}</strong>" a "<strong>{{ $estadoNuevo }}</strong>". Gracias por reportarla y ayudarnos a mejorar tu comunidad.
                </p>
            @elseif($estadoNuevo === 'Rechazado')
                <p style="margin:0 0 16px 0; font-size:15px; line-height:1.55;">
                    Tu incidencia <strong>#{{ $incidencia->id }}</strong> pasó de
                    "<strong>{{ $estadoAnterior }}</strong>" a "<strong>{{ $estadoNuevo }}</strong>". Puedes ver el motivo más abajo o escribirnos un comentario si crees que fue un error.
                </p>
            @else
                <p style="margin:0 0 16px 0; font-size:15px; line-height:1.55;">
                    Tu incidencia <strong>#{{ $incidencia->id }}</strong> cambió de
                    "<strong>{{ $estadoAnterior }}</strong>" a "<strong>{{ $estadoNuevo }}</strong>".
                </p>
            @endif
        </td>
    </tr>

    <!-- Resumen de la incidencia -->
    <tr>
        <td style="padding:0 28px;">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#f8fafc; border:1px solid #e5e7eb; border-radius:10px;">
                <tr>
                    <td style="padding:16px 18px;">
                        <p style="margin:0 0 4px 0; font-size:14px; font-weight:700; color:#0A1128;">
                            {{ $incidencia->titulo }}
                        </p>
                        <p style="margin:0; font-size:12.5px; color:#6b7280; line-height:1.6;">
                            @if($incidencia->tipo) {{ $incidencia->tipo->nombre }} &nbsp;&middot;&nbsp; @endif
                            @if($incidencia->ciudad) {{ $incidencia->ciudad->nombre }} &nbsp;&middot;&nbsp; @endif
                            Prioridad: {{ $incidencia->prioridad }}
                        </p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>

    @if($observacion)
        <!-- Observación del equipo (especialmente relevante en Rechazado) -->
        <tr>
            <td style="padding:14px 28px 0 28px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#fff7ed; border-left:4px solid {{ $colorEstado }}; border-radius:6px;">
                    <tr>
                        <td style="padding:12px 16px;">
                            <p style="margin:0 0 2px 0; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.3px; color:#92400e;">
                                Comentario del equipo
                            </p>
                            <p style="margin:0; font-size:13.5px; color:#78350f; line-height:1.5;">
                                {{ $observacion }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    @endif

    <!-- Botón de acción -->
    <tr>
        <td style="padding:24px 28px 8px 28px;" align="center">
            <a href="{{ $urlIncidencia }}"
               style="display:inline-block; background:linear-gradient(to bottom, #E3CD8F, #C9A961); background-color:#C9A961; color:#0A1128; font-weight:700; font-size:14px; text-decoration:none; border-radius:8px; padding:12px 26px;">
                Ver seguimiento completo
            </a>
        </td>
    </tr>

    <!-- Nota de emergencias -->
    <tr>
        <td style="padding:18px 28px 0 28px;">
            <p style="margin:0; font-size:11.5px; color:#9ca3af; text-align:center; line-height:1.5;">
                Si esto es una emergencia con riesgo para la vida, no esperes esta plataforma:
                comunícate directamente con los servicios de emergencia de tu localidad.
            </p>
        </td>
    </tr>

    <!-- Footer -->
    <tr>
        <td style="padding:22px 28px 26px 28px; border-top:1px solid #e5e7eb; margin-top:20px;">
            <p style="margin:16px 0 0 0; font-size:11px; color:#9ca3af; text-align:center;">
                Este es un mensaje automático, por favor no respondas a este correo.<br>
                Sistema de Gestión de Incidencias Georreferenciadas
            </p>
        </td>
    </tr>

</table>

</td>
</tr>
</table>

</body>
</html>