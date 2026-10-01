// Pre-launch audit for the Kiln & Crumb site.
//
// Runs every page at six viewport widths and fails (exit code 1) on:
//   - horizontal overflow (anything wider than the viewport)
//   - axe-core WCAG 2.2 A/AA violations
//   - tap targets under 24x24px (WCAG 2.5.8), except links inside running text
//   - JavaScript console errors
//   - missing/duplicate h1 or skipped heading levels
// and once per page for SEO / answer-engine basics:
//   - <title>, meta description length, canonical, lang, absolute og:image
//   - valid JSON-LD on the home page with a LocalBusiness-type node
//   - robots.txt with a Sitemap line, a reachable sitemap, and /llms.txt
// plus a keyboard test of the mobile menu.
//
// Usage:  BASE=http://localhost:8080 node audit.mjs
//         BASE=https://your-live-site.com node audit.mjs --shots

import { chromium } from 'playwright';
import AxeBuilder from '@axe-core/playwright';
import fs from 'node:fs';

const BASE = (process.env.BASE || 'http://localhost:8080').replace(/\/$/, '');
const PAGES = (process.env.PAGES || '/,/wholesale/,/does-not-exist/').split(',');
const WIDTHS = [320, 375, 768, 1024, 1280, 1440];
const SHOTS = process.argv.includes('--shots');
const EXEC = process.env.CHROMIUM_PATH || (fs.existsSync('/opt/pw-browsers/chromium') ? '/opt/pw-browsers/chromium' : undefined);

const findings = [];
const fail = (page, width, check, detail) => findings.push({ page, width, check, detail });

const browser = await chromium.launch({ executablePath: EXEC });

for (const path of PAGES) {
  for (const width of WIDTHS) {
    const ctx = await browser.newContext({ viewport: { width, height: 900 } });
    const page = await ctx.newPage();
    const errors = [];
    page.on('console', (m) => {
      // The 404 test page logs its own 404 status; that is the test passing, not a defect.
      if (m.type() === 'error' && !(path.includes('does-not-exist') && /status of 404/.test(m.text()))) errors.push(m.text());
    });
    page.on('pageerror', (e) => errors.push(e.message));

    const res = await page.goto(BASE + path, { waitUntil: 'networkidle' });
    const is404 = path.includes('does-not-exist');
    if (is404 && res.status() !== 404) fail(path, width, 'status', `expected 404, got ${res.status()}`);
    if (!is404 && res.status() !== 200) fail(path, width, 'status', `HTTP ${res.status()}`);

    // Horizontal overflow: list the widest offenders so the fix is obvious.
    const overflow = await page.evaluate(() => {
      const vw = document.documentElement.clientWidth;
      if (document.documentElement.scrollWidth <= vw) return [];
      return [...document.querySelectorAll('body *')]
        .filter((el) => el.getBoundingClientRect().right > vw + 1)
        .slice(0, 5)
        .map((el) => `${el.tagName.toLowerCase()}.${[...el.classList].join('.')} right=${Math.round(el.getBoundingClientRect().right)}`);
    });
    if (overflow.length) fail(path, width, 'overflow', overflow.join(' | '));

    // Accessibility (axe-core, WCAG 2.0/2.1/2.2 A + AA).
    const axe = await new AxeBuilder({ page }).withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa']).analyze();
    for (const v of axe.violations) {
      fail(path, width, `axe:${v.id}`, `${v.impact}: ${v.help} (${v.nodes.length}x) e.g. ${v.nodes[0].target.join(' ')}`);
    }

    // Tap targets: 24px minimum, with WCAG's exemption for links inside a sentence.
    const small = await page.evaluate(() => {
      const out = [];
      for (const el of document.querySelectorAll('a[href], button, summary, input, select, textarea')) {
        const r = el.getBoundingClientRect();
        const st = getComputedStyle(el);
        if (!r.width || !r.height || st.visibility === 'hidden') continue;
        if (el.closest('.screen-reader-text, [aria-hidden="true"]')) continue;
        if (r.width >= 24 && r.height >= 24) continue;
        const parent = el.parentElement;
        const inSentence = el.tagName === 'A' && parent && [...parent.childNodes].some((n) => n.nodeType === 3 && n.textContent.trim());
        if (inSentence) continue;
        out.push(`${el.tagName.toLowerCase()} "${(el.textContent || el.getAttribute('aria-label') || '').trim().slice(0, 30)}" ${Math.round(r.width)}x${Math.round(r.height)}`);
      }
      return out;
    });
    if (small.length) fail(path, width, 'tap-target', small.slice(0, 5).join(' | '));

    // Heading structure.
    const headings = await page.evaluate(() =>
      [...document.querySelectorAll('h1,h2,h3,h4,h5,h6')].filter((h) => h.offsetParent !== null || h.getClientRects().length).map((h) => Number(h.tagName[1])));
    const h1s = headings.filter((l) => l === 1).length;
    if (h1s !== 1) fail(path, width, 'h1', `${h1s} h1 elements`);
    headings.forEach((l, i) => { if (i && l > headings[i - 1] + 1) fail(path, width, 'heading-order', `h${headings[i - 1]} -> h${l}`); });

    if (errors.length) fail(path, width, 'console', errors.slice(0, 3).join(' | '));

    if (SHOTS) {
      fs.mkdirSync('shots', { recursive: true });
      await page.screenshot({ path: `shots/${path.replace(/\W+/g, '_') || 'home'}-${width}.png`, fullPage: true });
    }

    // SEO checks once per page, at desktop width.
    if (width === 1280 && !is404) {
      const seo = await page.evaluate(() => ({
        title: document.title,
        desc: document.querySelector('meta[name="description"]')?.content || '',
        canonical: document.querySelector('link[rel="canonical"]')?.href || '',
        lang: document.documentElement.lang,
        og: document.querySelector('meta[property="og:image"]')?.content || '',
        jsonld: [...document.querySelectorAll('script[type="application/ld+json"]')].map((s) => s.textContent),
      }));
      if (!seo.title || seo.title.length > 65) fail(path, width, 'seo:title', `"${seo.title}" (${seo.title.length} chars)`);
      if (seo.desc.length < 50 || seo.desc.length > 165) fail(path, width, 'seo:description', `${seo.desc.length} chars`);
      if (!seo.canonical) fail(path, width, 'seo:canonical', 'missing');
      if (!seo.lang) fail(path, width, 'seo:lang', 'missing <html lang>');
      if (!/^https?:\/\//.test(seo.og)) fail(path, width, 'seo:og:image', `not absolute: "${seo.og}"`);
      if (path === '/') {
        let types = [];
        try {
          for (const raw of seo.jsonld) {
            const data = JSON.parse(raw);
            for (const node of data['@graph'] || [data]) types.push(node['@type']);
          }
        } catch (e) { fail(path, width, 'seo:json-ld', `invalid JSON: ${e.message}`); }
        const local = ['LocalBusiness', 'Bakery', 'Restaurant', 'Store', 'ProfessionalService'];
        if (!types.some((t) => local.includes(t))) fail(path, width, 'seo:json-ld', `no LocalBusiness-type node (found: ${types.join(', ') || 'none'})`);
        if (!types.includes('FAQPage')) fail(path, width, 'seo:json-ld', 'no FAQPage node');
      }
    }
    await ctx.close();
  }
}

// Site-wide files crawlers and AI answer engines read.
const ctx = await browser.newContext();
const robots = await ctx.request.get(BASE + '/robots.txt');
const robotsText = await robots.text();
if (!robots.ok()) fail('/robots.txt', '-', 'status', robots.status());
const sitemapUrl = (robotsText.match(/^Sitemap:\s*(\S+)/im) || [])[1];
if (!sitemapUrl) fail('/robots.txt', '-', 'seo:sitemap', 'no Sitemap: line');
else {
  const sm = await ctx.request.get(sitemapUrl);
  if (!sm.ok()) fail(sitemapUrl, '-', 'seo:sitemap', `HTTP ${sm.status()}`);
}
const llms = await ctx.request.get(BASE + '/llms.txt');
if (!llms.ok() || !(await llms.text()).startsWith('# ')) fail('/llms.txt', '-', 'aeo:llms.txt', `HTTP ${llms.status()} or not in llms.txt format`);
await ctx.close();

// Keyboard test of the mobile menu: open with Enter, links reachable, Escape closes.
{
  const ctx2 = await browser.newContext({ viewport: { width: 375, height: 800 } });
  const page = await ctx2.newPage();
  await page.goto(BASE + '/', { waitUntil: 'networkidle' });
  const opener = page.locator('.wp-block-navigation__responsive-container-open').first();
  if (!(await opener.isVisible())) fail('/', 375, 'mobile-menu', 'menu button not visible');
  else {
    await opener.focus();
    await page.keyboard.press('Enter');
    const menu = page.locator('.wp-block-navigation__responsive-container.is-menu-open');
    if (!(await menu.isVisible())) fail('/', 375, 'mobile-menu', 'Enter did not open the menu');
    const links = await menu.locator('a').count();
    if (links < 3) fail('/', 375, 'mobile-menu', `only ${links} links in open menu`);
    await page.keyboard.press('Escape');
    if (await menu.isVisible()) fail('/', 375, 'mobile-menu', 'Escape did not close the menu');
  }
  await ctx2.close();
}

await browser.close();

const checked = PAGES.length * WIDTHS.length;
fs.writeFileSync('audit-report.json', JSON.stringify({ base: BASE, pages: PAGES, widths: WIDTHS, findings }, null, 2));
if (findings.length) {
  console.log(`\n${findings.length} finding(s) across ${checked} page/viewport runs:\n`);
  for (const f of findings) console.log(`  ${f.page} @${f.width}px  [${f.check}]  ${f.detail}`);
  process.exit(1);
}
console.log(`0 findings across ${PAGES.length} pages x ${WIDTHS.length} viewports (${checked} runs), plus robots.txt, sitemap, llms.txt and mobile-menu keyboard test.`);
