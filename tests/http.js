const {spawn} = require('node:child_process');
const assert = require('node:assert/strict');
const path = require('node:path');
const port = Number(process.env.HIE_TEST_PORT || 18769);
const origin = `http://127.0.0.1:${port}`;
const server = spawn(process.argv[2] || 'php', ['-S', `127.0.0.1:${port}`, '-t', path.resolve(__dirname, '..')], {stdio: 'ignore', windowsHide: true});
let spawnError;
server.on('error', error => {spawnError = error;});
let cookie = '', checks = 0;
async function get(route) {
    const response = await fetch(origin + route, {headers: cookie ? {cookie} : {}});
    const session = response.headers.get('set-cookie');
    if (session) cookie = session.split(';')[0];
    return {response, text: await response.text()};
}
function page(text) {
    const config = JSON.parse(text.match(/id="hiequeryui-config">(.*?)<\/script>/s)[1]);
    const source = text.match(/<button type="button" id="([^"]+)"/)[1];
    return {config, source};
}
async function post(route, info, event = 'onClick', overrides = {}) {
    const response = await fetch(origin + route, {method: 'POST', headers: {cookie},
        body: new URLSearchParams({source: info.source, event, data: '', csrf: info.config.csrf, aj: 'an', ...overrides})});
    return {status: response.status, text: await response.text()};
}
(async () => {
    let initial;
    for (let i = 0; i < 30; i++) {
        if (spawnError) throw spawnError;
        try { initial = await get('/examples/index.php'); break; } catch {}
        await new Promise(resolve => setTimeout(resolve, 100));
    }
    assert(initial, 'PHP test server did not start'); checks++;
    assert.equal(initial.response.status, 200); checks++;
    const info = page(initial.text);
    const accepted = await post('/examples/index.php', info);
    assert.equal(accepted.status, 200); checks++;
    const updates = JSON.parse(accepted.text)[0];
    assert.match(Buffer.from(updates[info.source], 'base64').toString(), /Clicks: 1/); checks++;
    assert.equal((await post('/examples/index.php', info, 'show')).status, 400); checks++;
    assert.equal((await post('/examples/index.php', info, 'onClick', {csrf: 'invalid'})).status, 403); checks++;
    assert.equal((await post('/examples/index.php', info, 'onClick', {source: 'expired'})).status, 410); checks++;
    const getEvent = await get('/examples/index.php?source=' + info.source + '&event=onClick&data=');
    assert.equal(getEvent.response.status, 405); checks++;
    const reloaded = await get('/examples/index.php');
    assert.match(reloaded.text, /Clicks: 1/); checks++;
    const runtime = await get('/assets/hiequeryui.js'); assert.equal(runtime.response.status, 200); checks++;
    for (const route of ['/examples/full.php', '/examples/embedded.php']) {
        const html = await get(route); assert.equal(html.response.status, 200); checks++;
        const result = await post(route, page(html.text));
        assert.equal(result.status, 200); checks++;
        assert(!result.text.includes('Fatal error')); checks++;
        if (route.includes('full')) assert.match(result.text, /Clicks: 1/);
        else assert.match(Buffer.from(Object.values(JSON.parse(result.text)[0])[0], 'base64').toString(), /Clicks: 1/);
        checks++;
    }
    cookie = '';
    await get('/examples/index.php');
    assert.equal((await post('/examples/index.php', info)).status, 403); checks++;
    console.log(`PASS ${checks} HTTP/session checks`);
})().catch(error => {console.error(error); process.exitCode = 1;}).finally(() => server.kill());
