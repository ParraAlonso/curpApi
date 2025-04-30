const { chromium } = require('playwright');
const os = require('os');
const curp = process.argv[2];

(async () => {

    const isLinuxServer = os.platform() === 'linux' && !process.env.DISPLAY;

    const browser = await chromium.launch({
        headless: isLinuxServer,
        executablePath: isLinuxServer ? '/usr/bin/google-chrome' : 'C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe',
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--no-first-run',
            '--no-zygote',
            '--disable-gpu'
        ]
    });

    const page = await browser.newPage();

    await page.goto('https://www.gob.mx/curp/', { waitUntil: 'networkidle' });

    await page.locator('#curpinput').fill(curp);

    await page.locator('#searchButton').click();

    try {
        await page.waitForSelector('.results', { timeout: 3000 });

        const resultado = await page.locator('.results').innerText();

        console.log(resultado);

    } catch (error) {

        console.error('No se encontró resultado para esa CURP. ' + error);

        process.exit(1);
    }

    await browser.close();
})();
