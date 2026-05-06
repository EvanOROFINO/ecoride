// Convertit tous les .md du dossier docs/ en HTML stylé prêt pour impression PDF
const fs = require('fs');
const path = require('path');
const { marked } = require('marked');

const docsRoot = path.join(__dirname, '..', 'docs');
const outDir = path.join(docsRoot, 'pdf');
if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

const cssTheme = `
@page { size: A4; margin: 1.5cm; }
* { box-sizing: border-box; }
body {
  font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
  line-height: 1.6;
  color: #212121;
  max-width: 100%;
  margin: 0;
  padding: 1rem 2rem;
  font-size: 11pt;
}
h1 { color: #2E7D32; border-bottom: 3px solid #2E7D32; padding-bottom: .4rem; font-size: 22pt; }
h2 { color: #2E7D32; margin-top: 1.8rem; font-size: 16pt; border-bottom: 1px solid #C8E6C9; padding-bottom: .25rem; }
h3 { color: #424242; margin-top: 1.2rem; font-size: 13pt; }
h4 { color: #616161; }
p, li { line-height: 1.65; }
code { background: #F5F5F5; padding: 1px 6px; border-radius: 3px; font-family: 'Consolas', 'Monaco', monospace; font-size: 10pt; color: #C62828; }
pre { background: #F5F5F5; padding: 1rem; border-radius: 6px; border-left: 4px solid #2E7D32; overflow-x: auto; font-size: 9pt; }
pre code { background: transparent; padding: 0; color: #212121; }
table { border-collapse: collapse; width: 100%; margin: 1rem 0; font-size: 10pt; }
th { background: #2E7D32; color: white; padding: .5rem .75rem; text-align: left; }
td { padding: .4rem .75rem; border-bottom: 1px solid #E0E0E0; }
tr:nth-child(even) td { background: #FAFAFA; }
blockquote { border-left: 4px solid #66BB6A; background: #E8F5E9; padding: .75rem 1rem; margin: 1rem 0; font-style: italic; }
a { color: #2E7D32; text-decoration: none; }
strong { color: #1B5E20; }
hr { border: none; border-top: 2px solid #E0E0E0; margin: 2rem 0; }
ul, ol { padding-left: 1.5rem; }
.toc { background: #F5F7F4; padding: 1rem 1.5rem; border-radius: 6px; margin-bottom: 2rem; }
`;

function findMarkdownFiles(dir, files = []) {
  for (const item of fs.readdirSync(dir)) {
    const full = path.join(dir, item);
    const stat = fs.statSync(full);
    if (stat.isDirectory() && item !== 'pdf' && item !== 'node_modules') {
      findMarkdownFiles(full, files);
    } else if (item.endsWith('.md')) {
      files.push(full);
    }
  }
  return files;
}

const files = findMarkdownFiles(docsRoot);
console.log(`Conversion de ${files.length} fichier(s) Markdown en HTML...`);

for (const mdPath of files) {
  const md = fs.readFileSync(mdPath, 'utf-8');
  const htmlBody = marked.parse(md);
  const title = path.basename(mdPath, '.md').replace(/_/g, ' ');

  const html = `<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>${title} — EcoRide</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
  <style>${cssTheme}</style>
</head>
<body>
${htmlBody}
</body>
</html>`;

  const relPath = path.relative(docsRoot, mdPath).replace(/\.md$/, '.html').replace(/[\\\/]/g, '_');
  const outPath = path.join(outDir, relPath);
  fs.writeFileSync(outPath, html, 'utf-8');
  console.log(`  ${path.relative(docsRoot, mdPath)} -> pdf/${path.basename(outPath)}`);
}

console.log('\nHTML genere. Conversion en PDF avec Edge headless...');
