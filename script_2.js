
        // --- AUDIO SYNTH SYSTEM (Web Audio API) ---
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        let bgmSource = null;
        let isMusicPlaying = false;

        // Sonido de caída de la bola seleccionada
        function playSoundBallDrop() {
            try {
                const now = audioCtx.currentTime;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(180, now);
                osc.frequency.exponentialRampToValueAtTime(90, now + 0.35);
                gain.gain.setValueAtTime(0.25, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
                osc.start();
                osc.stop(now + 0.35);
            } catch(e){}
        }

        // Sonido de vibración de suspenso
        function playSoundRumble() {
            try {
                const now = audioCtx.currentTime;
                for (let i = 0; i < 10; i++) {
                    const timeOffset = i * 0.08;
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    osc.type = 'triangle';
                    osc.frequency.setValueAtTime(80 + Math.random() * 40, now + timeOffset);
                    gain.gain.setValueAtTime(0.18, now + timeOffset);
                    gain.gain.exponentialRampToValueAtTime(0.001, now + timeOffset + 0.07);
                    osc.start(now + timeOffset);
                    osc.stop(now + timeOffset + 0.07);
                }
            } catch(e){}
        }

        // Sonido de explosión pop al abrirse la bola
        function playSoundBallPop() {
            try {
                const now = audioCtx.currentTime;
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                osc.type = 'sine';
                osc.frequency.setValueAtTime(320, now);
                osc.frequency.exponentialRampToValueAtTime(950, now + 0.18);
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.18);
                osc.start();
                osc.stop(now + 0.18);

                // Brillo agudo adicional
                const ch = audioCtx.createOscillator();
                const chGain = audioCtx.createGain();
                ch.connect(chGain);
                chGain.connect(audioCtx.destination);
                ch.type = 'triangle';
                ch.frequency.setValueAtTime(600, now + 0.08);
                ch.frequency.exponentialRampToValueAtTime(1400, now + 0.45);
                chGain.gain.setValueAtTime(0, now);
                chGain.gain.linearRampToValueAtTime(0.2, now + 0.12);
                chGain.gain.exponentialRampToValueAtTime(0.001, now + 0.45);
                ch.start(now + 0.08);
                ch.stop(now + 0.45);
            } catch(e){}
        }

        // Sonido de papel estirándose/desenrollándose
        function playSoundRustle() {
            try {
                const now = audioCtx.currentTime;
                const bufferSize = audioCtx.sampleRate * 0.35;
                const buffer = audioCtx.createBuffer(1, bufferSize, audioCtx.sampleRate);
                const data = buffer.getChannelData(0);
                for (let i = 0; i < bufferSize; i++) {
                    data[i] = Math.random() * 2 - 1;
                }
                const noise = audioCtx.createBufferSource();
                noise.buffer = buffer;
                
                const filter = audioCtx.createBiquadFilter();
                filter.type = 'bandpass';
                filter.frequency.value = 1100;
                
                const gain = audioCtx.createGain();
                gain.gain.setValueAtTime(0.07, now);
                gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
                
                noise.connect(filter);
                filter.connect(gain);
                gain.connect(audioCtx.destination);
                
                noise.start(now);
                noise.stop(now + 0.35);
            } catch(e){}
        }

        // Sonido de hover sobre botones
        function playSoundHover() {
            try {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                
                osc.type = 'sine';
                osc.frequency.setValueAtTime(600, audioCtx.currentTime);
                osc.frequency.exponentialRampToValueAtTime(800, audioCtx.currentTime + 0.08);
                
                gain.gain.setValueAtTime(0.04, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.08);
                
                osc.start();
                osc.stop(audioCtx.currentTime + 0.08);
            } catch(e){}
        }

        // Sonido de clic (pop seco clásico)
        function playSoundClick() {
            try {
                const osc = audioCtx.createOscillator();
                const gain = audioCtx.createGain();
                osc.connect(gain);
                gain.connect(audioCtx.destination);
                
                osc.type = 'triangle';
                osc.frequency.setValueAtTime(440, audioCtx.currentTime);
                osc.frequency.setValueAtTime(110, audioCtx.currentTime + 0.03);
                
                gain.gain.setValueAtTime(0.2, audioCtx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.15);
                
                osc.start();
                osc.stop(audioCtx.currentTime + 0.15);
            } catch(e){}
        }

        // Sonido de suspense / redoble de tambor
        let rollInterval = null;
        function playSoundRoll(start) {
            if (start) {
                if (rollInterval) return;
                rollInterval = setInterval(() => {
                    try {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        
                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(60 + Math.random()*20, audioCtx.currentTime);
                        
                        gain.gain.setValueAtTime(0.12, audioCtx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.08);
                        
                        osc.start();
                        osc.stop(audioCtx.currentTime + 0.08);
                    } catch(e){}
                }, 50);
            } else {
                if (rollInterval) {
                    clearInterval(rollInterval);
                    rollInterval = null;
                }
            }
        }

        // Fanfarria de Victoria deportiva
        function playSoundVictory() {
            try {
                const now = audioCtx.currentTime;
                const notes = [261.63, 329.63, 392.00, 523.25, 659.25, 783.99, 1046.50];
                
                notes.forEach((freq, idx) => {
                    const osc = audioCtx.createOscillator();
                    const gain = audioCtx.createGain();
                    osc.connect(gain);
                    gain.connect(audioCtx.destination);
                    
                    osc.type = 'sine';
                    osc.frequency.setValueAtTime(freq, now + (idx * 0.12));
                    
                    gain.gain.setValueAtTime(0.15, now + (idx * 0.12));
                    gain.gain.exponentialRampToValueAtTime(0.001, now + (idx * 0.12) + 0.6);
                    
                    osc.start(now + (idx * 0.12));
                    osc.stop(now + (idx * 0.12) + 0.6);
                });

                // Ruido de aplausos sintetizado
                for (let i = 0; i < 15; i++) {
                    setTimeout(() => {
                        const osc = audioCtx.createOscillator();
                        const gain = audioCtx.createGain();
                        osc.connect(gain);
                        gain.connect(audioCtx.destination);
                        osc.type = 'sawtooth';
                        osc.frequency.setValueAtTime(150 + Math.random() * 200, audioCtx.currentTime);
                        gain.gain.setValueAtTime(0.04, audioCtx.currentTime);
                        gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.4);
                        osc.start();
                        osc.stop(audioCtx.currentTime + 0.4);
                    }, 50 + (i * 80));
                }
            } catch(e){}
        }

        // Música de fondo sintetizada en tiempo real (estilo menú deportivo retro)
        function startBgm() {
            if (isMusicPlaying) return;
            try {
                if (audioCtx.state === 'suspended') {
                    audioCtx.resume();
                }

                const synthTheme = () => {
                    const now = audioCtx.currentTime;
                    const chordProgression = [
                        [196.00, 246.94, 293.66, 392.00], // Sol Mayor
                        [220.00, 261.63, 329.63, 440.00], // La menor
                        [146.83, 220.00, 293.66, 369.99], // Re Mayor
                        [196.00, 246.94, 293.66, 392.00]  // Sol Mayor
                    ];
                    
                    let time = now;
                    chordProgression.forEach((chord) => {
                        chord.forEach((note, noteIdx) => {
                            const osc = audioCtx.createOscillator();
                            const gain = audioCtx.createGain();
                            osc.connect(gain);
                            gain.connect(audioCtx.destination);
                            
                            osc.type = 'triangle';
                            osc.frequency.setValueAtTime(note, time + (noteIdx * 0.3));
                            
                            gain.gain.setValueAtTime(0.06, time + (noteIdx * 0.3));
                            gain.gain.exponentialRampToValueAtTime(0.001, time + (noteIdx * 0.3) + 0.5);
                            
                            osc.start(time + (noteIdx * 0.3));
                            osc.stop(time + (noteIdx * 0.3) + 0.5);
                        });
                        time += 1.2;
                    });
                };

                synthTheme();
                bgmSource = setInterval(synthTheme, 4800);
                isMusicPlaying = true;
                
                document.getElementById('music-icon').className = "fa-solid fa-volume-high text-emerald-500";
                document.getElementById('music-text').innerText = "Música: ON";
            } catch(e) {
                console.error("No se pudo iniciar la música sintetizada:", e);
            }
        }

        function stopBgm() {
            if (bgmSource) {
                clearInterval(bgmSource);
                bgmSource = null;
            }
            isMusicPlaying = false;
            document.getElementById('music-icon').className = "fa-solid fa-volume-xmark text-red-500";
            document.getElementById('music-text').innerText = "Música: OFF";
        }

        function toggleMusic() {
            playSoundClick();
            if (isMusicPlaying) {
                stopBgm();
            } else {
                startBgm();
            }
        }
    