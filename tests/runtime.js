const fs = require('node:fs');
const vm = require('node:vm');
const assert = require('node:assert/strict');
const code = fs.readFileSync(__dirname + '/../assets/hiequeryui.js', 'utf8');
let count = 0;
async function test() {
    const requests = [];
    const timers = new Map();
    const elements = new Map([
        ['hiequeryui-config', {textContent: JSON.stringify({endpoint: '/nested/index.php', mode: 'ajax', csrf: 'token'})}],
        ['a', {value: 'Zoë + 🦞 & <text>'}], ['b', {type: 'checkbox', checked: true}]
    ]);
    const document = {getElementById: id => elements.get(id), dispatchEvent() {}};
    const context = {document, window: {}, URLSearchParams, TextEncoder, TextDecoder, Uint8Array,
        btoa: text => Buffer.from(text, 'binary').toString('base64'), atob: text => Buffer.from(text, 'base64').toString('binary'),
        CustomEvent: class {}, clearTimeout: id => timers.delete(id),
        setTimeout: fn => { const id = Symbol(); timers.set(id, fn); return id; },
        fetch: async (url, request) => { requests.push({url, request}); return {ok: true, json: async () => [{}]}; }};
    vm.runInNewContext(code, context);
    const {eventcall, deventcall, Base64} = context.window;
    const value = elements.get('a').value;
    assert.equal(Base64.decode(Base64.encode(value)), value); count++;
    await eventcall('a', 'onChange');
    assert.equal(requests[0].url, '/nested/index.php'); count++;
    assert.equal(requests[0].request.method, 'POST'); count++;
    assert.equal(requests[0].request.body.get('csrf'), 'token'); count++;
    assert.equal(Base64.decode(requests[0].request.body.get('data')), value); count++;
    await eventcall('b', 'onClick');
    assert.equal(Base64.decode(requests[1].request.body.get('data')), '1'); count++;
    deventcall('a', 'onChange', {}); deventcall('b', 'onChange', {});
    assert.equal(timers.size, 2); count++;
    await deventcall('a', 'onEnter', {key: 'Enter'});
    assert.equal(timers.size, 1); count++;
    assert.equal(requests.at(-1).request.body.get('event'), 'onEnter'); count++;
    const first = eventcall('a', 'onChange'); elements.get('a').value = 'second';
    const second = eventcall('a', 'onChange'); await Promise.all([first, second]);
    assert.equal(Base64.decode(requests.at(-2).request.body.get('data')), value); count++;
    assert.equal(Base64.decode(requests.at(-1).request.body.get('data')), 'second'); count++;
    const original = elements.get('a');
    original.id = 'a'; original.selectionStart = 1; original.selectionEnd = 2; original.selectionDirection = 'forward';
    document.activeElement = original;
    let focused = false, selection;
    Object.defineProperty(original, 'outerHTML', {set() {
        elements.set('a', {id: 'a', focus() { focused = true; }, setSelectionRange(...args) { selection = args; }});
    }});
    context.fetch = async () => ({ok: true, json: async () => [{a: Base64.encode('<input id="a">')}]});
    await eventcall('a', 'onChange');
    assert.equal(focused, true); count++;
    assert.equal(JSON.stringify(selection), JSON.stringify([1, 2, 'forward'])); count++;
    console.log(`PASS ${count} browser-runtime checks`);
}
test().catch(error => { console.error(error); process.exitCode = 1; });
