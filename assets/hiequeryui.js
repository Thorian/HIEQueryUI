'use strict';
(() => {
    const configNode = document.getElementById('hiequeryui-config');
    if (!configNode) return;
    const config = JSON.parse(configNode.textContent);
    const timers = new Map();
    let pending = Promise.resolve();
    const encode = text => {
        const bytes = new TextEncoder().encode(text);
        let binary = '';
        for (const byte of bytes) binary += String.fromCharCode(byte);
        return btoa(binary);
    };
    const decode = text => new TextDecoder('utf-8', {fatal: true}).decode(Uint8Array.from(atob(text), c => c.charCodeAt(0)));
    window.Base64 = {encode, decode};

    function report(error) {
        document.dispatchEvent(new CustomEvent('hiequeryui:error', {detail: error}));
        let banner = document.getElementById('hiequeryui-error');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'hiequeryui-error';
            banner.setAttribute('role', 'alert');
            document.body.prepend(banner);
        }
        banner.textContent = error.message + ' ';
        const reload = document.createElement('button');
        reload.type = 'button'; reload.textContent = 'Reload';
        reload.addEventListener('click', () => window.location.reload());
        banner.append(reload);
    }

    async function send(source, event, value) {
        const body = new URLSearchParams({source, event, data: encode(value), csrf: config.csrf});
        if (config.mode === 'full') {
            const form = document.createElement('form');
            form.method = 'post'; form.action = config.endpoint;
            for (const [name, value] of body) {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = name; input.value = value; form.append(input);
            }
            document.body.append(form); form.submit();
            return;
        }
        body.set('aj', 'an');
        const response = await fetch(config.endpoint, {method: 'POST', credentials: 'same-origin', body});
        const updates = await response.json();
        if (!response.ok) throw new Error(updates.error?.message || 'The widget request failed.');
        const active = document.activeElement;
        const focus = active?.id ? {id: active.id, start: active.selectionStart, end: active.selectionEnd, direction: active.selectionDirection} : null;
        for (const item of updates) for (const [id, markup] of Object.entries(item)) {
            const target = document.getElementById(id);
            if (target) target.outerHTML = decode(markup);
        }
        if (focus) {
            const replacement = document.getElementById(focus.id);
            if (replacement && replacement !== active) {
                replacement.focus({preventScroll: true});
                if (typeof focus.start === 'number' && replacement.setSelectionRange) {
                    replacement.setSelectionRange(focus.start, focus.end, focus.direction);
                }
            }
        }
        document.getElementById('hiequeryui-error')?.remove();
        document.dispatchEvent(new CustomEvent('hiequeryui:updated'));
    }

    window.eventcall = (source, action) => {
        const element = document.getElementById(source);
        if (!element) return Promise.resolve();
        // Capture at click/typing time, before an earlier request can replace the element.
        const value = element.type === 'checkbox' ? (element.checked ? '1' : '') : String(element.value ?? '');
        pending = pending.then(() => send(source, action, value)).catch(report);
        return pending;
    };
    window.deventcall = (source, action, event) => {
        clearTimeout(timers.get(source));
        timers.delete(source);
        if (event?.isComposing) return;
        if (action === 'onEnter' || event?.key === 'Enter') return window.eventcall(source, 'onEnter');
        timers.set(source, setTimeout(() => {
            timers.delete(source); window.eventcall(source, action);
        }, 350));
    };
})();
