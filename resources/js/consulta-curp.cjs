const { chromium } = require('playwright');
const os = require('os');
const { json } = require('stream/consumers');
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

    await page.goto('https://www.gob.mx/curp/', { waitUntil: 'networkidle', timeout: 40000 });

    await page.locator('#curpinput').fill(curp);

    await page.locator('#searchButton').click();

    try {

        const response = await page.waitForResponse(response => response.url().includes('/v1/renapoCURP/consulta'),{ timeout:500 });
        const data = await response.json();
        console.log(JSON.stringify(data));

    } catch (error) {

        console.error('No se encontró resultado para esa CURP. ' + error);

        process.exit(1);
    }

    await browser.close();
})();
