#!/usr/bin/env python3
"""Export the running WordPress site as static files for Vercel.

Vercel can't run PHP, so the public demo is a static snapshot of the HTML
WordPress renders. The theme source (theme/kiln-crumb) stays the real
product; re-run this after changing it.

Usage:
    python3 scripts/export-static.py --src http://localhost:8080 \
        --dest site --public-url https://kiln-crumb.vercel.app

What it does:
  - fetches each page, the 404 page, robots.txt, llms.txt and the XML sitemaps
  - downloads every same-site asset they reference (CSS, JS modules, fonts,
    images), dropping ?ver= query strings, which static hosts ignore
  - rewrites the local origin to the public URL so canonicals, Open Graph
    images and JSON-LD point at the live site
  - removes head links to things a static host can't serve (REST API,
    feeds, oEmbed, XML-RPC)
"""
import argparse
import os
import re
import shutil
import sys
import urllib.error
import urllib.request
from urllib.parse import urlsplit

PAGES = ['/', '/wholesale/']
TEXT_FILES = ['/robots.txt', '/llms.txt', '/wp-sitemap.xml']
NOT_FOUND = '/this-page-does-not-exist-404/'

# Head links that point at dynamic WordPress endpoints.
DEAD_LINK_RE = re.compile(
    r'<link[^>]+(?:/feed/|/wp-json/|xmlrpc\.php|oembed|rel="EditURI")[^>]*>\s*', re.I)


def fetch(url):
    try:
        with urllib.request.urlopen(url, timeout=60) as r:
            return r.status, r.read()
    except urllib.error.HTTPError as e:
        return e.code, e.read()


def out_path(dest, path):
    path = urlsplit(path).path
    if path.endswith('/'):
        path += 'index.html'
    return os.path.join(dest, path.lstrip('/'))


def save(dest, path, data):
    target = out_path(dest, path)
    os.makedirs(os.path.dirname(target), exist_ok=True)
    with open(target, 'wb') as f:
        f.write(data)
    return target


def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('--src', default='http://localhost:8080')
    ap.add_argument('--dest', default='site')
    ap.add_argument('--public-url', required=True)
    args = ap.parse_args()
    src = args.src.rstrip('/')
    public = args.public_url.rstrip('/')
    host = urlsplit(src).netloc

    if os.path.isdir(args.dest):
        shutil.rmtree(args.dest)
    os.makedirs(args.dest)

    # Any reference to the local site, quoted with " ' ( or inside JSON (\/ escaped).
    url_re = re.compile(r'(?:https?:)?(?:\\?/){2}' + re.escape(host) + r'([^"\'\s)<>\\]*(?:\\/[^"\'\s)<>\\]*)*)')

    queue = list(PAGES) + list(TEXT_FILES)
    seen = set()
    texts = {}  # path -> decoded text, rewritten at the end
    failures = []

    while queue:
        path = queue.pop(0).replace('\\/', '/')
        clean = urlsplit(path).path or '/'
        if clean in seen:
            continue
        seen.add(clean)
        status, body = fetch(src + path)
        if status != 200:
            failures.append(f'{status} {path}')
            continue
        is_text = clean.endswith(('/', '.html', '.css', '.js', '.txt', '.xml', '.xsl', '.json', '.svg'))
        if is_text:
            text = body.decode('utf-8')
            texts[clean] = text
            for m in url_re.finditer(text):
                ref = m.group(1).replace('\\/', '/')
                ref_path = urlsplit(ref).path or '/'
                if ref_path in seen:
                    continue
                # Follow assets and sitemaps; only crawl the pages we listed.
                if ref_path.startswith(('/wp-content/', '/wp-includes/')) or 'sitemap' in ref_path:
                    queue.append(ref_path)
        else:
            save(args.dest, clean, body)

    # 404 page: WordPress returns status 404 with the themed page.
    status, body = fetch(src + NOT_FOUND)
    texts['/404.html'] = body.decode('utf-8')

    for path, text in texts.items():
        if path.endswith(('/', '.html')):
            text = DEAD_LINK_RE.sub('', text)
        # Drop ?ver= cache-busters on local URLs, then swap origin.
        text = re.sub(r'(' + re.escape(host) + r'[^"\'\s)<>]*?)\?ver=[\w.\-]+', r'\1', text)
        text = re.sub(r'(' + re.escape(host) + r'[^"\'\s)<>]*?)(?:&#038;|&amp;|&)ver=[\w.\-]+', r'\1', text)
        text = text.replace('http:\\/\\/' + host, public.replace('/', '\\/'))
        text = text.replace('http://' + host, public).replace('//' + host, '//' + urlsplit(public).netloc)
        texts[path] = text
        save(args.dest, path, text.encode('utf-8'))

    leftover = [p for p, t in texts.items() if host in t]
    print(f'Exported {len(seen)} URLs + 404 page to {args.dest}/ for {public}')
    if failures:
        print('Not exported (expected for optional files):', *failures, sep='\n  ')
    if leftover:
        print('WARNING: local host still referenced in', leftover)
        sys.exit(1)


if __name__ == '__main__':
    main()
