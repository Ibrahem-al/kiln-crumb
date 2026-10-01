// Lighthouse scores for each page, mobile and desktop.
// Fails if any category drops below the budget.
//
// Usage:  BASE=http://localhost:8080 node lighthouse.mjs
// Note: run against the real host before launch; local servers skew performance.

import lighthouse from 'lighthouse';
import * as chromeLauncher from 'chrome-launcher';
import fs from 'node:fs';

const BASE = (process.env.BASE || 'http://localhost:8080').replace(/\/$/, '');
const PAGES = (process.env.PAGES || '/,/wholesale/').split(',');
const BUDGET = { performance: 90, accessibility: 100, 'best-practices': 95, seo: 100 };
const chromePath = process.env.CHROMIUM_PATH || (fs.existsSync('/opt/pw-browsers/chromium') ? '/opt/pw-browsers/chromium' : undefined);

const chrome = await chromeLauncher.launch({ chromePath, chromeFlags: ['--headless=new', '--no-sandbox'] });
const rows = [];
let failed = false;

for (const path of PAGES) {
  for (const formFactor of ['mobile', 'desktop']) {
    const config = formFactor === 'desktop'
      ? { extends: 'lighthouse:default', settings: { formFactor: 'desktop', screenEmulation: { mobile: false, width: 1350, height: 940, deviceScaleFactor: 1, disabled: false }, throttling: { rttMs: 40, throughputKbps: 10240, cpuSlowdownMultiplier: 1 } } }
      : undefined;
    const { lhr } = await lighthouse(BASE + path, { port: chrome.port, output: 'json', logLevel: 'error' }, config);
    const scores = Object.fromEntries(Object.entries(lhr.categories).map(([k, v]) => [k, Math.round(v.score * 100)]));
    const lcp = lhr.audits['largest-contentful-paint'].displayValue;
    const cls = lhr.audits['cumulative-layout-shift'].displayValue;
    const tbt = lhr.audits['total-blocking-time'].displayValue;
    rows.push({ path, formFactor, ...scores, lcp, cls, tbt });
    for (const [k, min] of Object.entries(BUDGET)) if (scores[k] < min) failed = true;
  }
}
await chrome.kill();

console.table(rows);
fs.writeFileSync('lighthouse-report.json', JSON.stringify(rows, null, 2));
if (failed) {
  console.log(`Below budget ${JSON.stringify(BUDGET)}`);
  process.exit(1);
}
console.log('All pages within budget:', JSON.stringify(BUDGET));
