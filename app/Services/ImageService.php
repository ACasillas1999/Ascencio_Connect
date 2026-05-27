<?php

namespace App\Services;

use App\Models\Participante;
use App\Models\Evento;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Genera el Gafete del Participante combinando el machote del Evento, su QR y su Nombre
     */
    public function generarGafete(Participante $participante): ?string
    {
        $evento = $participante->evento;
        
        // Verificar si el evento tiene un machote configurado
        if (!$evento || !$evento->machote_gafete) {
            return null; // O usar un machote por defecto
        }

        $templatePath = storage_path('app/public/' . $evento->machote_gafete);
        if (!file_exists($templatePath)) {
            return null;
        }

        // Generar QR en almacenamiento temporal (o usar API pública por simplicidad si no tenemos librería)
        // Como alternativa moderna en Laravel sugerimos instalar "simplesoftwareio/simple-qrcode"
        // Por ahora, simularemos la ruta o confiaremos en que el código QR existe
        // Formato acordado: ID[Número]Ñ[Texto]
        $qrData = "ID{$participante->ID}Ñ{$participante->Nombre}";
        $qrFilename = storage_path('app/public/qrcodes/participante_' . $participante->ID . '.png');
        
        if (!is_dir(dirname($qrFilename))) {
            mkdir(dirname($qrFilename), 0777, true);
        }

        // Intentar generar QR con API pública (más fácil y no requiere librerías)
        $url = "https://api.qrserver.com/v1/create-qr-code/?size=900x900&data=" . urlencode($qrData);
        $qrImageContent = @file_get_contents($url);
        
        if ($qrImageContent) {
            file_put_contents($qrFilename, $qrImageContent);
        } else {
            // Fallback: Usar la librería phpqrcode del proyecto anterior
            $phpQrCodePath = 'C:\xampp\xampp\htdocs\Congreso\phpqrcode\qrlib.php';
            if (file_exists($phpQrCodePath)) {
                include_once $phpQrCodePath;
                \QRcode::png($qrData, $qrFilename, 'L', 4);
            }
        }

        $outputPath = storage_path('app/public/gafetes/Gafete_personalizado_' . $participante->ID . '.jpg');
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0777, true);
        }

        // Cargar imagen de fondo
        $image = @imagecreatefromstring(file_get_contents($templatePath));
        if (!$image) return null;

        $hexColorNombre = $evento->gafete_color_nombre ?? '#000000';
        list($r, $g, $b) = sscanf(str_pad($hexColorNombre, 7, '0'), "#%02x%02x%02x");
        $colorNombre = imagecolorallocate($image, $r ?? 0, $g ?? 0, $b ?? 0);

        $hexColorId = $evento->gafete_color_id ?? '#000000';
        list($r, $g, $b) = sscanf(str_pad($hexColorId, 7, '0'), "#%02x%02x%02x");
        $colorId = imagecolorallocate($image, $r ?? 0, $g ?? 0, $b ?? 0);

        // Selección de Fuente
        $fontName = $evento->gafete_font_family ?? 'Nexa';
        if ($fontName === 'Arial') {
            $fontPath = 'C:\Windows\Fonts\arial.ttf';
        } elseif ($fontName === 'Times New Roman') {
            $fontPath = 'C:\Windows\Fonts\times.ttf';
        } elseif ($fontName === 'Courier') {
            $fontPath = 'C:\Windows\Fonts\cour.ttf';
        } else {
            $fontPath = __DIR__ . '/../../public/fonts/nexa-book.ttf';
        }
        
        if (!file_exists($fontPath)) {
            $fontSystem = 'C:\Windows\Fonts\arial.ttf';
            if (file_exists($fontSystem)) {
                $fontPath = $fontSystem;
            }
        }

        if (file_exists($fontPath)) {
            $fontSize = $evento->gafete_font_size ?? 60;
            $areaWidth = 1000;
            $areaX = $evento->gafete_nombre_x ?? 202;
            
            $textBox = imagettfbbox($fontSize, 0, $fontPath, $participante->Nombre);
            $textWidth = abs($textBox[4] - $textBox[0]);

            if ($textWidth > $areaWidth) {
                $fontSize = ($areaWidth / $textWidth) * $fontSize; 
                $textBox = imagettfbbox($fontSize, 0, $fontPath, $participante->Nombre);
                $textWidth = abs($textBox[4] - $textBox[0]);
            }

            $x = $areaX + ($areaWidth - $textWidth) / 2;
            $y = $evento->gafete_nombre_y ?? 1050;
            imagettftext($image, $fontSize, 0, $x, $y, $colorNombre, $fontPath, $participante->Nombre);
            
            // Dibujar ID
            $idText = "ID: " . $participante->ID;
            $idFontSize = $evento->gafete_id_font_size ?? 40;
            $idX = $evento->gafete_id_x ?? 202;
            $idY = $evento->gafete_id_y ?? 1200;
            imagettftext($image, $idFontSize, 0, $idX, $idY, $colorId, $fontPath, $idText);
        } else {
            // Fallback extremo: imagestring (solo soporta tamaños 1-5)
            $fontSize = 5;
            $x = $evento->gafete_nombre_x ?? 202;
            $y = $evento->gafete_nombre_y ?? 1050;
            imagestring($image, $fontSize, $x, $y, $participante->Nombre, $colorNombre);
            
            $idX = $evento->gafete_id_x ?? 202;
            $idY = $evento->gafete_id_y ?? 1200;
            imagestring($image, $fontSize, $idX, $idY, "ID: " . $participante->ID, $colorId);
        }

        // Superponer QR si se generó
        if (file_exists($qrFilename)) {
            $qrImage = imagecreatefrompng($qrFilename);
            $qrWidth = imagesx($qrImage);
            $qrHeight = imagesy($qrImage);
            
            $templateWidth = imagesx($image);
            $qrNewWidth = (int)($templateWidth * 0.25); 
            $qrNewHeight = $qrNewWidth; 
            $qrResized = imagecreatetruecolor($qrNewWidth, $qrNewHeight);
            imagecopyresampled($qrResized, $qrImage, 0, 0, 0, 0, $qrNewWidth, $qrNewHeight, $qrWidth, $qrHeight);

            $qrX = $evento->gafete_qr_x ?? 1755;
            $qrY = $evento->gafete_qr_y ?? 280;
            imagecopy($image, $qrResized, $qrX, $qrY, 0, 0, $qrNewWidth, $qrNewHeight);
            
            imagedestroy($qrImage);
            imagedestroy($qrResized);
        } else {
            // Debug: Dibujar un cuadro rojo donde debería ir el QR
            $qrX = $evento->gafete_qr_x ?? 1755;
            $qrY = $evento->gafete_qr_y ?? 280;
            $qrNewWidth = 900;
            $qrNewHeight = 900;
            
            $colorRojo = imagecolorallocate($image, 255, 0, 0);
            imagerectangle($image, $qrX, $qrY, $qrX + $qrNewWidth, $qrY + $qrNewHeight, $colorRojo);
            
            $fontPath = __DIR__ . '/../../public/fonts/nexa-book.ttf';
            if (file_exists($fontPath)) {
                imagettftext($image, 30, 0, $qrX + 50, $qrY + 450, $colorRojo, $fontPath, "QR No Generado");
            }
        }

        imagejpeg($image, $outputPath, 100);
        imagedestroy($image);

        // Guardar la ruta en la base de datos
        $participante->update([
            'Ruta_Gafete' => 'gafetes/Gafete_personalizado_' . $participante->ID . '.jpg',
            'QR_Code' => 'qrcodes/participante_' . $participante->ID . '.png'
        ]);

        return $outputPath;
    }

    /**
     * Lógica simplificada para generar el Horario dinámico en base a las clases registradas
     */
    public function generarHorario(Participante $participante): ?string
    {
        $evento = $participante->evento;
        if (!$evento || !$evento->machote_horario) {
            return null;
        }

        $templatePath = storage_path('app/public/' . $evento->machote_horario);
        if (!file_exists($templatePath)) {
            return null;
        }

        $clases = $participante->clases()->with('agenda')->get();
        if ($clases->isEmpty()) {
            return null;
        }

        $outputPath = storage_path('app/public/horarios/Horario_' . $participante->ID . '_' . time() . '.png');
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0777, true);
        }

        // Cargar machote
        $im = @imagecreatefromstring(file_get_contents($templatePath));
        if (!$im) return null;

        $colorNegro = imagecolorallocate($im, 0, 0, 0);
        // $fontPath = public_path('fonts/roboto.ttf'); // Placeholder
        $fontPath = __DIR__ . '/../../public/fonts/roboto.ttf';

        if (file_exists($fontPath)) {
            // Escribir nombre del participante
            imagettftext($im, 32, 0, 60, 480, $colorNegro, $fontPath, $participante->Nombre);
            
            // Escribir nombre del evento
            imagettftext($im, 20, 0, 60, 514, $colorNegro, $fontPath, $evento->name_evento);

            // TODO: Integrar aquí la lógica compleja de cuadrículas (columnas, filas, text wrap) del archivo original.
            // Para mantener este servicio limpio, se recomienda usar una librería como Intervention Image
            // o adaptar el renderizado por cuadrícula en base a los días de la agenda.
        }

        imagepng($im, $outputPath, 9);
        imagedestroy($im);

        // Guardar ruta
        $publicPath = 'horarios/Horario_' . $participante->ID . '_' . time() . '.png';
        $participante->update(['Ruta_Horario' => $publicPath]);

        return $outputPath;
    }

    /**
     * Genera un Gafete de Prueba para el Evento
     */
    public function generarMockGafete(Evento $evento): ?string
    {
        if (!$evento->machote_gafete) {
            return null;
        }

        $templatePath = storage_path('app/public/' . $evento->machote_gafete);
        if (!file_exists($templatePath)) {
            return null;
        }

        $outputPath = storage_path('app/public/machotes/mock_gafete_' . $evento->ID . '.jpg');
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0777, true);
        }

        // Cargar imagen de fondo
        $image = @imagecreatefromstring(file_get_contents($templatePath));
        if (!$image) return null;

        $hexColorNombre = $evento->gafete_color_nombre ?? '#000000';
        list($r, $g, $b) = sscanf(str_pad($hexColorNombre, 7, '0'), "#%02x%02x%02x");
        $colorNombre = imagecolorallocate($image, $r ?? 0, $g ?? 0, $b ?? 0);

        $hexColorId = $evento->gafete_color_id ?? '#000000';
        list($r, $g, $b) = sscanf(str_pad($hexColorId, 7, '0'), "#%02x%02x%02x");
        $colorId = imagecolorallocate($image, $r ?? 0, $g ?? 0, $b ?? 0);

        // Selección de Fuente
        $fontName = $evento->gafete_font_family ?? 'Nexa';
        if ($fontName === 'Arial') {
            $fontPath = 'C:\Windows\Fonts\arial.ttf';
        } elseif ($fontName === 'Times New Roman') {
            $fontPath = 'C:\Windows\Fonts\times.ttf';
        } elseif ($fontName === 'Courier') {
            $fontPath = 'C:\Windows\Fonts\cour.ttf';
        } else {
            $fontPath = __DIR__ . '/../../public/fonts/nexa-book.ttf';
        }
        
        $nombrePrueba = "Participante de Prueba";

        if (!file_exists($fontPath)) {
            $fontSystem = 'C:\Windows\Fonts\arial.ttf';
            if (file_exists($fontSystem)) {
                $fontPath = $fontSystem;
            }
        }

        if (file_exists($fontPath)) {
            $fontSize = $evento->gafete_font_size ?? 60;
            $areaWidth = 1000;
            $areaX = $evento->gafete_nombre_x ?? 202;
            
            $textBox = imagettfbbox($fontSize, 0, $fontPath, $nombrePrueba);
            $textWidth = abs($textBox[4] - $textBox[0]);

            if ($textWidth > $areaWidth) {
                $fontSize = ($areaWidth / $textWidth) * $fontSize; 
                $textBox = imagettfbbox($fontSize, 0, $fontPath, $nombrePrueba);
                $textWidth = abs($textBox[4] - $textBox[0]);
            }

            $x = $areaX + ($areaWidth - $textWidth) / 2;
            $y = $evento->gafete_nombre_y ?? 1050;
            imagettftext($image, $fontSize, 0, $x, $y, $colorNombre, $fontPath, $nombrePrueba);
            
            // Dibujar ID
            $idText = "ID: 12345";
            $idFontSize = $evento->gafete_id_font_size ?? 40;
            $idX = $evento->gafete_id_x ?? 202;
            $idY = $evento->gafete_id_y ?? 1200;
            imagettftext($image, $idFontSize, 0, $idX, $idY, $colorId, $fontPath, $idText);
        } else {
            // Fallback extremo: imagestring (solo soporta tamaños 1-5)
            $fontSize = 5;
            $x = $evento->gafete_nombre_x ?? 202;
            $y = $evento->gafete_nombre_y ?? 1050;
            imagestring($image, $fontSize, $x, $y, $nombrePrueba, $colorNombre);
            
            $idX = $evento->gafete_id_x ?? 202;
            $idY = $evento->gafete_id_y ?? 1200;
            imagestring($image, $fontSize, $idX, $idY, "ID: 12345", $colorId);
        }

        // Generar QR de prueba
        $qrData = "ID0000ÑNombrePrueba";
        $qrFilename = storage_path('app/public/qrcodes/mock_qr.png');
        
        // Intentar generar QR con API pública
        $url = "https://api.qrserver.com/v1/create-qr-code/?size=900x900&data=" . urlencode($qrData);
        $qrImageContent = @file_get_contents($url);
        
        if ($qrImageContent) {
            file_put_contents($qrFilename, $qrImageContent);
        } else {
            // Fallback: Usar la librería phpqrcode del proyecto anterior
            $phpQrCodePath = 'C:\xampp\xampp\htdocs\Congreso\phpqrcode\qrlib.php';
            if (file_exists($phpQrCodePath)) {
                include_once $phpQrCodePath;
                \QRcode::png($qrData, $qrFilename, 'L', 4);
            }
        }

        // Superponer QR si se generó
        if (file_exists($qrFilename)) {
            $qrImage = imagecreatefrompng($qrFilename);
            $qrWidth = imagesx($qrImage);
            $qrHeight = imagesy($qrImage);
            
            $templateWidth = imagesx($image);
            $qrNewWidth = (int)($templateWidth * 0.25); 
            $qrNewHeight = $qrNewWidth; 
            $qrResized = imagecreatetruecolor($qrNewWidth, $qrNewHeight);
            imagecopyresampled($qrResized, $qrImage, 0, 0, 0, 0, $qrNewWidth, $qrNewHeight, $qrWidth, $qrHeight);

            $qrX = $evento->gafete_qr_x ?? 1755;
            $qrY = $evento->gafete_qr_y ?? 280;
            imagecopy($image, $qrResized, $qrX, $qrY, 0, 0, $qrNewWidth, $qrNewHeight);
            
            imagedestroy($qrImage);
            imagedestroy($qrResized);
        }

        imagejpeg($image, $outputPath, 100);
        imagedestroy($image);

        return 'machotes/mock_gafete_' . $evento->ID . '.jpg';
    }
}
