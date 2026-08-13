(function () {
    const effectType = window.themeEffect || 'none';

    if (effectType === 'none') return;

    if (effectType === 'snow' || effectType === 'noel') {
        initSnowEffect();
    } else if (effectType === 'tet') {
        initFlowerEffect();
    }

    function initSnowEffect() {
        const snowflakeCount = 50;
        const style = document.createElement('style');
        style.innerHTML = `
            .snowflake {
                position: fixed;
                top: -10px;
                z-index: 9999;
                color: #fff;
                user-select: none;
                pointer-events: none;
                animation-name: fall;
                animation-timing-function: linear;
                animation-iteration-count: infinite;
            }
            @keyframes fall {
                0% { transform: translateY(0); }
                100% { transform: translateY(100vh); }
            }
        `;
        document.head.appendChild(style);

        const symbols = ['❄', '❅', '❆', '•'];

        for (let i = 0; i < snowflakeCount; i++) {
            createSnowflake(symbols);
        }
    }

    function createSnowflake(symbols) {
        const flake = document.createElement('div');
        flake.classList.add('snowflake');
        flake.innerText = symbols[Math.floor(Math.random() * symbols.length)];

        const startX = Math.random() * window.innerWidth;
        const duration = Math.random() * 5 + 5;
        const delay = Math.random() * 5;
        const size = Math.random() * 15 + 10;
        const opacity = Math.random() * 0.5 + 0.3;

        flake.style.left = startX + 'px';
        flake.style.fontSize = size + 'px';
        flake.style.opacity = opacity;
        flake.style.animationDuration = duration + 's';
        flake.style.animationDelay = delay + 's';

        document.body.appendChild(flake);
    }

    function initFlowerEffect() {
        const flowerCount = 30;
        const style = document.createElement('style');
        style.innerHTML = `
            .flower {
                position: fixed;
                top: -10px;
                z-index: 9999;
                user-select: none;
                pointer-events: none;
                border-radius: 50% 0 50% 50%;
                animation-name: fall-sway;
                animation-timing-function: linear;
                animation-iteration-count: infinite;
            }
            .flower.mai {
                background: linear-gradient(135deg, #ffeb3b, #fbc02d);
                box-shadow: 0 0 2px rgba(0,0,0,0.1);
            }
            .flower.dao {
                background: linear-gradient(135deg, #ff80ab, #f50057);
                box-shadow: 0 0 2px rgba(0,0,0,0.1);
            }
            @keyframes fall-sway {
                0% { transform: translateY(0) translateX(0) rotate(0deg); opacity: 0.8;}
                25% { transform: translateY(25vh) translateX(20px) rotate(45deg); }
                50% { transform: translateY(50vh) translateX(-20px) rotate(90deg); }
                75% { transform: translateY(75vh) translateX(20px) rotate(135deg); }
                100% { transform: translateY(100vh) translateX(0) rotate(180deg); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Instead of images, we use CSS shapes for petals
        const types = ['mai', 'dao'];

        for (let i = 0; i < flowerCount; i++) {
            setTimeout(() => createFlower(types), Math.random() * 3000);
        }
    }

    function createFlower(types) {
        const flower = document.createElement('div');
        flower.classList.add('flower');
        // Randomly pick flower type
        const type = types[Math.floor(Math.random() * types.length)];
        flower.classList.add(type);

        const startX = Math.random() * window.innerWidth;
        const duration = Math.random() * 5 + 5;
        const size = Math.random() * 10 + 10; // 10-20px size

        flower.style.left = startX + 'px';
        flower.style.width = size + 'px';
        flower.style.height = size + 'px'; // Making it a square base for border-radius
        flower.style.animationDuration = duration + 's';

        // Reset position helper (simple version as CSS handles basic loop)
        flower.addEventListener('animationiteration', () => {
            flower.style.left = Math.random() * window.innerWidth + 'px';
        });

        document.body.appendChild(flower);
    }

})();
