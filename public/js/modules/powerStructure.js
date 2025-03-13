export default class PowerStructure {
    constructor() {
        this.container = document.querySelector('.power-structure-container');
        this.dragPosition = { x: 0, y: 0 };
        this.scale = 0.8; // Начальный масштаб
        this.nodePadding = 16;

        this.removeExistingSvg();
        this.init();
        this.initContainerDrag();
        this.initConnections();

        // Добавляем обработчик изменения размера окна
        window.addEventListener('resize', () => {
            this.updateConnections();
        });

        // Применяем начальный масштаб
        this.updateContainerTransform();
    }

    updateContainerTransform() {
        this.container.style.transform =
            `translate(${this.dragPosition.x}px, ${this.dragPosition.y}px) scale(${this.scale})`;
    }

    getScale() {
        const transform = window.getComputedStyle(this.container).transform;
        if (transform && transform !== 'none') {
            const matrix = new DOMMatrix(transform);
            return matrix.a; // Получаем значение масштаба из матрицы трансформации
        }
        return 1;
    }

    removeExistingSvg() {
        // Удаляем все существующие SVG перед инициализацией
        const existingSvg = this.container.querySelector('.connections');
        if (existingSvg) {
            existingSvg.remove();
        }
    }

    init() {
        this.connections = {
            'people-president': {
                from: 'people',
                to: 'president',
                label: 'Избирает'
            },
            'people-assembly': {
                from: 'people',
                to: 'assembly',
                label: 'Формирует'
            },
            'president-executive': {
                from: 'president',
                to: 'executive',
                label: 'Руководит'
            },
            'president-judicial': {
                from: 'president',
                to: 'judicial',
                label: 'Назначает'
            },
            'assembly-legislative': {
                from: 'assembly',
                to: 'legislative',
                label: 'Определяет'
            },
            'assembly-president': {
                from: 'assembly',
                to: 'president',
                label: 'Контролирует'
            }
        };
    }

    initContainerDrag() {
        interact('.power-structure-container')
            .draggable({
                inertia: true,
                listeners: {
                    move: (event) => {
                        this.dragPosition.x += event.dx;
                        this.dragPosition.y += event.dy;
                        this.updateContainerTransform();
                        this.updateConnections();
                    }
                }
            })
            .gesturable({
                listeners: {
                    move: (event) => {
                        // Ограничиваем масштаб от 0.5 до 2
                        this.scale = Math.min(Math.max(this.scale * (1 + event.ds), 0.5), 2);
                        this.updateContainerTransform();
                        this.updateConnections();
                    }
                }
            });
    }

    initConnections() {
        const svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
        svg.classList.add('connections');
        svg.setAttribute('width', '100%');
        svg.setAttribute('height', '100%');

        const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
        defs.innerHTML = `
            <marker id="arrowhead"
                    markerWidth="10"
                    markerHeight="7"
                    refX="9"
                    refY="3.5"
                    orient="auto-start-reverse"
                    markerUnits="strokeWidth">
                <path d="M0,0 L0,7 L10,3.5 z" fill="#64748b"/>
            </marker>
        `;
        svg.appendChild(defs);

        // Создаем пути и текст для каждого соединения
        Object.keys(this.connections).forEach(id => {
            // Создаем группу для каждого соединения
            const group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            group.id = `group-${id}`;

            // Создаем основной путь
            const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            path.id = id;
            path.classList.add('connection');
            group.appendChild(path);

            // Создаем фон для текста
            const textBg = document.createElementNS('http://www.w3.org/2000/svg', 'rect');
            textBg.classList.add('connection-label-bg');
            textBg.id = `${id}-label-bg`;
            group.appendChild(textBg);

            // Создаем текст
            const text = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            text.classList.add('connection-label');
            text.id = `${id}-label`;
            group.appendChild(text);

            svg.appendChild(group);
        });

        this.container.insertBefore(svg, this.container.firstChild);
        this.updateConnections();

        document.querySelectorAll('.power-node').forEach(node => {
            node.addEventListener('mouseenter', () => this.highlightConnections(node.id));
            node.addEventListener('mouseleave', () => this.resetConnections());
        });
    }

    updateConnections() {
        const scale = this.getScale();

        Object.entries(this.connections).forEach(([id, connection]) => {
            const fromNode = document.getElementById(connection.from);
            const toNode = document.getElementById(connection.to);
            const path = document.getElementById(id);
            const label = document.getElementById(`${id}-label`);
            const labelBg = document.getElementById(`${id}-label-bg`);

            if (fromNode && toNode && path && label && labelBg) {
                const containerRect = this.container.getBoundingClientRect();
                const fromRect = fromNode.getBoundingClientRect();
                const toRect = toNode.getBoundingClientRect();

                // Корректируем координаты с учетом масштаба
                const scaleCorrection = 1 / scale;

                const fromCenterX = (fromRect.left - containerRect.left) * scaleCorrection + (fromRect.width * scaleCorrection / 2);
                const fromCenterY = (fromRect.top - containerRect.top) * scaleCorrection + (fromRect.height * scaleCorrection / 2);
                const toCenterX = (toRect.left - containerRect.left) * scaleCorrection + (toRect.width * scaleCorrection / 2);
                const toCenterY = (toRect.top - containerRect.top) * scaleCorrection + (toRect.height * scaleCorrection / 2);

                const dx = toCenterX - fromCenterX;
                const dy = toCenterY - fromCenterY;

                const fromWidth = fromRect.width * scaleCorrection / 2;
                const fromHeight = fromRect.height * scaleCorrection / 2;
                const toWidth = toRect.width * scaleCorrection / 2;
                const toHeight = toRect.height * scaleCorrection / 2;

                let fromX, fromY, toX, toY;
                let controlPoint1X, controlPoint1Y, controlPoint2X, controlPoint2Y;

                if (Math.abs(dx) > Math.abs(dy)) {
                    if (dx > 0) {
                        fromX = fromCenterX + fromWidth;
                        toX = toCenterX - toWidth;
                    } else {
                        fromX = fromCenterX - fromWidth;
                        toX = toCenterX + toWidth;
                    }
                    fromY = fromCenterY;
                    toY = toCenterY;

                    const offset = Math.abs(dx) * 0.15;
                    controlPoint1X = fromX + (dx > 0 ? offset : -offset);
                    controlPoint1Y = fromY;
                    controlPoint2X = toX - (dx > 0 ? offset : -offset);
                    controlPoint2Y = toY;
                } else {
                    if (dy > 0) {
                        fromY = fromCenterY + fromHeight;
                        toY = toCenterY - toHeight;
                    } else {
                        fromY = fromCenterY - fromHeight;
                        toY = toCenterY + toHeight;
                    }
                    fromX = fromCenterX;
                    toX = toCenterX;

                    const offset = Math.abs(dy) * 0.15;
                    controlPoint1X = fromX;
                    controlPoint1Y = fromY + (dy > 0 ? offset : -offset);
                    controlPoint2X = toX;
                    controlPoint2Y = toY - (dy > 0 ? offset : -offset);
                }

                if (!isNaN(fromX) && !isNaN(fromY) && !isNaN(toX) && !isNaN(toY)) {
                    path.setAttribute('d', `M ${fromX} ${fromY} C ${controlPoint1X} ${controlPoint1Y}, ${controlPoint2X} ${controlPoint2Y}, ${toX} ${toY}`);

                    const midX = (fromX + toX) / 2;
                    const midY = (fromY + toY) / 2;

                    label.setAttribute('x', midX);
                    label.setAttribute('y', midY);
                    label.textContent = connection.label;

                    const textBox = label.getBBox();
                    const labelPadding = 4;

                    labelBg.setAttribute('x', textBox.x - labelPadding);
                    labelBg.setAttribute('y', textBox.y - labelPadding);
                    labelBg.setAttribute('width', textBox.width + labelPadding * 2);
                    labelBg.setAttribute('height', textBox.height + labelPadding * 2);
                }
            }
        });
    }

    highlightConnections(nodeId) {
        document.querySelectorAll('.connection').forEach(path => {
            const id = path.id;
            const connection = this.connections[id];

            if (connection.from === nodeId || connection.to === nodeId) {
                path.classList.add('connection-highlighted');
            } else {
                path.classList.add('connection-dimmed');
            }
        });
    }

    resetConnections() {
        document.querySelectorAll('.connection').forEach(path => {
            path.classList.remove('connection-dimmed', 'connection-highlighted');
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new PowerStructure();
});
