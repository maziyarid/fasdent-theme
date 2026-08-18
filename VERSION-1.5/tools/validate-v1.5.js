#!/usr/bin/env node
'use strict';

import fs from 'node:fs';
import path from 'node:path';
import { fileURLToPath } from 'node:url';
const __dirname = path.dirname(fileURLToPath(import.meta.url));
const root = path.resolve(__dirname, '../..');
const jsRoot = path.join(root, 'VERSION-1.5/Theme/assets/js');
const files = [];

function walk(dir) {
  if (!fs.existsSync(dir)) return;
  for (const entry of fs.readdirSync(dir, { withFileTypes: true })) {
    const full = path.join(dir, entry.name);
    if (entry.isDirectory()) walk(full);
    else if (entry.name.endsWith('.js')) files.push(full);
  }
}

walk(jsRoot);
if (!files.length) {
  console.error('FAIL: no JavaScript files found');
  process.exit(1);
}
console.log(`Found ${files.length} JavaScript file(s).`);
console.log('Run `node --check <file>` for each file in CI or staging.');
console.log('NOTE: browser console, network, cache, and production behavior remain environment-dependent.');
