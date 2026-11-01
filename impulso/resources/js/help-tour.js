// Botones que lanzan la guía; cada uno trae los pasos en JSON dentro de data-help-tour.
const helpTourButtons = document.querySelectorAll('[data-help-tour]');
// Si el usuario prefiere menos movimiento, se evita el scroll suave.
const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
// Estado de la guía abierta (null si no hay ninguna).
let activeTour = null;

/**
 * Crea un elemento con su clase y lo agrega al contenedor padre.
 * @param {string} tag Nombre de la etiqueta HTML.
 * @param {string} className Clases CSS del elemento.
 * @param {Element} parent Contenedor donde se inserta.
 * @returns {HTMLElement} El elemento creado.
 */
function makeElement(tag, className, parent) {
    const element = document.createElement(tag);
    element.className = className;
    parent.append(element);
    return element;
}

/**
 * Cierra la guía activa: quita listeners, temporizadores, capas y tarjeta,
 * y devuelve el foco al botón que la abrió.
 */
function closeHelpTour() {
    if (!activeTour) return;

    window.removeEventListener('keydown', activeTour.onKeydown);
    window.removeEventListener('resize', activeTour.position);
    activeTour.shades.forEach((shade) => shade.remove());
    if (activeTour.scrollEndTimer) {
        window.clearTimeout(activeTour.scrollEndTimer);
    }
    if (activeTour.positionFrame) {
        window.cancelAnimationFrame(activeTour.positionFrame);
    }
    document.removeEventListener('scroll', activeTour.onScroll, true);
    activeTour.ring.remove();
    activeTour.card.remove();
    activeTour.trigger.focus();
    activeTour = null;
}

/**
 * Inicia la guía paso a paso de una pantalla: oscurece la página, resalta cada
 * elemento objetivo y muestra una tarjeta con título, texto y navegación.
 * @param {HTMLElement} trigger Botón que abrió la guía (recupera el foco al cerrar).
 * @param {Array<{target: string, title: string, body: string}>} steps Pasos con selector CSS del objetivo.
 */
function startHelpTour(trigger, steps) {
    closeHelpTour();

    // Solo se usan pasos cuyo objetivo existe, es un selector válido y está visible.
    const availableSteps = steps.filter((step) => {
        let target = null;
        try {
            target = document.querySelector(step.target);
        } catch (error) {
            return false;
        }

        return Boolean(target && target.getClientRects().length && getComputedStyle(target).visibility !== 'hidden');
    });
    if (!availableSteps.length) return;

    // Cuatro paneles oscuros (arriba, izquierda, derecha, abajo) que dejan un hueco sobre el objetivo; clic en ellos cierra la guía.
    const shades = ['top', 'left', 'right', 'bottom'].map((side) => {
        const shade = makeElement('div', `help-tour-shade help-tour-shade-${side}`, document.body);
        shade.addEventListener('click', closeHelpTour);
        return shade;
    });
    // Anillo que enmarca el elemento resaltado y tarjeta accesible (diálogo modal) con los textos.
    const ring = makeElement('div', 'help-tour-spotlight', document.body);
    const card = makeElement('section', 'help-tour-card', document.body);
    card.setAttribute('role', 'dialog');
    card.setAttribute('aria-modal', 'true');
    card.setAttribute('aria-label', 'Guía de esta pantalla');
    card.tabIndex = -1;

    const head = makeElement('div', 'help-tour-card-head', card);
    const title = makeElement('h2', 'help-tour-title', head);
    const close = makeElement('button', 'help-tour-close', head);
    close.type = 'button';
    close.setAttribute('aria-label', 'Cerrar guía');
    close.textContent = '×';

    const body = makeElement('p', 'help-tour-body', card);
    const footer = makeElement('div', 'help-tour-footer', card);
    const count = makeElement('span', 'help-tour-count', footer);
    const controls = makeElement('div', 'help-tour-controls', footer);
    const skip = makeElement('button', 'help-tour-skip', controls);
    skip.type = 'button';
    skip.textContent = 'Salir';
    const previous = makeElement('button', 'help-tour-previous', controls);
    previous.type = 'button';
    previous.textContent = 'Anterior';
    const next = makeElement('button', 'help-tour-next', controls);
    next.type = 'button';

    // Estado de la guía en curso; position/onScroll/onKeydown se asignan más abajo.
    activeTour = {
        trigger,
        steps: availableSteps,
        index: 0,
        shades,
        ring,
        card,
        position: null,
        onScroll: null,
        positionFrame: null,
        scrollEndTimer: null,
        onKeydown: null,
    };

    /**
     * Reposiciona los paneles oscuros, el anillo y la tarjeta según el
     * rectángulo del objetivo del paso actual.
     */
    function position() {
        if (!activeTour) return;
        const step = activeTour.steps[activeTour.index];
        const target = document.querySelector(step.target);
        if (!target) {
            closeHelpTour();
            return;
        }

        // Recorta el rectángulo resaltado al viewport, con 8px de margen.
        const rect = target.getBoundingClientRect();
        const padding = 8;
        const left = Math.max(0, rect.left - padding);
        const top = Math.max(0, rect.top - padding);
        const right = Math.min(window.innerWidth, rect.right + padding);
        const bottom = Math.min(window.innerHeight, rect.bottom + padding);
        const width = Math.max(0, right - left);
        const height = Math.max(0, bottom - top);

        // Paneles oscuros alrededor del hueco resaltado y anillo sobre el objetivo.
        Object.assign(shades[0].style, { left: '0px', top: '0px', width: '100vw', height: `${top}px` });
        Object.assign(shades[1].style, { left: '0px', top: `${top}px`, width: `${left}px`, height: `${height}px` });
        Object.assign(shades[2].style, { left: `${right}px`, top: `${top}px`, width: `${Math.max(0, window.innerWidth - right)}px`, height: `${height}px` });
        Object.assign(shades[3].style, { left: '0px', top: `${bottom}px`, width: '100vw', height: `${Math.max(0, window.innerHeight - bottom)}px` });
        Object.assign(ring.style, { left: `${left}px`, top: `${top}px`, width: `${width}px`, height: `${height}px` });

        // En móvil la tarjeta se fija por CSS (clase -mobile); en escritorio se coloca junto al objetivo.
        if (window.innerWidth <= 640) {
            card.classList.add('help-tour-card-mobile');
            card.style.left = '';
            card.style.top = '';
            card.style.bottom = '';
        } else {
            card.classList.remove('help-tour-card-mobile');
            const cardRect = card.getBoundingClientRect();
            const cardWidth = cardRect.width || 360;
            const cardHeight = cardRect.height || 180;
            const cardLeft = Math.max(16, Math.min(rect.left, window.innerWidth - cardWidth - 16));
            const spaceBelow = window.innerHeight - rect.bottom - 16;
            const spaceAbove = rect.top - 16;
            // Prefiere ubicar la tarjeta debajo; si no cabe y hay más espacio arriba, la sube. Siempre dentro del viewport.
            const top = spaceBelow >= cardHeight || spaceBelow >= spaceAbove
                ? rect.bottom + 14
                : rect.top - cardHeight - 14;
            card.style.left = `${Math.min(cardLeft, Math.max(16, window.innerWidth - cardWidth - 16))}px`;
            card.style.top = `${Math.max(16, Math.min(top, window.innerHeight - cardHeight - 16))}px`;
            card.style.bottom = '';
        }
    }

    /** Agrupa reposicionamientos en un solo requestAnimationFrame para no recalcular en cada evento. */
    function schedulePosition() {
        if (!activeTour || activeTour.positionFrame) return;

        activeTour.positionFrame = window.requestAnimationFrame(() => {
            if (!activeTour) return;
            activeTour.positionFrame = null;
            position();
        });
    }

    /** Se ejecuta al terminar el scroll: reposiciona y muestra la tarjeta con su transición. */
    function finishScroll() {
        if (!activeTour) return;
        activeTour.scrollEndTimer = null;
        position();
        card.classList.add('help-tour-card-visible');
    }

    // Durante el scroll reposiciona por frame; 140 ms sin scroll se considera fin del desplazamiento.
    activeTour.onScroll = () => {
        schedulePosition();
        if (activeTour.scrollEndTimer) {
            window.clearTimeout(activeTour.scrollEndTimer);
        }
        activeTour.scrollEndTimer = window.setTimeout(finishScroll, 140);
    };

    /** Muestra el paso actual: actualiza textos, contador y botones, y desplaza la página al objetivo si hace falta. */
    function showStep() {
        const step = activeTour.steps[activeTour.index];
        const target = document.querySelector(step.target);
        title.textContent = step.title;
        body.textContent = step.body;
        count.textContent = `${activeTour.index + 1} / ${activeTour.steps.length}`;
        previous.disabled = activeTour.index === 0;
        next.textContent = activeTour.index === activeTour.steps.length - 1 ? 'Terminar' : 'Siguiente';
        card.classList.remove('help-tour-card-visible');

        // Si el objetivo ya está a la vista (o hay movimiento reducido) no se anima el scroll.
        const rect = target.getBoundingClientRect();
        const targetIsVisible = rect.top >= 16 && rect.bottom <= window.innerHeight - 16;
        if (targetIsVisible || reducedMotion) {
            if (!targetIsVisible) {
                target.scrollIntoView({ behavior: 'auto', block: 'center', inline: 'nearest' });
            }
            position();
            window.requestAnimationFrame(() => card.classList.add('help-tour-card-visible'));
            return;
        }

        target.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
        position();
        activeTour.scrollEndTimer = window.setTimeout(finishScroll, 140);
    }

    // Navegación: anterior / siguiente (el último paso cierra la guía) / cerrar / salir.
    previous.addEventListener('click', () => {
        if (activeTour.index > 0) {
            activeTour.index -= 1;
            showStep();
        }
    });
    next.addEventListener('click', () => {
        if (activeTour.index === activeTour.steps.length - 1) {
            closeHelpTour();
        } else {
            activeTour.index += 1;
            showStep();
        }
    });
    close.addEventListener('click', closeHelpTour);
    skip.addEventListener('click', closeHelpTour);
    activeTour.position = schedulePosition;
    // Teclado: Escape cierra; Tab/Shift+Tab quedan atrapados entre los botones de la tarjeta (accesibilidad).
    activeTour.onKeydown = (event) => {
        if (event.key === 'Escape') {
            closeHelpTour();
        }
        if (event.key === 'Tab') {
            const focusable = [...card.querySelectorAll('button:not(:disabled)')];
            const first = focusable[0];
            const last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    };
    // Listeners globales (se retiran en closeHelpTour); el scroll usa capture:true para captar el de cualquier contenedor.
    window.addEventListener('keydown', activeTour.onKeydown);
    document.addEventListener('scroll', activeTour.onScroll, { passive: true, capture: true });
    window.addEventListener('resize', activeTour.position);
    showStep();
    close.focus();
}

// Cada botón [data-help-tour] abre la guía con los pasos definidos en su JSON; si es inválido se informa por consola.
helpTourButtons.forEach((button) => {
    button.addEventListener('click', () => {
        try {
            startHelpTour(button, JSON.parse(button.dataset.helpTour));
        } catch (error) {
            console.error('No se pudo iniciar la guía de esta pantalla.', error);
        }
    });
});
