// Confirms every theme pattern, template and template part parses as VALID
// block markup in the real WordPress editor. AI-written block markup often
// renders fine on the front end but shows "This block contains unexpected or
// invalid content" to the client in the editor; this catches that.
//
// Usage: BASE=http://localhost:8080 WP_USER=admin WP_PASS=admin node validate-blocks.mjs
import { chromium } from 'playwright';
import fs from 'node:fs';
const BASE = (process.env.BASE || 'http://localhost:8080').replace(/\/$/, '');
const EXEC = process.env.CHROMIUM_PATH || (fs.existsSync('/opt/pw-browsers/chromium') ? '/opt/pw-browsers/chromium' : undefined);
const b = await chromium.launch({ executablePath: EXEC });
const p = await b.newPage({ viewport: { width: 1400, height: 900 } });
await p.goto(BASE + '/wp-login.php');
await p.fill('#user_login', process.env.WP_USER || 'admin'); await p.fill('#user_pass', process.env.WP_PASS || 'admin'); await p.click('#wp-submit');
await p.waitForLoadState('networkidle');
await p.goto(BASE + '/wp-admin/post-new.php?post_type=page', { waitUntil: 'domcontentloaded', timeout: 120000 });
await p.waitForFunction(() => window.wp && wp.blocks && wp.blocks.getBlockTypes().length > 50, null, { timeout: 120000 });
const out = await p.evaluate(async () => {
  const pats = await wp.apiFetch({ path: '/wp/v2/block-patterns/patterns' });
  const mine = pats.filter(x => x.name.startsWith('kiln-crumb/'));
  const res = [];
  const walk = (blocks, name) => { for (const bl of blocks) { if (!bl.isValid) res.push(name + ': INVALID ' + bl.name + ' ' + JSON.stringify((bl.validationIssues||[]).map(i=>i.args?.slice?.(0,3)))); walk(bl.innerBlocks, name); } };
  for (const pt of mine) walk(wp.blocks.parse(pt.content), pt.name);
  // templates and parts too
  const tpls = await wp.apiFetch({ path: '/wp/v2/templates?per_page=100' });
  const parts = await wp.apiFetch({ path: '/wp/v2/template-parts?per_page=100' });
  for (const t of [...tpls, ...parts].filter(t => t.theme === 'kiln-crumb')) walk(wp.blocks.parse(t.content.raw), t.slug);
  return { patterns: mine.map(m => m.name), templates: tpls.filter(t=>t.theme==='kiln-crumb').length, parts: parts.filter(t=>t.theme==='kiln-crumb').length, issues: res };
});
await b.close();
if (out.issues.length) { console.log(out.issues.join('\n')); process.exit(1); }
console.log(`All blocks valid: ${out.patterns.length} patterns, ${out.templates} templates, ${out.parts} template parts.`);
