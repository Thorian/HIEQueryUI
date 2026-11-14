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
    const labels = await get('/examples/label.php');
    assert.equal(labels.response.status, 200); checks++;
    const inputId = labels.text.match(/<input id="([^"]+)"/)[1];
    assert(labels.text.includes(`for="${inputId}"`)); checks++;
    const labelInfo = {config: JSON.parse(labels.text.match(/id="hiequeryui-config">(.*?)<\/script>/s)[1]), source: inputId};
    const labelChange = await post('/examples/label.php', labelInfo, 'onChange', {data: Buffer.from('<Zoë> 🦞').toString('base64')});
    assert.equal(labelChange.status, 200); checks++;
    const labelUpdates = Object.values(JSON.parse(labelChange.text)[0]).map(value => Buffer.from(value, 'base64').toString());
    assert(labelUpdates.some(value => value.includes('Hello, &lt;Zoë&gt; 🦞'))); checks++;
    assert.match((await get('/examples/label.php')).text, /Hello, &lt;Zoë&gt; 🦞/); checks++;
    const buttons = await get('/examples/button.php');
    assert.equal(buttons.response.status, 200); checks++;
    const buttonIds = [...buttons.text.matchAll(/<button type="button" id="([^"]+)"/g)].map(match => match[1]);
    const buttonConfig = JSON.parse(buttons.text.match(/id="hiequeryui-config">(.*?)<\/script>/s)[1]);
    const buttonInfo = index => ({config: buttonConfig, source: buttonIds[index]});
    assert.equal(buttonIds.length, 3); checks++;
    assert.match(buttons.text, /aria-label="Reset count"/); checks++;
    assert.equal((await post('/examples/button.php', buttonInfo(0))).status, 200); checks++;
    assert.equal((await post('/examples/button.php', buttonInfo(1))).status, 200); checks++;
    assert.equal((await post('/examples/button.php', buttonInfo(0))).status, 400); checks++;
    const disabledButtons = await get('/examples/button.php');
    assert.match(disabledButtons.text, / disabled/); checks++;
    assert.match(disabledButtons.text, /Items: 1/); checks++;
    assert.equal((await post('/examples/button.php', buttonInfo(1))).status, 200); checks++;
    assert.equal((await post('/examples/button.php', buttonInfo(0))).status, 200); checks++;
    assert.match((await get('/examples/button.php')).text, /Items: 2/); checks++;
    assert.equal((await post('/examples/button.php', buttonInfo(2))).status, 200); checks++;
    assert.match((await get('/examples/button.php')).text, /Items: 0/); checks++;
    cookie = '';
    await get('/examples/index.php');
    assert.equal((await post('/examples/index.php', info)).status, 403); checks++;
    console.log(`PASS ${checks} HTTP/session checks`);
})().catch(error => {console.error(error); process.exitCode = 1;}).finally(() => server.kill());
