<?php

namespace App\Services;

use App\Models\Evento;
use App\Models\Participante;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Envia un mensaje con plantilla por WhatsApp usando Meta Cloud API
     */
    public function enviarPlantilla(Participante $participante, Evento $evento, string $token = "", ?string $headerImageUrl = null): bool
    {
        $accessToken = env("META_WA_ACCESS_TOKEN");
        $phoneNumberId = env("META_WA_PHONE_NUMBER_ID");
        
        // Nombre de la plantilla desde la BD del evento o .env
        $templateName = $evento->wa_template_name ?: env("META_WA_TEMPLATE_NAME", "conexion_ascencio_2026");
        // Idioma segun Meta Manager (Spanish MEX = es_MX)
        $languageCode = env("META_WA_LANGUAGE", "es_MX");

        // Limpiar telefono para dejar solo digitos
        $telefonoLimpio = preg_replace("/\D/", "", $participante->Telefono);
        $telefonoDestino = "52" . $telefonoLimpio;
        
        $url = "https://graph.facebook.com/v19.0/{$phoneNumberId}/messages";

        // Parametros de la plantilla:
        // {{1}} -> Nombre del participante
        // {{2}} -> Folio / ID del participante
        $folio = (string) $participante->ID;

        // Determinar la URL de la imagen del Encabezado (Header)
        // 1. $headerImageUrl parametro explicito
        // 2. META_WA_HEADER_IMAGE_URL en .env (banner fijo)
        // 3. Imagen del Gafete generado del participante
        $imageUrl = $headerImageUrl 
            ?: env("META_WA_HEADER_IMAGE_URL") 
            ?: ($participante->Ruta_Gafete ? url("storage/" . $participante->Ruta_Gafete) : null);

        $components = [];

        // Si la plantilla tiene encabezado de Imagen, lo agregamos
        if ($imageUrl) {
            $components[] = [
                "type" => "header",
                "parameters" => [
                    [
                        "type" => "image",
                        "image" => [
                            "link" => $imageUrl
                        ]
                    ]
                ]
            ];
        }

        // Componente Body: {{1}} = Nombre, {{2}} = Folio
        $components[] = [
            "type" => "body",
            "parameters" => [
                ["type" => "text", "text" => $participante->Nombre],
                ["type" => "text", "text" => $folio]
            ]
        ];

        $data = [
            "messaging_product" => "whatsapp",
            "to" => $telefonoDestino,
            "type" => "template",
            "template" => [
                "name" => $templateName,
                "language" => ["code" => $languageCode],
                "components" => $components
            ]
        ];

        $response = Http::withToken($accessToken)->post($url, $data);

        if ($response->failed()) {
            Log::error("Error al enviar WhatsApp a {$telefonoDestino}", [
                "response" => $response->json(),
                "status"   => $response->status()
            ]);
            return false;
        }

        return true;
    }
}
