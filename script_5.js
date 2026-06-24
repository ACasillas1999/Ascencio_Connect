
        // --- SISTEMA FÍSICO DE LA TÓMBOLA EN CANVAS ---
        const tombolaCanvas = document.getElementById('tombola-canvas');
        const tombolaCtx = tombolaCanvas.getContext('2d');
        let tombolaBalls = [];
        let tombolaAngle = 0;
        let tombolaSpeed = 0;
        let isSpinningTombola = false;
        let animationFrameId = null;
        let droppingBallObj = null;

        // Escala dinámica
        let tombolaScale = 1;
        let tombolaOffsetX = 0;
        let tombolaOffsetY = 0;

        function resizeTombolaCanvas() {
            const rect = tombolaCanvas.getBoundingClientRect();
            const dpr = window.devicePixelRatio || 1;
            
            tombolaCanvas.width = rect.width * dpr;
            tombolaCanvas.height = rect.height * dpr;
            
            const scaleX = tombolaCanvas.width / 400;
            const scaleY = tombolaCanvas.height / 350;
            tombolaScale = Math.min(scaleX, scaleY);
            
            tombolaOffsetX = (tombolaCanvas.width - (400 * tombolaScale)) / 2;
            tombolaOffsetY = (tombolaCanvas.height - (350 * tombolaScale)) / 2;
        }

        const resizeObserver = new ResizeObserver(() => {
            resizeTombolaCanvas();
        });
        resizeObserver.observe(tombolaCanvas);

        class PhysicalBall {
            constructor(name, color, face, index, displayId) {
                this.name = name;
                this.color = color;
                this.face = face;
                this.index = index;
                this.displayId = displayId;
                this.radius = 12;
                
                const rMax = 100;
                const angle = Math.random() * Math.PI * 2;
                const dist = Math.random() * rMax;
                
                this.x = 200 + Math.cos(angle) * dist;
                this.y = 175 + Math.sin(angle) * dist;
                
                this.vx = (Math.random() - 0.5) * 4;
                this.vy = (Math.random() - 0.5) * 4;
            }

            update(cageRotation, centerX, centerY, cageRadius) {
                // Fuerza de gravedad constante hacia abajo
                this.vy += 0.25;
                
                // Rotación impulsada por el tambor (si la bola toca las paredes)
                const dx = this.x - centerX;
                const dy = this.y - centerY;
                const dist = Math.sqrt(dx * dx + dy * dy);
                
                // Efecto de centrifugado suave y arrastre
                if (dist > cageRadius - this.radius - 5) {
                    const angle = Math.atan2(dy, dx);
                    this.vx += Math.cos(angle + Math.PI/2) * cageRotation * 2.5;
                    this.vy += Math.sin(angle + Math.PI/2) * cageRotation * 2.5;
                }

                // Fricción del aire
                this.vx *= 0.99;
                this.vy *= 0.99;

                this.x += this.vx;
                this.y += this.vy;

                // Colisión con la pared circular exterior
                const dx2 = this.x - centerX;
                const dy2 = this.y - centerY;
                const dist2 = Math.sqrt(dx2 * dx2 + dy2 * dy2);

                if (dist2 > cageRadius - this.radius) {
                    const angle = Math.atan2(dy2, dx2);
                    this.x = centerX + Math.cos(angle) * (cageRadius - this.radius);
                    this.y = centerY + Math.sin(angle) * (cageRadius - this.radius);
                    
                    const dotProduct = this.vx * Math.cos(angle) + this.vy * Math.sin(angle);
                    this.vx -= 1.8 * dotProduct * Math.cos(angle);
                    this.vy -= 1.8 * dotProduct * Math.sin(angle);
                }
            }

            updateDrop() {
                // Efecto 3D: La bola crece hacia la pantalla
                this.radius += (45 - this.radius) * 0.12;

                this.vy += 0.8; // Gravedad
                this.x += this.vx;
                this.y += this.vy;
                
                // Rebotar en el suelo, respetando su radio actual
                const floorY = 345 - this.radius;
                if (this.y > floorY) {
                    this.y = floorY;
                    this.vy *= -0.65; // Rebote elástico
                    this.vx *= 0.96;  // Fricción horizontal
                }
            }

            drawDrop(ctx) {
                // Sombra de piso proyectada en perspectiva 3D
                const floorY = 345 - this.radius;
                const distToFloor = Math.max(0, floorY - this.y);
                const shadowScale = Math.max(0.1, 1 - distToFloor / 180);
                
                ctx.save();
                ctx.beginPath();
                ctx.translate(this.x, 345);
                ctx.scale(1, 0.25);
                ctx.arc(0, 0, this.radius * shadowScale * 1.3, 0, Math.PI * 2);
                ctx.fillStyle = "rgba(0,0,0,0.35)";
                ctx.fill();
                ctx.restore();

                // Dibuja la bola con su escala actual
                this.draw(ctx);
            }

            draw(ctx) {
                // Sombra interna de la bola (iluminación)
                ctx.beginPath();
                ctx.arc(this.x + this.radius*0.15, this.y + this.radius*0.15, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = "rgba(0,0,0,0.15)";
                ctx.fill();

                // Esfera principal
                ctx.beginPath();
                ctx.arc(this.x, this.y, this.radius, 0, Math.PI * 2);
                ctx.fillStyle = this.color;
                ctx.fill();

                ctx.strokeStyle = "rgba(0,0,0,0.2)";
                ctx.lineWidth = this.radius * 0.12;
                ctx.stroke();

                // Brillo de luz superior izquierdo de la bola
                ctx.beginPath();
                ctx.arc(this.x - this.radius*0.15, this.y - this.radius*0.15, this.radius * 0.5, 0, Math.PI * 2);
                ctx.fillStyle = "#ffffff";
                ctx.fill();

                // Número de bola pintado en el centro, escalado con el radio
                ctx.fillStyle = "#1e293b";
                ctx.font = "bold " + Math.max(9, Math.round(this.radius * 0.75)) + "px 'Fredoka'";
                ctx.textAlign = "center";
                ctx.textBaseline = "middle";
                ctx.fillText(this.displayId, this.x - this.radius*0.15, this.y - this.radius*0.15);
            }
        }

        function initTombolaPhysics() {
            resizeTombolaCanvas();
            tombolaBalls = [];
            
            // Para evitar que el navegador colapse con la física O(n^2) de 1000+ colisiones,
            // limitamos la cantidad máxima de bolas que se dibujan físicamente dentro del tambor.
            // (Nota: 75 bolas llenan visualmente el tambor ampliado dejándoles suficiente espacio para rebotar)
            const maxVisibleBalls = 75;
            
            // Mezclamos un poco visualmente para que las bolas "visibles" sean variadas
            const shuffled = [...participants].sort(() => 0.5 - Math.random());
            const participantsToRender = shuffled.slice(0, maxVisibleBalls);

            participantsToRender.forEach((p, index) => {
                tombolaBalls.push(new PhysicalBall(p.name, p.color, p.face, index, p.display_id || (index + 1)));
            });
        }

        function resolveBallCollisions() {
            for (let i = 0; i < tombolaBalls.length; i++) {
                for (let j = i + 1; j < tombolaBalls.length; j++) {
                    const b1 = tombolaBalls[i];
                    const b2 = tombolaBalls[j];

                    const dx = b2.x - b1.x;
                    const dy = b2.y - b1.y;
                    const dist = Math.sqrt(dx * dx + dy * dy);
                    const minDist = b1.radius + b2.radius;

                    if (dist < minDist) {
                        const overlap = minDist - dist;
                        const nx = dx / (dist || 1);
                        const ny = dy / (dist || 1);

                        // Resolver interpenetración suavemente (Soft body relaxation para evitar temblores/jittering)
                        // En lugar de moverlos al 100% (0.5 cada uno), aplicamos un factor de relajación (0.15)
                        const relaxation = 0.15;
                        b1.x -= nx * overlap * relaxation;
                        b1.y -= ny * overlap * relaxation;
                        b2.x += nx * overlap * relaxation;
                        b2.y += ny * overlap * relaxation;

                        // Transferencia elástica de impulsos vectoriales
                        const kx = b1.vx - b2.vx;
                        const ky = b1.vy - b2.vy;
                        const p = 2 * (nx * kx + ny * ky) / 2;

                        b1.vx -= p * nx * 0.85;
                        b1.vy -= p * ny * 0.85;
                        b2.vx += p * nx * 0.85;
                        b2.vy += p * ny * 0.85;
                    }
                }
            }
        }

        const programLogoImg = new Image();
        programLogoImg.src = ""blade_replaced"";

        let debugTick = 0;
        function animateTombola() {
            const debugOverlay = document.getElementById('debug-overlay');
            try {
                debugTick++;
                tombolaCtx.clearRect(0, 0, tombolaCanvas.width, tombolaCanvas.height);
                
                tombolaCtx.save();
                tombolaCtx.translate(tombolaOffsetX, tombolaOffsetY);
                tombolaCtx.scale(tombolaScale, tombolaScale);

                if(debugTick % 30 === 0 && debugOverlay) {
                    debugOverlay.innerHTML = `Tick: ${debugTick}<br>W: ${tombolaCanvas.width}, H: ${tombolaCanvas.height}<br>Scale: ${tombolaScale}<br>Balls: ${tombolaBalls.length}`;
                }

            const centerX = 200;
            const centerY = 165;
            const cageRadius = 150;

            // Dibujar soporte metálico inferior
            tombolaCtx.strokeStyle = "#adb9be";
            tombolaCtx.lineWidth = 14;
            tombolaCtx.lineCap = "round";
            
            tombolaCtx.beginPath();
            tombolaCtx.moveTo(centerX - 165, centerY + 160);
            tombolaCtx.lineTo(centerX - 110, centerY);
            tombolaCtx.lineTo(centerX + 110, centerY);
            tombolaCtx.lineTo(centerX + 165, centerY + 160);
            tombolaCtx.stroke();

            tombolaCtx.beginPath();
            tombolaCtx.moveTo(centerX - 185, centerY + 160);
            tombolaCtx.lineTo(centerX + 185, centerY + 160);
            tombolaCtx.lineWidth = 8;
            tombolaCtx.stroke();

            // Lógica de aceleración/fricción al girar
            if (isSpinningTombola) {
                tombolaSpeed += 0.02;
                if (tombolaSpeed > 0.4) tombolaSpeed = 0.4;
            } else {
                tombolaSpeed *= 0.95;
            }
            tombolaAngle += tombolaSpeed;

            resolveBallCollisions();

            tombolaBalls.forEach(ball => {
                ball.update(tombolaSpeed, centerX, centerY, cageRadius);
                ball.draw(tombolaCtx);
            });

            // Dibujar esfera/rejilla del tambor giratorio
            tombolaCtx.save();
            tombolaCtx.translate(centerX, centerY);
            tombolaCtx.rotate(tombolaAngle);

            tombolaCtx.strokeStyle = "#80929a";
            tombolaCtx.lineWidth = 6;
            tombolaCtx.beginPath();
            tombolaCtx.arc(0, 0, cageRadius, 0, Math.PI * 2);
            tombolaCtx.stroke();

            // Rayos del tambor metálico
            tombolaCtx.strokeStyle = "rgba(128,146,154,0.45)";
            tombolaCtx.lineWidth = 2.5;
            const spokes = 16;
            for (let i = 0; i < spokes; i++) {
                const angle = (i * Math.PI * 2) / spokes;
                tombolaCtx.beginPath();
                tombolaCtx.moveTo(0, 0);
                tombolaCtx.lineTo(Math.cos(angle) * cageRadius, Math.sin(angle) * cageRadius);
                tombolaCtx.stroke();
            }

            // Plaqueta central del Bingo
            tombolaCtx.fillStyle = "#ffffff";
            tombolaCtx.beginPath();
            tombolaCtx.arc(0, 0, 32, 0, Math.PI * 2);
            tombolaCtx.fill();
            tombolaCtx.strokeStyle = "#00a0e9";
            tombolaCtx.lineWidth = 3;
            tombolaCtx.stroke();

            if (programLogoImg.complete && programLogoImg.naturalWidth !== 0) {
                const logoSize = 48; // Tamaño ajustado para caber en el centro de 64px de diámetro
                tombolaCtx.drawImage(programLogoImg, -logoSize/2, -logoSize/2, logoSize, logoSize);
            } else {
                tombolaCtx.fillStyle = "#5b5b5b";
                tombolaCtx.font = "bold 13px 'Fredoka'";
                tombolaCtx.textAlign = "center";
                tombolaCtx.textBaseline = "middle";
                tombolaCtx.fillText("BINGO", 0, -4);
                tombolaCtx.font = "bold 8px 'Fredoka'";
                tombolaCtx.fillStyle = "#00a0e9";
                tombolaCtx.fillText("SPORTS", 0, 8);
            }

            tombolaCtx.restore();

            // Perno central
            tombolaCtx.fillStyle = "#0087c4";
            tombolaCtx.beginPath();
            tombolaCtx.arc(centerX, centerY, 8, 0, Math.PI * 2);
            tombolaCtx.fill();

            // Manivela lateral giratoria
            tombolaCtx.save();
            tombolaCtx.translate(centerX, centerY);
            tombolaCtx.rotate(tombolaAngle);
            
            tombolaCtx.strokeStyle = "#3a4a50";
            tombolaCtx.lineWidth = 8;
            tombolaCtx.beginPath();
            tombolaCtx.moveTo(0, 0);
            tombolaCtx.lineTo(180, 0);
            tombolaCtx.lineTo(180, 45);
            tombolaCtx.stroke();

            tombolaCtx.fillStyle = "#f47c20";
            tombolaCtx.beginPath();
            tombolaCtx.arc(180, 45, 14, 0, Math.PI * 2);
            tombolaCtx.fill();
            tombolaCtx.restore();
            
            // Dibujar la bola cayendo (bola ganadora extraída con efecto 3D)
            if (droppingBallObj) {
                droppingBallObj.updateDrop();
                droppingBallObj.drawDrop(tombolaCtx);
            }

            tombolaCtx.restore(); // Restore global scale and translate

            } catch (err) {
                console.error("Tombola Animation Error:", err);
                const debugOverlay = document.getElementById('debug-overlay');
                if (debugOverlay) debugOverlay.innerHTML = `<span class="text-red-500">ERROR: ${err.message}<br>${err.stack}</span>`;
                tombolaCtx.restore();
                tombolaCtx.fillStyle = 'red';
                tombolaCtx.font = '14px Arial';
                tombolaCtx.fillText("Error: " + err.message, 10, 50);
            }

            animationFrameId = requestAnimationFrame(animateTombola);
        }

        function spinTombolaManual() {
            playSoundClick();
            if (isSpinningTombola) {
                isSpinningTombola = false;
                playSoundRoll(false);
                document.getElementById('btn-spin-tombola').innerHTML = '<i class="fa-solid fa-arrows-spin"></i><span>MEZCLAR BOLAS</span>';
            } else {
                if (participants.length === 0) {
                    showCustomToast("¡No hay participantes!", "Agrega algunos personajes en el panel de Ajustes.");
                    return;
                }
                isSpinningTombola = true;
                playSoundRoll(true);
                document.getElementById('btn-spin-tombola').innerHTML = '<i class="fa-solid fa-pause"></i><span>DETENER MEZCLA</span>';
                
                setTimeout(() => {
                    if (isSpinningTombola) {
                        isSpinningTombola = false;
                        playSoundRoll(false);
                        const btn = document.getElementById('btn-spin-tombola');
                        if (btn) btn.innerHTML = '<i class="fa-solid fa-arrows-spin"></i><span>MEZCLAR BOLAS</span>';
                    }
                }, 1800);
            }
        }
    