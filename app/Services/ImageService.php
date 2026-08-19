<?php

namespace App\Services;

use App\Models\Evento;
use App\Models\Participante;

class ImageService
{
    /**
     * Obtiene la ruta de una fuente TTF válida compatible con Linux y Windows.
     */
    private function resolveFontPath(?string $fontName): ?string
    {
        $candidates = [];

        // 1. Fuentes del proyecto en public/fonts
        $candidates[] = public_path('fonts/nexa-book.ttf');
        $candidates[] = public_path('fonts/arial.ttf');

        // 2. Fuentes de Linux (Ubuntu/Debian)
        $candidates[] = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';
        $candidates[] = '/usr/share/fonts/truetype/freefont/FreeSans.ttf';
        $candidates[] = '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf';

        // 3. Fuentes de Windows
        if ($fontName === 'Times New Roman') {
            $candidates[] = 'C:\Windows\Fonts\times.ttf';
        } elseif ($fontName === 'Courier') {
            $candidates[] = 'C:\Windows\Fonts\cour.ttf';
        } else {
            $candidates[] = 'C:\Windows\Fonts\arial.ttf';
        }

        foreach ($candidates as $path) {
            if ($path && file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Genera o carga la imagen de fondo para un gafete.
     */
    private function getBaseGafeteImage(Evento $evento)
    {
        if ($evento->machote_gafete) {
            $templatePath = storage_path('app/public/' . $evento->machote_gafete);
            if (file_exists($templatePath)) {
                $img = @imagecreatefromstring(file_get_contents($templatePath));
                if ($img) return $img;
            }
        }

        // Crear una plantilla por defecto elegante en HD (2400 x 1500) si no hay machote
        $width = 2400;
        $height = 1500;
        $img = imagecreatetruecolor($width, $height);
        $bgColor = imagecolorallocate($img, 248, 250, 252);
        $borderColor = imagecolorallocate($img, 203, 213, 225);
        $headerColor = imagecolorallocate($img, 15, 23, 42);
        $goldColor = imagecolorallocate($img, 201, 162, 39);

        imagefill($img, 0, 0, $bgColor);
        imagerectangle($img, 10, 10, $width - 11, $height - 11, $borderColor);
        imagefilledrectangle($img, 10, 10, $width - 11, 200, $headerColor);
        imagefilledrectangle($img, 10, 190, $width - 11, 200, $goldColor);

        return $img;
    }

    /**
     * Genera el Gafete personalizado para un Participante.
     */
    public function generarGafete(Participante $participante): ?string
    {
        $evento = $participante->evento;
        if (!$evento) {
            $evento = Evento::find($participante->ID_Evento);
        }

        $image = $evento ? $this->getBaseGafeteImage($evento) : imagecreatetruecolor(2400, 1500);

        $outputPath = storage_path('app/public/gafetes/Gafete_personalizado_' . $participante->ID . '.jpg');
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0777, true);
        }

        // Configuración de Colores
        $hexColorNombre = ($evento && $evento->gafete_color_nombre) ? $evento->gafete_color_nombre : '#000000';
        list($r, $g, $b) = sscanf(str_pad($hexColorNombre, 7, '0'), "#%02x%02x%02x");
        $colorNombre = imagecolorallocate($image, $r ?? 0, $g ?? 0, $b ?? 0);

        $hexColorId = ($evento && $evento->gafete_color_id) ? $evento->gafete_color_id : '#000000';
        list($r, $g, $b) = sscanf(str_pad($hexColorId, 7, '0'), "#%02x%02x%02x");
        $colorId = imagecolorallocate($image, $r ?? 0, $g ?? 0, $b ?? 0);

        $fontPath = $this->resolveFontPath($evento->gafete_font_family ?? 'Arial');
        $nombreText = $participante->Nombre ?? 'Participante';
        $idText = "ID: " . ($participante->ID ?? 0);

        $templateWidth = imagesx($image);

        if ($fontPath) {
            $fontSize = $evento->gafete_font_size ?? 60;
            $nombreX = $evento->gafete_nombre_x ?? 202;
            $nombreY = $evento->gafete_nombre_y ?? 1050;

            $areaWidth = (int)($templateWidth * 0.5);
            $textBox = imagettfbbox($fontSize, 0, $fontPath, $nombreText);
            $textWidth = abs($textBox[4] - $textBox[0]);
            $nombreFinalX = $nombreX + ($areaWidth - $textWidth) / 2;

            imagettftext($image, $fontSize, 0, $nombreFinalX, $nombreY, $colorNombre, $fontPath, $nombreText);

            $idFontSize = $evento->gafete_id_font_size ?? 40;
            $idX = $evento->gafete_id_x ?? 202;
            $idY = $evento->gafete_id_y ?? 1200;

            $idAreaWidth = (int)($templateWidth * 0.2);
            $idTextBox = imagettfbbox($idFontSize, 0, $fontPath, $idText);
            $idTextWidth = abs($idTextBox[4] - $idTextBox[0]);
            $idFinalX = $idX + ($idAreaWidth - $idTextWidth) / 2;

            imagettftext($image, $idFontSize, 0, $idFinalX, $idY, $colorId, $fontPath, $idText);
        } else {
            $fontSize = 5;
            imagestring($image, $fontSize, $evento->gafete_nombre_x ?? 202, $evento->gafete_nombre_y ?? 1050, $nombreText, $colorNombre);
            imagestring($image, $fontSize, $evento->gafete_id_x ?? 202, $evento->gafete_id_y ?? 1200, $idText, $colorId);
        }

        // Generar superposición de QR
        $qrData = "ID" . $participante->ID . "Ñ" . $participante->Nombre;
        $qrDir = storage_path('app/public/qrcodes');
        if (!is_dir($qrDir)) mkdir($qrDir, 0777, true);
        $qrFilename = $qrDir . '/participante_' . $participante->ID . '.png';

        if (!file_exists($qrFilename)) {
            $url = "https://api.qrserver.com/v1/create-qr-code/?size=900x900&data=" . urlencode($qrData);
            $qrContent = @file_get_contents($url);
            if ($qrContent) {
                file_put_contents($qrFilename, $qrContent);
            }
        }

        if (file_exists($qrFilename)) {
            $qrImg = @imagecreatefrompng($qrFilename);
            if ($qrImg) {
                $qrW = imagesx($qrImg);
                $qrH = imagesy($qrImg);

                $qrPercent = (($evento->gafete_qr_size ?? 25) / 100.0);
                $qrNewW = (int)($templateWidth * $qrPercent);
                $qrResized = imagecreatetruecolor($qrNewW, $qrNewW);
                imagecopyresampled($qrResized, $qrImg, 0, 0, 0, 0, $qrNewW, $qrNewW, $qrW, $qrH);

                $qrX = $evento->gafete_qr_x ?? 1755;
                $qrY = $evento->gafete_qr_y ?? 280;
                imagecopy($image, $qrResized, $qrX, $qrY, 0, 0, $qrNewW, $qrNewW);

                imagedestroy($qrImg);
                imagedestroy($qrResized);
            }
        }

        imagejpeg($image, $outputPath, 95);
        imagedestroy($image);

        return 'gafetes/Gafete_personalizado_' . $participante->ID . '.jpg';
    }

    /**
     * Genera un Gafete de Prueba para la Vista Previa en la Configuración del Evento.
     */
    public function generarMockGafete(Evento $evento): ?string
    {
        $image = $this->getBaseGafeteImage($evento);

        $outputPath = storage_path('app/public/machotes/mock_gafete_' . $evento->ID . '.jpg');
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0777, true);
        }

        $hexColorNombre = $evento->gafete_color_nombre ?? '#000000';
        list($r, $g, $b) = sscanf(str_pad($hexColorNombre, 7, '0'), "#%02x%02x%02x");
        $colorNombre = imagecolorallocate($image, $r ?? 0, $g ?? 0, $b ?? 0);

        $hexColorId = $evento->gafete_color_id ?? '#000000';
        list($r, $g, $b) = sscanf(str_pad($hexColorId, 7, '0'), "#%02x%02x%02x");
        $colorId = imagecolorallocate($image, $r ?? 0, $g ?? 0, $b ?? 0);

        $fontPath = $this->resolveFontPath($evento->gafete_font_family ?? 'Arial');

        $nombrePrueba = "Participante de Prueba";
        $idText = "ID: 12345";
        $templateWidth = imagesx($image);

        if ($fontPath) {
            $fontSize = $evento->gafete_font_size ?? 60;
            $nombreX = $evento->gafete_nombre_x ?? 202;
            $nombreY = $evento->gafete_nombre_y ?? 1050;

            $areaWidth = (int)($templateWidth * 0.5);
            $textBox = imagettfbbox($fontSize, 0, $fontPath, $nombrePrueba);
            $textWidth = abs($textBox[4] - $textBox[0]);
            $nombreFinalX = $nombreX + ($areaWidth - $textWidth) / 2;

            imagettftext($image, $fontSize, 0, $nombreFinalX, $nombreY, $colorNombre, $fontPath, $nombrePrueba);

            $idFontSize = $evento->gafete_id_font_size ?? 40;
            $idX = $evento->gafete_id_x ?? 202;
            $idY = $evento->gafete_id_y ?? 1200;

            $idAreaWidth = (int)($templateWidth * 0.2);
            $idTextBox = imagettfbbox($idFontSize, 0, $fontPath, $idText);
            $idTextWidth = abs($idTextBox[4] - $idTextBox[0]);
            $idFinalX = $idX + ($idAreaWidth - $idTextWidth) / 2;

            imagettftext($image, $idFontSize, 0, $idFinalX, $idY, $colorId, $fontPath, $idText);
        } else {
            $fontSize = 5;
            imagestring($image, $fontSize, $evento->gafete_nombre_x ?? 202, $evento->gafete_nombre_y ?? 1050, $nombrePrueba, $colorNombre);
            imagestring($image, $fontSize, $evento->gafete_id_x ?? 202, $evento->gafete_id_y ?? 1200, $idText, $colorId);
        }

        // QR de prueba
        $qrDir = storage_path('app/public/qrcodes');
        if (!is_dir($qrDir)) mkdir($qrDir, 0777, true);
        $qrFilename = $qrDir . '/mock_qr.png';

        if (!file_exists($qrFilename)) {
            $url = "https://api.qrserver.com/v1/create-qr-code/?size=900x900&data=ID12345";
            $qrContent = @file_get_contents($url);
            if ($qrContent) {
                file_put_contents($qrFilename, $qrContent);
            }
        }

        if (file_exists($qrFilename)) {
            $qrImg = @imagecreatefrompng($qrFilename);
            if ($qrImg) {
                $qrW = imagesx($qrImg);
                $qrH = imagesy($qrImg);

                $qrPercent = (($evento->gafete_qr_size ?? 25) / 100.0);
                $qrNewW = (int)($templateWidth * $qrPercent);
                $qrResized = imagecreatetruecolor($qrNewW, $qrNewW);
                imagecopyresampled($qrResized, $qrImg, 0, 0, 0, 0, $qrNewW, $qrNewW, $qrW, $qrH);

                $qrX = $evento->gafete_qr_x ?? 1755;
                $qrY = $evento->gafete_qr_y ?? 280;
                imagecopy($image, $qrResized, $qrX, $qrY, 0, 0, $qrNewW, $qrNewW);

                imagedestroy($qrImg);
                imagedestroy($qrResized);
            }
        }

        imagejpeg($image, $outputPath, 95);
        imagedestroy($image);

        return 'machotes/mock_gafete_' . $evento->ID . '.jpg';
    }

    /**
     * Genera el Horario del Participante.
     */
    public function generarHorario(Participante $participante): ?string
    {
        return null;
    }

    /**
     * Genera un Horario de Prueba para el Evento.
     */
    public function generarMockHorario(Evento $evento): ?string
    {
        return null;
    }
}