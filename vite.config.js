import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import fs from 'fs';

function getTemplate() {
    try {
        const content = fs.readFileSync('config/site.php', 'utf-8');
        const match = content.match(/'template'\s*=>\s*'([^']+)'/);
        return match ? match[1] : 'bento';
    } catch (e) {
        return 'bento';
    }
}

const template = getTemplate();

// Only enable local Laragon SSL if the local certificate files actually exist
const laragonKey = 'C:/laragon/etc/ssl/laragon.key';
const laragonCert = 'C:/laragon/etc/ssl/laragon.crt';
const hasLaragonSsl = fs.existsSync(laragonKey) && fs.existsSync(laragonCert);

export default defineConfig({
    plugins: [
        laravel({
            input: [
                `resources/views/templates/${template}/css/app.css`,
                `resources/views/templates/${template}/js/app.js`
            ],
            refresh: [
                `resources/views/templates/${template}/**`,
                'routes/**',
                'config/site.php',
            ],
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        cors: true,
        ...(hasLaragonSsl
            ? {
                  https: {
                      key: fs.readFileSync(laragonKey),
                      cert: fs.readFileSync(laragonCert),
                  },
              }
            : {}),
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
