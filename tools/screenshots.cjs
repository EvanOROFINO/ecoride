/**
 * Script de capture de maquettes haute fidélité.
 * Utilise Puppeteer (installé avec mermaid-cli) pour prendre des screenshots
 * des pages clés en versions desktop et mobile.
 *
 * Usage :
 *   node tools/screenshots.cjs [baseUrl]
 *
 * Default baseUrl : http://localhost:8002 (serveur EcoRide local)
 */
const path = require('path');
const fs = require('fs');

const baseUrl = process.argv[2] || 'http://localhost:8002';
const outDir = path.join(__dirname, '..', 'docs', 'maquettes', 'img');
if (!fs.existsSync(outDir)) fs.mkdirSync(outDir, { recursive: true });

const credentials = {
  email: 'admin@ecoride.fr',
  password: 'Password123!',
};

const pages = [
  { url: '/',                                              name: '01-accueil',          auth: false },
  { url: '/covoiturages?depart=Paris&arrivee=Lyon&date=' + nextDate(2), name: '02-recherche-covoiturages', auth: false },
  { url: '/covoiturages/1',                                name: '03-detail-covoiturage', auth: false },
  { url: '/login',                                         name: '04-connexion',        auth: false },
  { url: '/register',                                      name: '05-inscription',      auth: false },
  { url: '/admin',                                         name: '06-admin-dashboard',  auth: true  },
];

const viewports = {
  desktop: { width: 1440, height: 900, isMobile: false },
  mobile:  { width: 414, height: 896,  isMobile: true, hasTouch: true, deviceScaleFactor: 2 },
};

function nextDate(days) {
  const d = new Date();
  d.setDate(d.getDate() + days);
  return d.toISOString().split('T')[0];
}

(async () => {
  // Charger puppeteer depuis le dossier node_modules de mermaid-cli
  const puppeteer = require('puppeteer');

  const browser = await puppeteer.launch({
    headless: 'new',
    args: ['--no-sandbox', '--disable-setuid-sandbox'],
  });

  for (const [device, viewport] of Object.entries(viewports)) {
    console.log(`\n=== Captures ${device} (${viewport.width}x${viewport.height}) ===`);
    const page = await browser.newPage();
    await page.setViewport(viewport);

    // Login si on a au moins une page authentifiée
    if (pages.some(p => p.auth)) {
      try {
        await page.goto(baseUrl + '/login', { waitUntil: 'networkidle2', timeout: 15000 });
        await page.type('input[name="email"]', credentials.email);
        await page.type('input[name="password"]', credentials.password);
        await Promise.all([
          page.click('button[type="submit"]'),
          page.waitForNavigation({ waitUntil: 'networkidle2', timeout: 15000 }),
        ]);
        console.log('  Login OK');
      } catch (e) {
        console.log('  Login skip : ' + e.message);
      }
    }

    for (const p of pages) {
      const filename = `${p.name}-${device}.png`;
      const filepath = path.join(outDir, filename);
      try {
        await page.goto(baseUrl + p.url, { waitUntil: 'networkidle2', timeout: 15000 });
        await new Promise(r => setTimeout(r, 600));
        await page.screenshot({ path: filepath, fullPage: false });
        console.log(`  ${filename}`);
      } catch (e) {
        console.log(`  ECHEC ${filename} : ${e.message}`);
      }
    }
    await page.close();
  }

  await browser.close();
  console.log('\nTermine. Images dans ' + outDir);
})().catch(e => {
  console.error('Erreur fatale :', e.message);
  process.exit(1);
});
