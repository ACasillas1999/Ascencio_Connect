
        // --- BASE DE DATOS Y ESTADO DE LA APLICACIÓN ---
        let participants = [];
        let prizes = [];
        let drawnBallsHistory = [];
        
        let currentAnimWinnerMii = null;
        let currentAnimWinningPrizeName = "";

        // Lista de personajes preestablecidos (Sin nombres del elenco original de la consola)
        const defaultMiis = [
            { id: '1', display_id: 101, name: 'Carlos', color: '#ff9500', face: 'cool' },
            { id: '2', display_id: 102, name: 'Sofía', color: '#e91e63', face: 'happy' },
            { id: '3', display_id: 103, name: 'Beto', color: '#76c336', face: 'excited' },
            { id: '4', display_id: 104, name: 'Ana', color: '#00a0e9', face: 'happy' },
            { id: '5', display_id: 105, name: 'Tomás', color: '#9c27b0', face: 'surprised' },
            { id: '6', display_id: 106, name: 'Lucía', color: '#e60012', face: 'cool' },
            { id: '7', display_id: 107, name: 'Silvia', color: '#ffeb3b', face: 'excited' }
        ];

        // Lista de premios preestablecidos (Genéricos)
        const defaultPrizes = [
            { id: 'p1', name: '🏆 Gran Trofeo de Oro', color: '#ffeb3b' },
            { id: 'p2', name: '🎳 Remera de Campeón de Sorteos', color: '#00a0e9' },
            { id: 'p3', name: '🍕 Gran Pizza Party de Celebración', color: '#ff9500' },
            { id: 'p4', name: '🎾 Raqueta Profesional de Tenis', color: '#76c336' },
            { id: 'p5', name: '🕶️ Lentes de Sol Estilo Retro', color: '#e60012' }
        ];

        function initData() {
            const backendParticipants = [];
            const backendPrizes = [];

            if (backendParticipants && backendParticipants.length > 0) {
                participants = backendParticipants;
            } else {
                const savedPart = localStorage.getItem('sports_participants');
                participants = savedPart ? JSON.parse(savedPart) : [...defaultMiis];
            }

            if (backendPrizes && backendPrizes.length > 0) {
                prizes = backendPrizes;
            } else {
                const savedPrizes = localStorage.getItem('sports_prizes');
                prizes = savedPrizes ? JSON.parse(savedPrizes) : [...defaultPrizes];
            }
            
            updateUI();
        }

        function saveToStorage() {
            localStorage.setItem('sports_participants', JSON.stringify(participants));
            localStorage.setItem('sports_prizes', JSON.stringify(prizes));
        }

        function resetData() {
            playSoundClick();
            participants = [...defaultMiis];
            prizes = [...defaultPrizes];
            drawnBallsHistory = [];
            saveToStorage();
            updateUI();
            
            initTombolaPhysics();
        }

        // --- GENERADOR DE AVATARES DE PERSONAJES EN SVG ---
        function generateMiiSVG(color, faceType) {
            let mouthPath = "M 35 65 Q 50 78 65 65";
            let eyes = `
                <circle cx="38" cy="45" r="5.5" fill="#1c1c1c"/>
                <circle cx="62" cy="45" r="5.5" fill="#1c1c1c"/>
            `;
            let brows = `
                <path d="M 30 36 Q 38 33 46 36" stroke="#1c1c1c" stroke-width="3" fill="none" stroke-linecap="round"/>
                <path d="M 54 36 Q 62 33 70 36" stroke="#1c1c1c" stroke-width="3" fill="none" stroke-linecap="round"/>
            `;
            let cheeks = '';

            if (faceType === 'cool') {
                eyes = `
                    <path d="M 28 42 L 46 42 L 44 48 L 30 48 Z M 54 42 L 72 42 L 70 48 L 56 48 Z" fill="#111" stroke="#111" stroke-width="2"/>
                    <line x1="46" y1="44" x2="54" y2="44" stroke="#111" stroke-width="2"/>
                `;
                brows = '';
                mouthPath = "M 42 63 Q 50 63 58 63";
            } else if (faceType === 'excited') {
                eyes = `
                    <circle cx="38" cy="45" r="7" fill="#1c1c1c"/>
                    <circle cx="38" cy="45" r="3" fill="#ffffff"/>
                    <circle cx="62" cy="45" r="7" fill="#1c1c1c"/>
                    <circle cx="62" cy="45" r="3" fill="#ffffff"/>
                `;
                mouthPath = "M 35 60 Q 50 82 65 60 Z";
                cheeks = `
                    <circle cx="28" cy="55" r="5" fill="#ffa0a0" opacity="0.6"/>
                    <circle cx="72" cy="55" r="5" fill="#ffa0a0" opacity="0.6"/>
                `;
            } else if (faceType === 'surprised') {
                eyes = `
                    <circle cx="38" cy="44" r="7" fill="#1c1c1c"/>
                    <circle cx="38" cy="44" r="2.5" fill="#ffffff"/>
                    <circle cx="62" cy="44" r="7" fill="#1c1c1c"/>
                    <circle cx="62" cy="44" r="2.5" fill="#ffffff"/>
                `;
                mouthPath = "M 44 65 A 6 6 0 1 0 56 65 A 6 6 0 1 0 44 65 Z";
                brows = `
                    <path d="M 30 32 Q 38 27 46 32" stroke="#1c1c1c" stroke-width="3" fill="none" stroke-linecap="round"/>
                    <path d="M 54 32 Q 62 27 70 32" stroke="#1c1c1c" stroke-width="3" fill="none" stroke-linecap="round"/>
                `;
            }

            return `
                <svg viewBox="0 0 100 100" width="100%" height="100%" class="select-none">
                    <path d="M 15 100 Q 50 72 85 100" fill="${color}" stroke="#4a5568" stroke-width="2.5" />
                    <ellipse cx="50" cy="74" rx="14" ry="8" fill="#ffdbac" stroke="#4a5568" stroke-width="2.5" />
                    <circle cx="50" cy="52" r="28" fill="#ffe0bd" stroke="#4a5568" stroke-width="2.5"/>
                    <circle cx="20" cy="52" r="5.5" fill="#ffe0bd" stroke="#4a5568" stroke-width="2.5"/>
                    <circle cx="80" cy="52" r="5.5" fill="#ffe0bd" stroke="#4a5568" stroke-width="2.5"/>
                    <path d="M 47 54 Q 50 51 53 54" stroke="#e0ac69" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                    <path d="${mouthPath}" stroke="#1c1c1c" stroke-width="3.5" fill="#fc8181" stroke-linecap="round" />
                    ${eyes}
                    ${brows}
                    ${cheeks}
                </svg>
            `;
        }
    