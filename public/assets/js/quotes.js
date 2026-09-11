document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-signature-pad]').forEach(setupSignaturePad);
    document.querySelectorAll('[data-copy-button]').forEach(button => button.addEventListener('click', async () => {
        const input = button.closest('.signing-link-copy').querySelector('[data-copy-value]');
        try { await navigator.clipboard.writeText(input.value); }
        catch { input.select(); document.execCommand('copy'); }
        button.textContent = 'Copiado';
        setTimeout(() => { button.textContent = 'Copiar enlace'; }, 1800);
    }));
    const editor = document.querySelector('[data-quote-items]');
    if (!editor) return;
    const list = editor.querySelector('.quote-items');
    const template = document.querySelector('[data-item-template]');
    const money = value => new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(value || 0);

    function refresh() {
        let subtotal = 0;
        list.querySelectorAll('[data-item]').forEach((row, index) => {
            row.querySelector('[data-item-number]').textContent = index + 1;
            row.querySelectorAll('[data-name]').forEach(field => field.name = `items[${index}][${field.dataset.name}]`);
            const total = (parseFloat(row.querySelector('[data-quantity]').value) || 0) * (parseFloat(row.querySelector('[data-price]').value) || 0);
            row.querySelector('[data-line-total]').textContent = money(total);
            subtotal += total;
        });
        editor.querySelector('[data-materials-total]').textContent = money(subtotal);
        const installation = parseFloat(editor.querySelector('[data-installation]').value) || 0;
        editor.querySelector('[data-grand-total]').textContent = `${money(subtotal + installation)} MXN`;
        list.querySelectorAll('[data-remove-item]').forEach(button => button.disabled = list.children.length === 1);
    }
    editor.addEventListener('input', refresh);
    editor.addEventListener('click', event => {
        if (event.target.closest('[data-add-item]')) {
            list.append(template.content.cloneNode(true)); refresh();
        }
        const remove = event.target.closest('[data-remove-item]');
        if (remove && list.children.length > 1) { remove.closest('[data-item]').remove(); refresh(); }
    });
    refresh();
});

function setupSignaturePad(pad) {
    const canvas = pad.querySelector('[data-signature-canvas]');
    const output = pad.querySelector('[data-signature-data]');
    const context = canvas.getContext('2d');
    let drawing = false;
    let hasSignature = false;

    function resize() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const bounds = canvas.getBoundingClientRect();
        const previous = hasSignature ? canvas.toDataURL() : null;
        canvas.width = Math.round(bounds.width * ratio);
        canvas.height = Math.round(bounds.height * ratio);
        context.setTransform(ratio, 0, 0, ratio, 0, 0);
        context.lineWidth = 2.25;
        context.lineCap = 'round';
        context.lineJoin = 'round';
        context.strokeStyle = '#092548';
        if (previous) {
            const image = new Image();
            image.onload = () => { context.drawImage(image, 0, 0, bounds.width, bounds.height); sync(); };
            image.src = previous;
        }
    }
    function point(event) {
        const bounds = canvas.getBoundingClientRect();
        return { x: event.clientX - bounds.left, y: event.clientY - bounds.top };
    }
    function sync() { output.value = hasSignature ? canvas.toDataURL('image/png') : ''; }
    canvas.addEventListener('pointerdown', event => {
        drawing = true; hasSignature = true; canvas.setPointerCapture(event.pointerId);
        const current = point(event); context.beginPath(); context.moveTo(current.x, current.y);
    });
    canvas.addEventListener('pointermove', event => {
        if (!drawing) return;
        const current = point(event); context.lineTo(current.x, current.y); context.stroke();
    });
    const stop = () => { if (drawing) { drawing = false; context.closePath(); sync(); } };
    canvas.addEventListener('pointerup', stop);
    canvas.addEventListener('pointercancel', stop);
    pad.querySelector('[data-clear-signature]').addEventListener('click', () => {
        context.clearRect(0, 0, canvas.width, canvas.height); hasSignature = false; sync();
    });
    window.addEventListener('resize', resize);
    resize();
}
