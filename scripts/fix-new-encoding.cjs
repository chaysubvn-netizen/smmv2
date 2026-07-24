const fs = require('fs');
const path = 'src/app/new/page.tsx';
const decoder = new TextDecoder('windows-1252');
const reverse = new Map();
for (let byte = 0; byte < 256; byte++) reverse.set(decoder.decode(Uint8Array.of(byte)), byte);

function decodeOnce(value) {
  const bytes = [];
  for (const char of value) {
    if (reverse.has(char)) bytes.push(reverse.get(char));
    else bytes.push(...Buffer.from(char, 'utf8'));
  }
  return Buffer.from(bytes).toString('utf8');
}

function score(value) {
  return (value.match(/Ã|Â|Æ|â€|áº|á»|Ä/g) || []).length;
}

let source = fs.readFileSync(path, 'utf8');
source = source.split(/\r?\n/).map(line => {
  let current = line;
  for (let pass = 0; pass < 10; pass++) {
    const next = decodeOnce(current);
    if (next.includes('\uFFFD') || score(next) >= score(current)) break;
    current = next;
  }
  return current;
}).join('\n');
fs.writeFileSync(path, source, 'utf8');
