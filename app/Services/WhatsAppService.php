<?php

namespace App\Services;

use App\Models\Evento;
use App\Models\Participante;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envía un mensaje con plantilla por WhatsApp usando Meta Cloud API
     */
    public function enviarPlantilla(Participante $participante, Evento $evento, string $token): bool
    {
        // NOTA: Estos valores idealmente deberían estar en el archivo .env
        $accessToken = env('META_WA_ACCESS_TOKEN');
        $phoneNumberId = env('META_WA_PHONE_NUMBER_ID');
        
        // El nombre de la plantilla ahora viene de la BD si está configurada, sino usa la default
        $templateName = $evento->wa_template_name ?: env('META_WA_TEMPLATE_NAME', 'ascencio_day_len_2026');

        $telefonoDestino = "52" . $participante->Telefono;
        $url = "https://graph.facebook.com/v19.0/{$phoneNumberId}/messages";

        $data = [
            "messaging_product" => "whatsapp",
            "to" => $telefonoDestino,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => ["code" => "en_US"],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            ["type" => "text", "text" => $participante->Nombre]
                        ]
                    ],
                    // Botón 0: Gafete
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "0",
                        "parameters" => [
                            ["type" => "text", "text" => $token]
                        ]
                    ],
                    // Botón 1: Horario
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "1",
                        "parameters" => [
                            ["type" => "text", "text" => $token]
                        ]
                    ]
                ]
            ]
        ];

        $response = Http::withToken($accessToken)
            ->post($url, $data);

        if ($response->failed()) {
            Log::error("Error al enviar WhatsApp a {$telefonoDestino}", [
                'response' => $response->json(),
                'status' => $response->status()
            ]);
            return false;
        }

        return true;
    }
}
