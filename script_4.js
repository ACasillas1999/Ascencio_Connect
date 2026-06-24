
        // --- ACTUALIZACIÓN DE INTERFAZ DE USUARIO ---
        function updateUI() {
            document.getElementById('tombola-participant-count').innerText = participants.length;
            document.getElementById('setup-participant-count').innerText = participants.length;
            document.getElementById('setup-prize-count').innerText = prizes.length;
            
            const prizeCountView = document.getElementById('tombola-prize-count-view');
            if (prizeCountView) prizeCountView.innerText = prizes.length;

            const tombolaPrizes = document.getElementById('tombola-available-prizes');
            if (tombolaPrizes) {
                tombolaPrizes.innerHTML = '';
                if (prizes.length === 0) {
                    tombolaPrizes.innerHTML = '<div class="text-gray-400 text-center py-4 text-xs">Aún no hay premios cargados.</div>';
                } else {
                    prizes.forEach(pr => {
                        const row = document.createElement('div');
                        const isWon = pr.winner ? true : false;
                        const bgColor = isWon ? 'bg-emerald-900/40 border-emerald-700/50 shadow-sm' : 'bg-slate-800/40 border-slate-700/50';
                        const iconColor = isWon ? 'text-emerald-400 bg-emerald-900/80' : 'text-gray-400 bg-slate-800';
                        
                        const winnerHtml = isWon 
                            ? `<span class="font-bold text-emerald-400 text-sm truncate bg-emerald-900/60 px-2.5 py-1 rounded-md border border-emerald-800/50"><i class="fa-solid fa-check mr-1"></i>${pr.winner}</span>` 
                            : `<span class="font-semibold text-gray-500 text-[11px] uppercase italic">Pendiente</span>`;

                        row.className = `flex items-center justify-between p-2 rounded-xl border ${bgColor} transition-all`;
                        row.innerHTML = `
                            <div class="flex items-center gap-2 w-1/2 overflow-hidden pr-2">
                                <div class="w-7 h-7 rounded-full ${iconColor} flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid ${isWon ? 'fa-gift' : 'fa-box'} text-[10px]"></i>
                                </div>
                                <span class="font-bold text-gray-200 text-xs leading-tight truncate" title="${pr.name}">${pr.name}</span>
                            </div>
                            <div class="w-1/2 text-right overflow-hidden flex justify-end items-center">
                                ${winnerHtml}
                            </div>
                        `;
                        tombolaPrizes.appendChild(row);
                    });
                }
            }

            const tombolaList = document.getElementById('tombola-miis-list');
            tombolaList.innerHTML = '';
            if (participants.length === 0) {
                tombolaList.innerHTML = '<div class="text-gray-400 text-center py-10 text-sm">No hay participantes cargados. Agrega algunos en Configuración.</div>';
            } else {
                const fragment = document.createDocumentFragment();
                const renderLimit = 100; // Limite para no congelar la UI con 1000+ SVGs
                const toRender = participants.slice(0, renderLimit);
                
                toRender.forEach(p => {
                    const card = document.createElement('div');
                    card.className = "flex items-center gap-3 p-2 bg-white rounded-xl border-2 border-slate-200/80 shadow-xs hover:border-[#00a0e9] transition-all";
                    card.innerHTML = `
                        <div class="w-12 h-12 bg-slate-50 rounded-full border border-slate-200 p-0.5 overflow-hidden flex-shrink-0">
                            ${generateMiiSVG(p.color, p.face)}
                        </div>
                        <div class="flex-grow">
                            <p class="font-bold text-gray-800 text-sm">${p.name}</p>
                            <span class="text-[10px] text-[#00a0e9] uppercase font-bold">ID #${p.display_id}</span>
                        </div>
                    `;
                    fragment.appendChild(card);
                });
                
                if (participants.length > renderLimit) {
                    const extra = document.createElement('div');
                    extra.className = "text-center py-3 mt-2 text-gray-500 font-bold text-sm bg-gray-50 rounded-xl border-2 border-dashed";
                    extra.innerText = `+ ${participants.length - renderLimit} participantes más...`;
                    fragment.appendChild(extra);
                }
                tombolaList.appendChild(fragment);
            }

            // Actualizar Ticker
            const tickerEl = document.getElementById('participant-ticker');
            if (tickerEl) {
                if (participants.length === 0) {
                    tickerEl.innerHTML = 'El bombo está vacío.';
                } else {
                    const namesString = participants.map(p => `<span class="text-white">${p.name}</span> <span class="text-yellow-400/80 text-xs">(#${p.display_id})</span>`).join(' &nbsp;&nbsp;&nbsp;★&nbsp;&nbsp;&nbsp; ');
                    tickerEl.innerHTML = namesString;
                    const duration = Math.max(15, participants.length * 1.5); // Velocidad constante
                    tickerEl.style.animationDuration = `${duration}s`;
                }
            }

            const setupMiis = document.getElementById('setup-miis-list');
            setupMiis.innerHTML = '';
            const renderLimitSetup = 150; // Límite de optimización de UI
            participants.slice(0, renderLimitSetup).forEach(p => {
                const row = document.createElement('div');
                row.className = "flex items-center justify-between p-2.5 bg-white rounded-2xl border-2 border-slate-200 shadow-xs";
                row.innerHTML = `
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 bg-slate-50 border border-slate-200 rounded-full overflow-hidden p-0.5 flex-shrink-0">
                            ${generateMiiSVG(p.color, p.face)}
                        </div>
                        <span class="font-bold text-gray-800">${p.name} <span class="text-xs text-gray-400 block">ID #${p.display_id}</span></span>
                    </div>
                    <button onclick="removeParticipant('${p.id}')" class="w-8 h-8 rounded-full bg-red-100 hover:bg-red-500 text-red-500 hover:text-white transition-colors flex items-center justify-center font-bold">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
                setupMiis.appendChild(row);
            });
            if (participants.length > renderLimitSetup) {
                const extra = document.createElement('div');
                extra.className = "text-center py-2 text-gray-500 font-bold text-sm";
                extra.innerText = `+ ${participants.length - renderLimitSetup} ocultos para fluidez...`;
                setupMiis.appendChild(extra);
            }

            const setupPrizes = document.getElementById('setup-prizes-list');
            setupPrizes.innerHTML = '';
            if (prizes.length === 0) {
                setupPrizes.innerHTML = '<div class="text-gray-400 text-center py-10 text-sm">No hay premios.</div>';
            } else {
                prizes.forEach(pr => {
                    const row = document.createElement('div');
                    row.className = "flex items-center justify-between p-3 bg-white rounded-2xl border-2 border-slate-200 shadow-xs";
                    row.innerHTML = `
                        <div class="flex items-center gap-3">
                            <div class="w-6 h-6 rounded-full border border-black/10" style="background-color: ${pr.color}"></div>
                            <span class="font-bold text-gray-800">${pr.name}</span>
                        </div>
                        <button onclick="removePrize('${pr.id}')" class="wii-btn p-2 rounded-xl text-red-500 hover:border-red-500">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;
                    setupPrizes.appendChild(row);
                });
            }

            updateHistories();
        }

        function updateHistories() {
            const tombolaHistEl = document.getElementById('tombola-history');
            tombolaHistEl.innerHTML = '';
            if (drawnBallsHistory.length === 0) {
                tombolaHistEl.innerHTML = '<p class="text-gray-400 text-sm py-1">No se han extraído ganadores todavía.</p>';
            } else {
                drawnBallsHistory.forEach(b => {
                    const badge = document.createElement('div');
                    badge.className = "flex items-center gap-2 bg-sky-100 border-2 border-sky-300 text-sky-800 px-3 py-1.5 rounded-full font-bold text-sm whitespace-nowrap flex-shrink-0 animate-fade-in";
                    badge.innerHTML = `
                        <div class="w-4 h-4 rounded-full" style="background-color: ${b.color}"></div>
                        <span>🏆 ${b.name} (${b.prize})</span>
                    `;
                    tombolaHistEl.appendChild(badge);
                });
            }
        }

        // --- ACCIONES DE GESTIÓN (Añadir/Eliminar) ---
        function saveCustomMii() {
            playSoundClick();
            const nameInput = document.getElementById('input-mii-name');
            const name = nameInput.value.trim();
            if (!name) return;

            const color = document.getElementById('select-mii-color').value;
            const face = document.getElementById('select-mii-face').value;

            const newMii = {
                id: 'char_' + Date.now(),
                display_id: Math.floor(Math.random() * 9000 + 1000),
                name: name,
                color: color,
                face: face
            };

            participants.push(newMii);
            nameInput.value = '';
            saveToStorage();
            updateUI();
            
            initTombolaPhysics();
        }

        function addMiiRandom() {
            playSoundClick();
            const names = ['Felipe', 'Lucía', 'Nicolás', 'Martina', 'Gastón', 'Sofía', 'Sandro', 'Estela', 'Charly', 'Renata', 'Mateo', 'Valentina'];
            const randomName = names[Math.floor(Math.random() * names.length)] + " " + Math.floor(Math.random()*90 + 10);
            const colors = ['#00a0e9', '#ff9500', '#76c336', '#e60012', '#e91e63', '#9c27b0', '#ffeb3b'];
            const faces = ['happy', 'cool', 'excited', 'surprised'];

            const newMii = {
                id: 'char_' + Date.now(),
                display_id: Math.floor(Math.random() * 9000 + 1000),
                name: randomName,
                color: colors[Math.floor(Math.random() * colors.length)],
                face: faces[Math.floor(Math.random() * faces.length)]
            };

            participants.push(newMii);
            saveToStorage();
            updateUI();
            
            initTombolaPhysics();
        }

        function addMultipleRandomMiis(amount) {
            playSoundClick();
            const names = ['Felipe', 'Lucía', 'Nicolás', 'Martina', 'Gastón', 'Sofía', 'Sandro', 'Estela', 'Charly', 'Renata', 'Mateo', 'Valentina', 'Alex', 'Juan', 'Maria', 'Pedro', 'Ana'];
            const colors = ['#00a0e9', '#ff9500', '#76c336', '#e60012', '#e91e63', '#9c27b0', '#ffeb3b'];
            const faces = ['happy', 'cool', 'excited', 'surprised'];

            for (let i = 0; i < amount; i++) {
                const randomName = names[Math.floor(Math.random() * names.length)] + " " + Math.floor(Math.random()*900 + 100);
                const newMii = {
                    id: 'char_' + Date.now() + '_' + i,
                    display_id: Math.floor(Math.random() * 90000 + 10000),
                    name: randomName,
                    color: colors[Math.floor(Math.random() * colors.length)],
                    face: faces[Math.floor(Math.random() * faces.length)]
                };
                participants.push(newMii);
            }

            saveToStorage();
            updateUI();
            initTombolaPhysics();
            showCustomToast(`¡Añadidos!`, `Se agregaron ${amount} participantes de prueba al instante.`);
        }

        function removeParticipant(id) {
            playSoundClick();
            participants = participants.filter(p => p.id !== id);
            saveToStorage();
            updateUI();
            initTombolaPhysics();
        }

        function savePrize() {
            playSoundClick();
            const nameInput = document.getElementById('input-prize-name');
            const name = nameInput.value.trim();
            if (!name) return;

            const color = document.getElementById('input-prize-color').value;

            const newPrize = {
                id: 'p_' + Date.now(),
                name: name,
                color: color
            };

            prizes.push(newPrize);
            nameInput.value = '';
            saveToStorage();
            updateUI();
        }

        function removePrize(id) {
            playSoundClick();
            prizes = prizes.filter(p => p.id !== id);
            saveToStorage();
            updateUI();
        }
    