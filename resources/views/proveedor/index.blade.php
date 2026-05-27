@extends('layouts.app')

@section('title', 'Escanear QR - Proveedor')
@section('page-title', 'Escanear QR')

@section('content')
<div style="max-width: 600px; margin: 0 auto;">
    
    <div class="card" style="text-align: center; margin-bottom: 20px;">
        <div class="card-header">
            <span class="card-title">Proveedor: {{ $usuario }}</span>
        </div>
        <div class="card-body">
            <p style="color: var(--text-secondary); margin-bottom: 0;">
                Estás dando <strong><span class="badge badge-gold">{{ $puntos }}</span></strong> puntos por escaneo en el evento <strong>{{ $evento_nombre }}</strong>.
            </p>
        </div>
    </div>

    <!-- LECTOR QR -->
    <div class="card">
        <div class="card-body" style="display: flex; flex-direction: column; align-items: center; gap: 16px;">
            <p style="text-align: center; color: var(--text-muted);">Apunta con la cámara al código QR del participante</p>
            
            <div id="reader" style="width: 100%; max-width: 400px; aspect-ratio: 1/1; border-radius: 12px; overflow: hidden; background: #000;"></div>
            
            <div id="resultado" style="width: 100%; padding: 12px; border-radius: 8px; background: rgba(255,255,255,0.05); text-align: center; font-weight: 500;">
                Pulsa "Abrir cámara"...
            </div>

            <div style="display: flex; gap: 10px; width: 100%; justify-content: center; flex-wrap: wrap;">
                <button id="btnStart" class="btn btn-primary" style="flex: 1; min-width: 150px;">📷 Abrir cámara</button>
                <button id="btnSwitch" class="btn btn-secondary" style="flex: 1; min-width: 150px;" disabled>🔄 Cambiar cámara</button>
            </div>
        </div>
    </div>

</div>

<!-- Librería para lectura de QR -->
<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    const html5QrCode = new Html5Qrcode("reader");
    let cams = [];
    let currentCamId = null;
    let scanning = false;

    const $res = s => document.getElementById("resultado").innerText = s;

    async function listarCamaras() {
        try {
            cams = await Html5Qrcode.getCameras();
            if (!cams.length) {
                $res("❌ No se detectó ninguna cámara.");
                return;
            }
            // Preferir cámara trasera
            const back = cams.find(c => /back|rear|environment/i.test(c.label));
            currentCamId = (back || cams[0]).id;
            document.getElementById("btnSwitch").disabled = cams.length < 2;
        } catch (e) {
            $res("❌ Error al acceder a las cámaras.");
            console.error(e);
        }
    }

    async function startScan() {
        if (scanning) return;
        scanning = true;
        try {
            if (!cams.length) await listarCamaras();

            await html5QrCode.start(
                { deviceId: { exact: currentCamId } },
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                onScanError
            );
            $res("Apunta al código QR...");
            document.getElementById("btnStart").innerText = "🛑 Detener";
            document.getElementById("btnStart").classList.remove('btn-primary');
            document.getElementById("btnStart").classList.add('btn-secondary');
        } catch (e) {
            $res(msgDeError(e));
            scanning = false;
        }
    }

    async function stopScan() {
        if (!scanning) return;
        try {
            await html5QrCode.stop();
            await html5QrCode.clear();
            scanning = false;
            $res("Cámara detenida.");
            document.getElementById("btnStart").innerText = "📷 Abrir cámara";
            document.getElementById("btnStart").classList.remove('btn-secondary');
            document.getElementById("btnStart").classList.add('btn-primary');
        } catch (e) {
            console.error(e);
        }
    }

    async function onScanSuccess(text) {
        try {
            await html5QrCode.pause(true);
        } catch(_) {}
        $res("Procesando código...");

        try {
            const r = await fetch("{{ route('proveedor.asignar-puntos') }}", {
                method: "POST",
                headers: { 
                    "Content-Type": "application/x-www-form-urlencoded",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: "codigo=" + encodeURIComponent(text)
            });
            
            const responseText = await r.text();
            $res(responseText);
            
        } catch (e) {
            $res("❌ Error enviando el código.");
            console.error(e);
        } finally {
            setTimeout(async () => {
                try { 
                    await html5QrCode.resume(); 
                    $res("Apunta al código QR..."); 
                } catch(_) {}
            }, 2000);
        }
    }

    function onScanError(err) {
        // Ignoramos errores de enfoque para no llenar la consola
    }

    async function switchCam() {
        if (cams.length < 2) return;
        try {
            const idx = cams.findIndex(c => c.id === currentCamId);
            currentCamId = cams[(idx + 1) % cams.length].id;
            await stopScan();
            startScan();
        } catch (e) {
            $res("❌ No se pudo cambiar de cámara.");
            console.error(e);
        }
    }

    function msgDeError(e) {
        const s = String(e || "");
        if (location.protocol !== "https:" && !location.hostname.match(/^(localhost|127\.0\.0\.1)$/))
            return "Necesitas abrir esta página en HTTPS para usar la cámara.";
        if (s.includes("NotAllowedError")) return "Permiso de cámara denegado. Revisa los permisos.";
        if (s.includes("NotFoundError"))  return "No se encontró cámara.";
        return "No se pudo abrir la cámara.";
    }

    document.getElementById("btnStart").addEventListener("click", () => {
        if (scanning) stopScan();
        else startScan();
    });
    document.getElementById("btnSwitch").addEventListener("click", switchCam);
</script>
@endsection
