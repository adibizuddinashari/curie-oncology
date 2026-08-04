const puppeteer = require("puppeteer");
const path = require("path");

const pages = process.argv.slice(2);
const OUT_DIR = process.env.SCREENSHOT_DIR;

(async () => {
  const browser = await puppeteer.launch();
  const page = await browser.newPage();
  await page.setViewport({ width: 1440, height: 1000 });
  for (const p of pages) {
    const url = `http://localhost:8123/${p}`;
    await page.goto(url, { waitUntil: "networkidle0" });
    const out = path.join(OUT_DIR, p.replace(".html", "") + ".png");
    await page.screenshot({ path: out, fullPage: true });
    console.log("saved", out);
  }
  await browser.close();
})();
