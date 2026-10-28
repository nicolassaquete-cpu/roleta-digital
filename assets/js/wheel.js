/**
 * Motor da Roleta Digital com HTML5 Canvas e Web Audio API
 */
class RouletteWheel {
    constructor(canvasId, options = {}) {
        this.canvas = document.getElementById(canvasId);
        if (!this.canvas) return;

        this.ctx = this.canvas.getContext('2d');
        this.items = options.items || [];
        this.onFinish = options.onFinish || null;
        this.pointerElement = options.pointerElement || document.querySelector('.wheel-pointer');

        // Estado da animação
        this.currentAngle = 0; // Em radianos
        this.isSpinning = false;
        this.spinVelocity = 0;
        this.spinDeceleration = 0.985;
        this.lastTickIndex = -1;

        // Cores vibrantes padrão caso item não tenha cor
        this.defaultPalette = [
            '#ef4444', '#f97316', '#f59e0b', '#10b981', 
            '#06b6d4', '#3b82f6', '#6366f1', '#8b5cf6', 
            '#ec4899', '#14b8a6', '#84cc16', '#e11d48'
        ];

        // Inicializa AudioContext para efeitos sonoros nativos
        this.initAudio();

        // Configura alta resolução para telas Retina
        this.setupHighDPI();

        // Desenha estado inicial
        this.draw();
    }

    initAudio() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            this.audioCtx = new AudioContext();
        } catch (e) {
            this.audioCtx = null;
        }
    }

    playTickSound() {
        if (!this.audioCtx) return;
        if (this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }

        try {
            const osc = this.audioCtx.createOscillator();
            const gain = this.audioCtx.createGain();

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(600, this.audioCtx.currentTime);
            osc.frequency.exponentialRampToValueAtTime(150, this.audioCtx.currentTime + 0.05);

            gain.gain.setValueAtTime(0.15, this.audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.01, this.audioCtx.currentTime + 0.05);

            osc.connect(gain);
            gain.connect(this.audioCtx.destination);

            osc.start();
            osc.stop(this.audioCtx.currentTime + 0.05);
        } catch (e) {}
    }

    playWinSound() {
        if (!this.audioCtx) return;
        if (this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }

        try {
            const notas = [523.25, 659.25, 783.99, 1046.50]; // Acorde C Maior
            notas.forEach((nota, idx) => {
                const osc = this.audioCtx.createOscillator();
                const gain = this.audioCtx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(nota, this.audioCtx.currentTime + idx * 0.1);

                gain.gain.setValueAtTime(0.2, this.audioCtx.currentTime + idx * 0.1);
                gain.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + idx * 0.1 + 0.4);

                osc.connect(gain);
                gain.connect(this.audioCtx.destination);

                osc.start(this.audioCtx.currentTime + idx * 0.1);
                osc.stop(this.audioCtx.currentTime + idx * 0.1 + 0.45);
            });
        } catch (e) {}
    }

    setupHighDPI() {
        const dpr = window.devicePixelRatio || 1;
        const rect = this.canvas.getBoundingClientRect();
        const displayWidth = rect.width || 440;
        const displayHeight = rect.height || 440;

        this.canvas.width = displayWidth * dpr;
        this.canvas.height = displayHeight * dpr;

        this.ctx.scale(dpr, dpr);
        this.width = displayWidth;
        this.height = displayHeight;
        this.centerX = displayWidth / 2;
        this.centerY = displayHeight / 2;
        this.radius = Math.min(this.centerX, this.centerY) - 12;
    }

    setItems(newItems) {
        this.items = newItems;
        this.draw();
    }

    draw() {
        if (!this.ctx) return;
        const ctx = this.ctx;
        ctx.clearRect(0, 0, this.width, this.height);

        const total = this.items.length;
        if (total === 0) {
            // Desenha roleta vazia informativa
            ctx.save();
            ctx.beginPath();
            ctx.arc(this.centerX, this.centerY, this.radius, 0, Math.PI * 2);
            ctx.fillStyle = '#1e293b';
            ctx.fill();
            ctx.lineWidth = 4;
            ctx.strokeStyle = '#334155';
            ctx.stroke();

            ctx.fillStyle = '#94a3b8';
            ctx.font = '600 16px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('Adicione itens para começar', this.centerX, this.centerY);
            ctx.restore();
            return;
        }

        const sliceAngle = (Math.PI * 2) / total;

        ctx.save();
        ctx.translate(this.centerX, this.centerY);
        ctx.rotate(this.currentAngle);

        // Desenha as fatias
        for (let i = 0; i < total; i++) {
            const startAngle = i * sliceAngle;
            const endAngle = startAngle + sliceAngle;
            const item = this.items[i];
            const color = item.cor || this.defaultPalette[i % this.defaultPalette.length];

            // Fatia
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.arc(0, 0, this.radius, startAngle, endAngle);
            ctx.closePath();
            ctx.fillStyle = color;
            ctx.fill();

            // Borda da fatia
            ctx.lineWidth = 2;
            ctx.strokeStyle = 'rgba(15, 23, 42, 0.4)';
            ctx.stroke();

            // Texto do item na fatia
            ctx.save();
            ctx.rotate(startAngle + sliceAngle / 2);
            ctx.textAlign = 'right';
            ctx.textBaseline = 'middle';
            ctx.fillStyle = '#ffffff';

            // Ajusta o tamanho da fonte conforme número de itens
            let fontSize = 15;
            if (total > 20) fontSize = 12;
            if (total > 35) fontSize = 10;
            ctx.font = `bold ${fontSize}px "Plus Jakarta Sans", sans-serif`;

            // Sombra no texto para legibilidade máxima
            ctx.shadowColor = 'rgba(0, 0, 0, 0.8)';
            ctx.shadowBlur = 4;

            // Corta texto longo se necessário
            let text = item.texto || `Opção ${i + 1}`;
            const maxTextLength = total > 20 ? 12 : 18;
            if (text.length > maxTextLength) {
                text = text.substring(0, maxTextLength) + '...';
            }

            ctx.fillText(text, this.radius - 20, 0);
            ctx.restore();
        }

        // Borda externa decorativa com pinos (tachinhas)
        ctx.beginPath();
        ctx.arc(0, 0, this.radius, 0, Math.PI * 2);
        ctx.lineWidth = 6;
        ctx.strokeStyle = '#0f172a';
        ctx.stroke();

        for (let i = 0; i < total; i++) {
            const angle = i * sliceAngle;
            const pinX = Math.cos(angle) * (this.radius - 2);
            const pinY = Math.sin(angle) * (this.radius - 2);

            ctx.beginPath();
            ctx.arc(pinX, pinY, 4, 0, Math.PI * 2);
            ctx.fillStyle = '#f8fafc';
            ctx.shadowColor = 'rgba(0, 0, 0, 0.5)';
            ctx.shadowBlur = 3;
            ctx.fill();
        }

        // Centro da Roleta (Hub central)
        ctx.beginPath();
        ctx.arc(0, 0, 32, 0, Math.PI * 2);
        ctx.fillStyle = '#0f172a';
        ctx.fill();
        ctx.lineWidth = 4;
        ctx.strokeStyle = '#6366f1';
        ctx.stroke();

        ctx.beginPath();
        ctx.arc(0, 0, 16, 0, Math.PI * 2);
        ctx.fillStyle = '#ffffff';
        ctx.fill();

        ctx.restore();
    }

    spin() {
        if (this.isSpinning || this.items.length === 0) return;

        if (this.audioCtx && this.audioCtx.state === 'suspended') {
            this.audioCtx.resume();
        }

        this.isSpinning = true;
        
        // Define velocidade angular inicial aleatória e alta
        // Garante pelo menos 4 a 8 voltas completas antes de parar
        const voltasMinimas = 5;
        const impulsoAleatorio = Math.random() * 0.15 + 0.35;
        this.spinVelocity = impulsoAleatorio;
        this.spinDeceleration = 0.991 - (Math.random() * 0.003); // Suavidade na parada

        this.animate();
    }

    animate() {
        if (!this.isSpinning) return;

        this.currentAngle += this.spinVelocity;
        this.currentAngle = this.currentAngle % (Math.PI * 2);

        // Desacelera gradualmente
        this.spinVelocity *= this.spinDeceleration;

        // Efeito sonoro do ponteiro ao passar de fatia
        this.checkPointerTick();

        // Desenha frame atual
        this.draw();

        // Parada quando a velocidade for quase zero
        if (this.spinVelocity < 0.002) {
            this.isSpinning = false;
            this.spinVelocity = 0;
            this.draw();
            this.finishSpin();
            return;
        }

        requestAnimationFrame(() => this.animate());
    }

    checkPointerTick() {
        const total = this.items.length;
        if (total === 0) return;

        // O ponteiro fica no topo (3 * PI / 2 radianos ou 270 graus)
        const pointerAngle = (3 * Math.PI) / 2;
        let relativeAngle = (pointerAngle - this.currentAngle) % (Math.PI * 2);
        if (relativeAngle < 0) relativeAngle += Math.PI * 2;

        const sliceAngle = (Math.PI * 2) / total;
        const currentSliceIndex = Math.floor(relativeAngle / sliceAngle);

        if (currentSliceIndex !== this.lastTickIndex) {
            this.lastTickIndex = currentSliceIndex;
            this.playTickSound();

            if (this.pointerElement) {
                this.pointerElement.classList.add('tick');
                setTimeout(() => this.pointerElement.classList.remove('tick'), 60);
            }
        }
    }

    getWinnerItem() {
        const total = this.items.length;
        if (total === 0) return null;

        const pointerAngle = (3 * Math.PI) / 2;
        let relativeAngle = (pointerAngle - this.currentAngle) % (Math.PI * 2);
        if (relativeAngle < 0) relativeAngle += Math.PI * 2;

        const sliceAngle = (Math.PI * 2) / total;
        const winnerIndex = Math.floor(relativeAngle / sliceAngle) % total;

        return this.items[winnerIndex];
    }

    finishSpin() {
        const winner = this.getWinnerItem();
        this.playWinSound();

        // Dispara efeito de confetes
        if (typeof confetti === 'function') {
            confetti({
                particleCount: 120,
                spread: 80,
                origin: { y: 0.6 }
            });
        }

        if (typeof this.onFinish === 'function') {
            this.onFinish(winner);
        }
    }
}
