<?php
// Ready Orders Public Display
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos Prontos | Delícias da Fran</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap"
        rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            background: var(--bg-base);
            min-height: 100vh;
        }

        .ready-container {
            padding: 2rem;
            min-height: 100vh;
        }

        .ready-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .ready-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .ready-logo-icon {
            width: 60px;
            height: 60px;
            background: var(--gradient-primary);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .ready-logo-text {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 800;
        }

        .ready-logo-text span {
            color: var(--accent-primary);
        }

        .ready-title {
            font-size: 2.5rem;
            color: var(--accent-green);
            margin-bottom: 0.5rem;
        }

        .ready-subtitle {
            color: var(--text-secondary);
            font-size: 1.25rem;
        }

        .ready-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .ready-column {
            background: var(--bg-secondary);
            border-radius: 1.5rem;
            padding: 1.5rem;
            min-height: 400px;
        }

        .column-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-align: center;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--border-color);
        }

        .column-preparando {
            color: var(--accent-secondary);
        }

        .column-pronto {
            color: var(--accent-green);
        }

        .ready-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .ready-card {
            background: linear-gradient(145deg, #1f1f1f 0%, #151515 100%);
            border: 3px solid var(--border-color);
            border-radius: 1rem;
            padding: 1.5rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .card-preparando {
            border-color: var(--accent-secondary);
        }

        .card-pronto {
            border-color: var(--accent-green);
            animation: readyPulse 2s infinite;
        }

        .ready-card.new {
            animation: readyAppear 0.5s ease-out, readyPulse 2s infinite;
        }

        .empty-column {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }

        .empty-column span {
            font-size: 3rem;
            display: block;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        @keyframes readyAppear {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes readyPulse {

            0%,
            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.4);
            }

            50% {
                box-shadow: 0 0 30px rgba(34, 197, 94, 0.3);
            }
        }

        .ready-order-number {
            font-size: 5rem;
            font-weight: 800;
            color: var(--accent-green);
            line-height: 1;
            font-family: var(--font-display);
        }

        .ready-customer-name {
            font-size: 2.5rem;
            font-weight: 700;
            margin-top: 1rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .ready-time {
            color: var(--text-muted);
            font-size: 1rem;
            margin-top: 0.5rem;
        }

        .empty-display {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-display-icon {
            font-size: 6rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-display-text {
            font-size: 1.5rem;
            color: var(--text-secondary);
        }

        .status-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: var(--bg-secondary);
            border-top: 1px solid var(--border-color);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .status-bar-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.5;
            }
        }

        @media (max-width: 768px) {
            .ready-order-number {
                font-size: 3.5rem;
            }

            .ready-customer-name {
                font-size: 1.75rem;
            }

            .ready-title {
                font-size: 1.75rem;
            }
        }
    </style>
</head>

<body x-data="readyDisplay()" x-init="init()">

    <div class="ready-container">
        <!-- Header -->
        <header class="ready-header">
            <div class="ready-logo">
                <img src="<?= asset('img/logo.png') ?>" alt="Delícias da Fran" style="height: 60px;">
            </div>
            <h1 class="ready-title">📋 Acompanhe seu Pedido</h1>
            <p class="ready-subtitle">Quando seu nome aparecer em "Prontos", retire seu pedido no balcão</p>
        </header>

        <!-- Two Column Layout -->
        <div class="ready-columns">
            <!-- Em Preparo -->
            <div class="ready-column">
                <h2 class="column-title column-preparando">👨‍🍳 Em Preparo</h2>
                <div class="ready-grid" x-show="preparando.length > 0">
                    <template x-for="order in preparando" :key="order.id">
                        <div class="ready-card card-preparando">
                            <div class="ready-order-number" x-text="'#' + order.order_number"></div>
                            <div class="ready-customer-name" x-text="order.customer_name"></div>
                        </div>
                    </template>
                </div>
                <div class="empty-column" x-show="preparando.length === 0">
                    <span>⏳</span>
                    <p>Aguardando...</p>
                </div>
            </div>

            <!-- Prontos -->
            <div class="ready-column">
                <h2 class="column-title column-pronto">✅ Prontos para Retirada</h2>
                <div class="ready-grid" x-show="prontos.length > 0">
                    <template x-for="order in prontos" :key="order.id">
                        <div class="ready-card card-pronto" :class="{ 'new': order.isNew }">
                            <div class="ready-order-number" x-text="'#' + order.order_number"></div>
                            <div class="ready-customer-name" x-text="order.customer_name"></div>
                        </div>
                    </template>
                </div>
                <div class="empty-column" x-show="prontos.length === 0">
                    <span>🍽️</span>
                    <p>Nenhum pedido pronto</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Bar -->
    <div class="status-bar">
        <div class="status-bar-info">
            <div class="status-indicator"></div>
            <span class="text-secondary">Atualizando automaticamente</span>
        </div>
        <div class="text-muted" x-text="'Última atualização: ' + lastUpdate"></div>
    </div>

    <!-- Audio for notification -->
    <audio id="notificationSound" preload="auto">
        <source
            src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2Onp6gnJaIdGdgZ3WDkpKNjIWPeG1ga2N0g5KMkJGMj4t0YmVlbneJlJWVk5CMhnJlZWdyfYqUl5aTkZCLd2VnZ3B6iZOXl5WSkY6BaGdnaXSCkJaXlpSSkY1/Z2hoanaBjpSXlpWTkpCHamlpanSAi5OXl5WUk4+JcGtqanR/ipKWl5WUk5CKcmxranR/ipKWlpWUk5GKc21sbHR+iZGVlpWUk5GLdG5sbHR9iJGVlpWUk5KLdW5sbHR9iJCUlpWUk5KMdm9sbHR9h5CUlpWUk5KMd29sbHR9h5CUlpWUk5KNeHBtbHR9h5CUlZWUk5KNeHBtbXR9h4+UlZWUk5KOeHBtbXR9h4+UlZWUk5OOeXFtbXR9h4+UlZWUk5OOeXFubXR9h4+UlZWUk5OPenFubXR9ho+UlZWUlJOPenJubXR9ho+UlZWUlJSPe3JubXR9ho+UlZWUlJSQe3JubXR9ho6TlZWUlJSQe3JubXR9ho6TlZWUlJSQe3JvbnR9ho6TlZSUlJSQfHJvbnR9ho6TlZSUlJSRfHNvbnR9ho6TlZSUlJSRfHNvbnR9ho6TlZSUlJSRfHNvbnR8ho6TlZSUlJSRfHNvb3R8ho6TlZSUlJSRfXNvb3R8ho6TlJSUlJSRfXNvb3R8ho6TlJSUlJSRfXNvb3R8ho6TlJSUlJSRfXNvb3R8hY6TlJSUlJSRfXRwb3R8hY6TlJSUlJSSfnRwb3R8hY6TlJSUlJSSfnRwb3R8hY6TlJSUlJSSfnRwb3R8hY6TlJSUlJSSfnRwb3R8hY6TlJSUlJSSfnRwb3R8hY2SlJSUlJSSfnRwb3R8hY2SlJSUlJSSf3Rwb3R8hY2SlJSUlJSSf3Rwb3R8hY2SlJSUlJSSf3Rwb3R8hY2SlJSUlJWSf3Rwb3R8hY2SlJSUlJWSf3Vwb3R8hY2SlJSUlJWSf3VxcHR8hY2SlJSUlJWSf3VxcHR8hY2SlJSUlJWTgHVxcHN8hY2SlJSUlJWTgHVxcHN8hY2SlJSUlJWTgHVxcHN8hY2SlJSUlZWTgHVxcHN8hY2SlJSUlZWTgHZxcHN8hY2SlJSUlZWTgHZxcHN8hY2SlJSUlZWTgXZxcHN8hY2SlJSUlZaTgXZxcHN8hY2SlJSUlZaTgXZxcHN8hY2SlJSUlZaTgXZxcHN7hY2SlJSUlZaTgXZycXN7hY2SlJSUlZaTgnZycXN7hY2SlJSVlZaTgnZycXN7hY2SlJSVlZaTgnZycXN7hY2SlJSVlZaTgnZycXN7hY2RlJSVlZaTgndycXN7hY2RlJSVlZaTgndycXN7hY2RlJSVlZeTgndycXN7hY2RlJSVlZeTg3dycXN7hY2RlJSVlZeTg3dycXN7hY2RlJSVlZeTg3dycXN7hY2RlJSVlZeTg3dycXN7hY2RlJSVlZeTg3dycXN7hY2RlJSVlZiTg3dzcXN7hY2RlJSVlZiTg3dzcXN7hY2RlJSVlZiUhHdzcXN7hY2RlJSVlZiUhHdzcXN7hY2RlJSVlZiUhHdzcXN7hY2RlJSVlZiUhHdzcXN7hYyRlJSVlZiUhHdzcXN7hYyRlJSVlZiUhHdzcXN7hYyRlJSVlZiUhHdzcXN7hYyRlJSVlZiUhHhzcXN7hYyRlJSVlZiUhHhzcXN6hYyRlJSVlZiUhHhzcXN6hYyRlJSVlpmUhHhzcXN6hYyRlJSVlpmVhXhzcXN6hYyRlJSVlpmVhXh0cXN6hYyRlJSVlpmVhXh0cXN6hYyRlJSWlpmVhXh0cXN6hYyRlJSWlpmVhXh0cXN6hYyRlJSWlpmVhXh0cnN6hYyRlJSWlpmVhXh0cnN6hYyRlJSWlpmVhnh0cnN6hYyRlJSWlpmVhnh0cnN6hYyRlJSWlpmVhnh0cnN6hYyQlJSWlpmWhnh0cnN6hYyQlJSWlpmWhnh0cnN6hYyQlJSWlpmWhnh0cnN6hYyQlJSWlpmWhnh0cnN6hYyQlJSWlpmWhnh0cnJ6hYyQlJSWlpmWhnh1cnJ6hYyQlJSWl5mWhnh1cnJ6hYyQlJSWl5mWhnh1cnJ6hYyQlJSWl5mWh3l1cnJ6hYyQlJSWl5mWh3l1cnJ6hYuQlJSWl5mWh3l1cnJ6hYuQlJSWl5mXh3l1cnJ6hYuQlJSWl5mXh3l1cnJ6hYuQlJSWl5mXh3l1cnJ5hYuQlJSWl5mXh3l1cnJ5hYuQlJSXl5mXh3l1cnJ5hYuQlJSXl5mXiHl1cnJ5hYuQlJSXl5qXiHl1cnJ5hYuQlJSXl5qXiHl1cnJ5hYuQlJSXl5qXiHl1cnJ5hYuQlJSXl5qXiHl2cnJ5hYuQlJSXl5qXiHp2cnJ5hYuQlJSXl5qYiHp2cnJ5hYuQlJSXl5qYiHp2cnJ5hYuQlJSXl5qYiHp2cnJ5hYuQlJWXl5qYiHp2cnJ5hYuQlJWXl5qYiHp2c3J5hYuQlJWXl5qYiHp2c3J5hYuPk5WXl5qYiHp2c3J5hYuPk5WXl5qYiXp2c3J5hYuPk5WXl5qYiXp2c3J5hYuPk5WXmJqYiXp2c3J5hIuPk5WXmJqYiXp2c3J5hIuPk5WXmJqYiXt2c3J5hIuPk5WXmJqZiXt2c3J5hIuPk5WXmJqZiXt2c3F4hIuPk5WXmJqZiXt3c3F4hIuPk5WYmJqZiXt3c3F4hIuPk5WYmJqZint3c3F4hIuPk5WYmJqZint3c3F4hIuPk5WYmJqZint3c3F4hIuPk5aYmJqZint3c3F4hIqPk5aYmJuZint3c3F4hIqPk5aYmJuZint3c3F4hIqPk5aYmJuZint3dHF4hIqPk5aYmJuaivt3dHF4hIqPk5aYmJuaivt3dHF4g4qPkpaYmJuait3dHF3g4qOkpaYmZuait3dHF3g4qOkpaYmZqait3dHF3g4qOkpaYmZqait3dHF3g4qOkpaYmZqbit3eHF3g4qOkpaZmZqbit3eHF3g4qOkpaZmZqbi/t4eHF3g4qOkpaZmZqbi/t4eHF3g4qOkpaZmZqbi/t4eHF3g4qOkpaZmZuci/t4eHF3g4qOkpaZmZuci/t4eHF3g4mOkpaZmpuci/t4eHF3g4mOkpaZmpuci/t4eHF2g4mOkpaZmpuci/t4eHF2g4mOkpaZmpudi/x4eHF2g4mOkpaZmpucjPx4eXF2g4mOkpaZmpucjPx4eXF2g4mOkpaZmpycjPx4eXF2g4mOkpaZmpycjPx4eXF2g4mOkpaZmpycjPx4eXF2g4mNkZWZmpycjPx5eXF2g4mNkZWZmpycjPx5eXF2g4mNkZWZm5ycjPx5eXF2g4mNkZWZm5ycjPx5eXF2g4mNkZWZm5ycjPx5eXF2g4mNkZWZm5ycjfx5eXF2g4mNkZWZm5ycjfx5eXF2g4mNkZWZm5ycjfx5eXF1g4mNkZWZm5ydjfx5eXJ1g4mNkZWZm5ydjfx5eXJ1gomNkZWZm5ydjfx5eXJ1gomNkZWam5ydjfx5eXJ1gomNkZWam5ydjfx5eXJ1gomNkZWam5ydjfx5enJ1gomNkZWam5ydjf16enJ1gomNkZWam5ydjf16enJ1gomNkZaam5yejf16enJ1gomNkZaam5yejf16enJ1gomMkJSam5yejf16enJ1gomMkJSam52ejf16enJ1gomMkJSam52ejf16enJ1gomMkJSam52ejf16enJ1gomMkJSam52ejf16enJ0gYmMkJSam52ejv56enJ0gYmMkJSam52ejv56e3J0gYmMkJSam52ejv56e3J0gYmMkJSam52ejv56e3J0gYmMkJSam52ejv56e3J0gYmMkJSam52ejv56e3J0gYmMkJSam52ejv56e3J0gYmMkJSam52ejv56e3J0gYmMkJSbm52ejv57e3J0gYmMkJSbm52ej/57e3J0gYmMkJSbm52ej/57e3J0gYmMkJSbm52ej/57e3J0gYiMkJSbm52ej/57e3J0gYiMkJSbm52fj/57e3J0gYiMkJSbm52fj/57e3J0gYiMkJSbm56fj/57e3Jz"
            type="audio/wav">
    </audio>

    <script>
        function readyDisplay() {
            return {
                preparando: [],
                prontos: [],
                lastUpdate: '--:--:--',
                knownProntoIds: new Set(),

                init() {
                    this.fetchOrders();
                    // Poll every 3 seconds
                    setInterval(() => this.fetchOrders(), 3000);
                },

                async fetchOrders() {
                    try {
                        const res = await fetch('<?= url('api/ready-orders') ?>');
                        const data = await res.json();

                        if (data.success) {
                            // Update preparando
                            this.preparando = data.preparando || [];

                            // Check for new PRONTO orders
                            const newProntos = (data.prontos || []).filter(o => !this.knownProntoIds.has(o.id));

                            if (newProntos.length > 0) {
                                // Play sound and announce
                                this.playNotification(newProntos);
                            }

                            // Update known prontos
                            (data.prontos || []).forEach(o => this.knownProntoIds.add(o.id));

                            // Mark new orders
                            this.prontos = (data.prontos || []).map(o => ({
                                ...o,
                                isNew: newProntos.some(n => n.id === o.id)
                            }));

                            // Remove "new" flag after animation
                            setTimeout(() => {
                                this.prontos = this.prontos.map(o => ({ ...o, isNew: false }));
                            }, 2000);

                            this.lastUpdate = new Date().toLocaleTimeString('pt-BR');
                        }
                    } catch (e) {
                        console.error('Failed to fetch ready orders:', e);
                    }
                },

                playNotification(newOrders) {
                    // Play sound
                    const audio = document.getElementById('notificationSound');
                    if (audio) {
                        audio.currentTime = 0;
                        audio.play().catch(() => { });
                    }

                    // Use Speech Synthesis
                    if ('speechSynthesis' in window) {
                        newOrders.forEach((order, index) => {
                            setTimeout(() => {
                                const msg = new SpeechSynthesisUtterance(
                                    `${order.customer_name}, seu pedido número ${order.order_number} está pronto!`
                                );
                                msg.lang = 'pt-BR';
                                msg.rate = 0.9;
                                speechSynthesis.speak(msg);
                            }, index * 3000);
                        });
                    }
                },

                formatTime(datetime) {
                    if (!datetime) return '';
                    const date = new Date(datetime);
                    return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                }
            }
        }
    </script>
</body>

</html>