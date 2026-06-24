
        // --- EXTRACCIÓN Y REVELACIÓN DEL GANADOR ---
        function drawBall() {
            playSoundClick();
            if (participants.length === 0) {
                showCustomToast("¡No hay bolas!", "Introduce participantes para jugar.");
                return;
            }

            const drawBtn = document.getElementById('btn-draw-ball');
            drawBtn.disabled = true;
            drawBtn.classList.add('opacity-50');

            // Determinar el premio antes de empezar
            let winningPrizeName = "¡Premio Sorpresa!";
            let prizeIndex = -1;
            if (prizes.length > 0) {
                prizeIndex = Math.floor(Math.random() * prizes.length);
                winningPrizeName = prizes[prizeIndex].name;
            }

            // Lanzar el anuncio de premio
            showPrizeAnnouncement(winningPrizeName, () => {
                executeDrawSequence(winningPrizeName, prizeIndex);
            });
        }

        function showPrizeAnnouncement(prizeName, callback) {
            const modal = document.getElementById('prize-announcement-modal');
            const banner = document.getElementById('prize-announcement-banner');
            document.getElementById('announcement-prize-name').textContent = prizeName;
            
            modal.classList.remove('hidden');
            
            // Subida de suspenso tipo notificación
            setTimeout(() => {
                playSoundClick();
                banner.classList.remove('scale-y-0', 'opacity-0');
                banner.classList.add('scale-y-100', 'opacity-100');
            }, 50);

            // Mantener en pantalla y luego ocultar para empezar el sorteo
            setTimeout(() => {
                banner.classList.remove('scale-y-100', 'opacity-100');
                banner.classList.add('scale-y-0', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    callback();
                }, 400); // tiempo de salida
            }, 2500); // 2.5 segundos de visualización
        }

        function executeDrawSequence(winningPrizeName, prizeIndex) {
            isSpinningTombola = true;
            playSoundRoll(true);

            setTimeout(() => {
                isSpinningTombola = false;
                playSoundRoll(false);

                const winnerIndex = Math.floor(Math.random() * participants.length);
                const winnerMii = participants[winnerIndex];

                // Asignar el ganador al premio
                if (prizeIndex !== -1) {
                    prizes[prizeIndex].winner = winnerMii.name;
                }

                // Remover el participante de la lista para que no pueda volver a salir
                participants.splice(winnerIndex, 1);

                // Instanciar la bola cayendo
                const winningBallIndex = tombolaBalls.findIndex(b => b.displayId === winnerMii.display_id);
                if (winningBallIndex !== -1) {
                    droppingBallObj = tombolaBalls[winningBallIndex];
                    tombolaBalls.splice(winningBallIndex, 1);
                } else {
                    // Si la bola no estaba renderizada por el límite de máximo de bolas, 
                    // creamos una instancia fresca de la nada solo para la animación de expulsión
                    droppingBallObj = new PhysicalBall(
                        winnerMii.name, 
                        winnerMii.color, 
                        winnerMii.face, 
                        winnerIndex, 
                        winnerMii.display_id || (winnerIndex + 1)
                    );
                }

                // Posicionarla en el centro para expulsarla hacia el frente
                droppingBallObj.x = 200;
                droppingBallObj.y = 165; // Centro del tambor
                droppingBallObj.vx = (Math.random() - 0.5) * 10; // Impulso lateral más fuerte
                droppingBallObj.vy = -12; // Salto alto simulando que es lanzada hacia afuera
                
                playSoundBallDrop();

                drawnBallsHistory.unshift({
                    name: winnerMii.name,
                    color: winnerMii.color,
                    prize: winningPrizeName
                });

                // Esperar a que termine la animación de caída antes de mostrar la celebración
                setTimeout(() => {
                    droppingBallObj = null; // Quitarla de la pantalla principal
                    const drawBtn = document.getElementById('btn-draw-ball');
                    drawBtn.disabled = false;
                    drawBtn.classList.remove('opacity-50');

                    // Iniciar la cinemática integrada de la bola abriéndose
                    triggerSphereOpeningAnimation(winnerMii, winningPrizeName, winnerIndex);
                    updateUI();
                }, 1800);
                
            }, 1800); // Tiempo girando la tómbola
        }

        // Ejecución de la cinemática detallada de la esfera física del bingo
        function triggerSphereOpeningAnimation(winnerMii, winningPrizeName, winnerIndex) {
            currentAnimWinnerMii = winnerMii;
            currentAnimWinningPrizeName = winningPrizeName;

            const wrapper = document.getElementById('sphere-wrapper');
            const topHalf = document.getElementById('sphere-top');
            const bottomHalf = document.getElementById('sphere-bottom');
            const paper = document.getElementById('drawn-paper');
            const actionBtn = document.getElementById('btn-proceed-celebration');
            const strikeBanner = document.getElementById('victory-strike-banner');
            const titleEl = document.getElementById('modal-sphere-title');

            // Resetear estados del banner de victoria horizontal
            strikeBanner.classList.add('scale-y-0', 'opacity-0');
            strikeBanner.classList.remove('scale-y-100', 'opacity-100');

            // Resetear contenedores de la esfera
            wrapper.className = "relative w-80 h-80 flex items-center justify-center mb-24";
            topHalf.className = "absolute inset-0 z-20 pointer-events-none transition-transform duration-500";
            bottomHalf.className = "absolute inset-0 z-10 pointer-events-none transition-transform duration-500";
            paper.className = "absolute w-72 bg-gradient-to-b from-amber-50 to-orange-50 border-4 border-amber-400 rounded-2xl shadow-2xl p-6 flex flex-col items-center justify-center opacity-0 transform origin-top";
            
            actionBtn.classList.add('opacity-0', 'translate-y-4');
            actionBtn.classList.remove('opacity-100', 'translate-y-0');

            titleEl.textContent = "¡BOLA SELECCIONADA!";

            // Color e índice de la bola física
            const sphereColor = winnerMii.color;
            const displayId = winnerMii.display_id || (winnerIndex + 1);
            document.getElementById('svg-sphere-top-bg').setAttribute('fill', sphereColor);
            document.getElementById('svg-sphere-bottom-bg').setAttribute('fill', sphereColor);
            document.getElementById('svg-sphere-top-num').textContent = displayId;
            document.getElementById('svg-sphere-bottom-num').textContent = displayId;

            // Cargar datos en el pergamino de papel interno
            document.getElementById('paper-winner-name').textContent = winnerMii.name;
            document.getElementById('paper-winner-num').textContent = `ID #${displayId}`;
            document.getElementById('paper-winner-prize').textContent = winningPrizeName;
            document.getElementById('paper-mii-mini').innerHTML = generateMiiSVG(winnerMii.color, 'excited');

            // Cargar datos en el banner horizontal de fondo
            document.getElementById('strike-winner-name').textContent = winnerMii.name;
            document.getElementById('strike-prize-name').textContent = winningPrizeName;

            // Desplegar modal
            document.getElementById('sphere-opening-modal').classList.remove('hidden');

            // --- SECUENCIA CRONOLÓGICA DE ANIMACIÓN ---
            
            // 1. Caída gravitatoria y rebote elástico de la bola en pantalla
            wrapper.classList.add('animate-sphere-bounce');
            playSoundBallDrop();

            // 2. Comienza a temblar ruidosamente (suspenso)
            setTimeout(() => {
                wrapper.classList.remove('animate-sphere-bounce');
                wrapper.classList.add('animate-sphere-shake');
                playSoundRumble();
            }, 750);

            // 3. ¡Se abre físicamente la cápsula!
            setTimeout(() => {
                wrapper.classList.remove('animate-sphere-shake');
                topHalf.classList.add('animate-split-top');
                bottomHalf.classList.add('animate-split-bottom');
                playSoundBallPop();
            }, 1650);

            // 4. Se desenrolla el papel de premio revelando el avatar del ganador
            setTimeout(() => {
                paper.classList.add('animate-paper-unroll');
                playSoundRustle();
            }, 1900);

            // 5. ¡EXPLOSIÓN DE FIESTA! Se despliega el banner de victoria y llueve confeti
            setTimeout(() => {
                strikeBanner.classList.remove('scale-y-0', 'opacity-0');
                strikeBanner.classList.add('scale-y-100', 'opacity-100');
                playSoundVictory();
                triggerConfettiRain();
            }, 2200);

            // 6. Aparece botón de confirmación
            setTimeout(() => {
                actionBtn.classList.remove('opacity-0', 'translate-y-4');
                actionBtn.classList.add('opacity-100', 'translate-y-0');
            }, 2900);
        }

        let confettiInterval = null;

        // Genera ráfagas físicas de confeti cayendo continuamente
        function triggerConfettiRain() {
            const confettiContainer = document.getElementById('modal-confetti');
            confettiContainer.innerHTML = '';
            
            const dropBatch = () => {
                for (let i = 0; i < 12; i++) {
                    const flake = document.createElement('div');
                    flake.className = "absolute rounded-full pointer-events-none z-0";
                    const size = Math.random() * 10 + 6;
                    const left = Math.random() * 100;
                    const top = Math.random() * 20;
                    const color = ['#ff9500', '#00a0e9', '#76c336', '#e60012', '#ffeb3b'][Math.floor(Math.random()*5)];
                    
                    flake.style.width = `${size}px`;
                    flake.style.height = `${size}px`;
                    flake.style.backgroundColor = color;
                    flake.style.left = `${left}%`;
                    flake.style.top = `-${top}px`;
                    flake.style.opacity = Math.random() * 0.5 + 0.5;
                    flake.style.transform = `rotate(${Math.random()*360}deg)`;
                    
                    const duration = Math.random() * 2.5 + 2.0;
                    flake.style.transition = `top ${duration}s linear, transform ${duration}s ease-in-out`;
                    confettiContainer.appendChild(flake);

                    // Fuerza física simulada de gravedad para la caída
                    setTimeout(() => {
                        flake.style.top = '105%';
                        flake.style.transform = `rotate(${Math.random()*720}deg) translateX(${Math.random()*100 - 50}px)`;
                    }, 50);

                    // Limpieza al terminar la caída para que no sature la memoria
                    setTimeout(() => {
                        if (flake.parentNode) flake.remove();
                    }, duration * 1000 + 100);
                }
            };

            // Disparo fuerte inicial
            for(let k=0; k<4; k++) dropBatch();
            
            // Bucle infinito de caída
            if (confettiInterval) clearInterval(confettiInterval);
            confettiInterval = setInterval(dropBatch, 400);
        }

        function closeVictoryModal() {
            playSoundClick();
            document.getElementById('sphere-opening-modal').classList.add('hidden');
            if (confettiInterval) {
                clearInterval(confettiInterval);
                confettiInterval = null;
            }
        }

        function switchView(viewId) {
            playSoundClick();
            document.getElementById('tombola-view').classList.add('hidden');
            document.getElementById('setup-view').classList.add('hidden');
            document.getElementById(viewId).classList.remove('hidden');

            if (viewId === 'tombola-view') {
                initTombolaPhysics();
            }
        }

        // --- MANEJADOR DEL PUNTERO INTERACTIVO ---
        let wiiCursorActive = true;
        const wiiPointer = document.getElementById('wii-pointer');
        const tombolaContainer = document.querySelector('.tombola-container');

        tombolaContainer.addEventListener('mousemove', (e) => {
            if (wiiCursorActive) {
                wiiPointer.style.display = 'block';
                const rect = tombolaContainer.getBoundingClientRect();
                wiiPointer.style.left = `${e.clientX - rect.left}px`;
                wiiPointer.style.top = `${e.clientY - rect.top}px`;
            }
        });

        tombolaContainer.addEventListener('mouseleave', () => {
            wiiPointer.style.display = 'none';
        });

        tombolaContainer.addEventListener('mouseover', (e) => {
            if (e.target.closest('.wii-btn') || e.target.closest('.cursor-pointer')) {
                playSoundHover();
            }
        });

        function toggleWiiCursor() {
            playSoundClick();
            wiiCursorActive = !wiiCursorActive;
            if (wiiCursorActive) {
                tombolaContainer.style.cursor = 'none';
            } else {
                tombolaContainer.style.cursor = 'auto';
                wiiPointer.style.display = 'none';
            }
        }

        function showCustomToast(title, bodyText) {
            const toast = document.createElement('div');
            toast.className = "fixed bottom-12 right-12 wii-panel p-4 max-w-sm flex flex-col gap-1 border-l-8 border-l-[#f47c20] shadow-2xl z-50 animate-bounce";
            toast.innerHTML = `
                <h4 class="font-bold text-gray-800 text-base">${title}</h4>
                <p class="text-xs text-gray-500">${bodyText}</p>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('opacity-0', 'transition-all', 'duration-500');
                setTimeout(() => toast.remove(), 500);
            }, 3000);
        }

        // --- MANEJADOR DE PANTALLA COMPLETA ---
        function toggleFullscreen() {
            playSoundClick();
            if (!document.fullscreenElement) {
                tombolaContainer.requestFullscreen().catch(err => {
                    alert(`No se pudo iniciar el modo pantalla completa: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }

        document.addEventListener('fullscreenchange', () => {
            const fsIcon = document.getElementById('fs-icon');
            const fsText = document.getElementById('fs-text');
            if (document.fullscreenElement) {
                fsIcon.classList.replace('fa-expand', 'fa-compress');
                fsText.textContent = 'Salir de Pantalla';
            } else {
                fsIcon.classList.replace('fa-compress', 'fa-expand');
                fsText.textContent = 'Pantalla Completa';
            }
        });

        // --- INICIALIZADORES AL CARGAR LA PÁGINA ---
        window.onload = function() {
            initData();

            // Reloj en tiempo real
            setInterval(() => {
                const clock = document.getElementById('wii-clock');
                const now = new Date();
                let hours = now.getHours();
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                const minutes = now.getMinutes().toString().padStart(2, '0');
                clock.innerText = `${hours}:${minutes} ${ampm}`;
            }, 1000);

            initTombolaPhysics();
            animateTombola();

            document.body.addEventListener('click', () => {
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }
            }, { once: true });
        }
    