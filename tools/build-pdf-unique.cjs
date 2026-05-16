// Combine tous les documents Markdown du dossier docs/ en UN SEUL HTML
// Ce HTML est ensuite converti en PDF unique avec Edge headless.
const fs = require('fs');
const path = require('path');
const { marked } = require('marked');

const docsRoot = path.join(__dirname, '..', 'docs');
const outFile = path.join(__dirname, '..', 'docs', 'pdf', '_COPIE_A_RENDRE_complete.html');

// Ordre logique pour le jury
const sections = [
  { file: 'manuel_utilisation.md',                  title: '1. Manuel d\'utilisation' },
  { file: 'charte/charte_graphique.md',             title: '2. Charte graphique' },
  { file: 'maquettes/maquettes.md',                 title: '3. Maquettes haute fidélité' },
  { file: 'technique/documentation_technique.md',   title: '4. Documentation technique' },
  { file: 'technique/mcd.md',                       title: '5. Modèle Conceptuel de Données' },
  { file: 'technique/use-case.md',                  title: '6. Diagramme cas d\'utilisation' },
  { file: 'technique/schema_mongodb.md',            title: '7. Schéma MongoDB' },
  { file: 'gestion_projet.md',                      title: '8. Gestion de projet' },
  { file: 'kanban.md',                              title: '9. Kanban' },
];

const cssTheme = `
@page { size: A4; margin: 1.5cm 1.8cm; }
* { box-sizing: border-box; }
body {
  font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
  line-height: 1.55;
  color: #212121;
  margin: 0;
  padding: 0;
  font-size: 10.5pt;
}

/* COUVERTURE */
.cover {
  page-break-after: always;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  height: 92vh;
  text-align: center;
  background: linear-gradient(135deg, #2E7D32 0%, #66BB6A 100%);
  color: white;
  padding: 4rem 2rem;
}
.cover .leaf { font-size: 5rem; margin-bottom: 1rem; }
.cover h1 { color: white; font-size: 36pt; border: none; margin: 0 0 1rem; font-weight: 700; }
.cover .subtitle { font-size: 16pt; margin-bottom: 3rem; opacity: 0.95; }
.cover .meta { background: rgba(255,255,255,0.15); padding: 1.5rem 2rem; border-radius: 12px; font-size: 12pt; }
.cover .meta strong { color: #FFEB3B; }
.cover .footer { margin-top: 3rem; font-size: 10pt; opacity: 0.85; }

/* SOMMAIRE */
.toc { page-break-after: always; padding: 2rem; }
.toc h2 { color: #2E7D32; font-size: 24pt; border-bottom: 3px solid #2E7D32; padding-bottom: 0.5rem; }
.toc ol { font-size: 13pt; line-height: 2.2; padding-left: 1.5rem; }
.toc ol li a { color: #212121; text-decoration: none; }
.toc ol li a:hover { color: #2E7D32; }

/* SECTION TITLE PAGE */
.section-cover { page-break-before: always; page-break-after: always; padding: 5rem 2rem; text-align: center; }
.section-cover .num { font-size: 80pt; color: #66BB6A; font-weight: 700; line-height: 1; }
.section-cover h2 { color: #2E7D32; font-size: 28pt; margin: 1rem 0; border: none; }

/* CONTENT */
.section { page-break-before: always; padding: 1rem 0; }
.section h1 { color: #2E7D32; border-bottom: 3px solid #2E7D32; padding-bottom: .4rem; font-size: 20pt; margin-top: 0; }
.section h2 { color: #2E7D32; margin-top: 1.6rem; font-size: 14pt; border-bottom: 1px solid #C8E6C9; padding-bottom: .25rem; page-break-after: avoid; }
.section h3 { color: #424242; margin-top: 1.2rem; font-size: 12pt; page-break-after: avoid; }
.section h4 { color: #616161; font-size: 11pt; }
.section p, .section li { line-height: 1.55; }
.section code { background: #F5F5F5; padding: 1px 5px; border-radius: 3px; font-family: 'Consolas', monospace; font-size: 9pt; color: #C62828; }
.section pre { background: #F5F5F5; padding: .8rem 1rem; border-radius: 6px; border-left: 4px solid #2E7D32; overflow-x: auto; font-size: 8.5pt; page-break-inside: avoid; }
.section pre code { background: transparent; padding: 0; color: #212121; }
.section table { border-collapse: collapse; width: 100%; margin: .8rem 0; font-size: 9.5pt; page-break-inside: avoid; }
.section th { background: #2E7D32; color: white; padding: .4rem .6rem; text-align: left; font-size: 9.5pt; }
.section td { padding: .35rem .6rem; border-bottom: 1px solid #E0E0E0; }
.section tr:nth-child(even) td { background: #FAFAFA; }
.section blockquote { border-left: 4px solid #66BB6A; background: #E8F5E9; padding: .6rem 1rem; margin: .8rem 0; font-style: italic; }
.section a { color: #2E7D32; text-decoration: none; }
.section strong { color: #1B5E20; }
.section hr { border: none; border-top: 2px solid #E0E0E0; margin: 1.5rem 0; }
.section ul, .section ol { padding-left: 1.4rem; }
`;

const today = new Date().toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });

let body = `
<div class="cover">
  <div class="leaf">🌿</div>
  <h1>EcoRide</h1>
  <div class="subtitle">Plateforme de covoiturage écologique</div>
  <div class="meta">
    <p><strong>Évan OROFINO</strong></p>
    <p>TP — Développeur Web et Web Mobile</p>
    <p>Studi · Promotion 2026</p>
    <p>ECF · CCP1 / CCP2 — Hiver 2025 / Printemps 2026</p>
  </div>
  <div class="footer">${today}</div>
</div>

<div class="toc">
  <h2>Liens des livrables</h2>
  <div style="background:#E8F5E9;padding:1.5rem 2rem;border-radius:8px;border-left:5px solid #2E7D32;margin-bottom:1.5rem;font-size:11pt;line-height:2">
    <p style="margin:.3rem 0"><strong>🌐 Application déployée :</strong><br>
       <a href="https://orofino.alwaysdata.net">https://orofino.alwaysdata.net</a></p>
    <p style="margin:.3rem 0"><strong>💻 Code source GitHub PUBLIC :</strong><br>
       <a href="https://github.com/EvanOROFINO/ecoride">https://github.com/EvanOROFINO/ecoride</a></p>
    <p style="margin:.3rem 0"><strong>📋 Outil de gestion de projet (Trello) :</strong><br>
       <a href="https://trello.com/b/cwAXaZBt/ecoride">https://trello.com/b/cwAXaZBt/ecoride</a></p>
  </div>
  <div style="background:#FFF8E1;padding:1rem 1.5rem;border-radius:8px;font-size:10pt;margin-bottom:1.5rem">
    <strong>Identifiants de démonstration</strong> (mot de passe : <code>Password123!</code>)<br>
    Administrateur : <code>admin@ecoride.fr</code> · Employé : <code>employe@ecoride.fr</code> ·
    Chauffeur+Pass. : <code>sophie@example.com</code> · Passager : <code>tom@example.com</code>
  </div>

  <h2>Sommaire</h2>
  <ol>
    ${sections.map(s => `<li>${s.title}</li>`).join('\n    ')}
  </ol>
</div>
`;

console.log(`Construction de la copie unique (${sections.length} sections)...`);

for (const section of sections) {
  const fullPath = path.join(docsRoot, section.file);
  if (!fs.existsSync(fullPath)) {
    console.log(`  /!\\ ${section.file} introuvable, ignoré`);
    continue;
  }
  const md = fs.readFileSync(fullPath, 'utf-8');
  let html = marked.parse(md);

  // Réécrit les chemins d'images relatifs en file:// absolus pour Edge headless
  const sectionDir = path.dirname(fullPath);
  html = html.replace(/<img([^>]*?)src="([^"]+)"/g, (match, attrs, src) => {
    if (/^(https?:|file:|data:)/.test(src)) return match;
    const absPath = path.resolve(sectionDir, src).replace(/\\/g, '/');
    return `<img${attrs}src="file:///${absPath}" style="max-width:100%;height:auto;border:1px solid #E0E0E0;border-radius:4px;margin:1rem 0"`;
  });

  body += `
<div class="section-cover">
  <div class="num">${section.title.split('.')[0]}</div>
  <h2>${section.title.replace(/^\d+\.\s*/, '')}</h2>
</div>
<div class="section">
${html}
</div>
`;
  console.log(`  + ${section.file}`);
}

const fullHtml = `<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Copie à rendre — EcoRide — Evan OROFINO</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
  <style>${cssTheme}</style>
</head>
<body>
${body}
</body>
</html>`;

fs.writeFileSync(outFile, fullHtml, 'utf-8');
console.log(`\nHTML genere : ${outFile}`);
console.log(`Taille : ${(fullHtml.length / 1024).toFixed(1)} Ko`);
