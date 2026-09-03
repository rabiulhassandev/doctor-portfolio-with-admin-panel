import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament/admin/theme.css'],
            refresh: true,
            /*
             * Self-hosted through Bunny rather than linked from Google, so the
             * site makes no request to an ad network.
             *
             * The Latin half of the type system:
             *
             *   Text     Source Sans 3
             *   Display  Newsreader
             *
             * The Bengali half is SolaimanLipi, which is not on Bunny and is
             * therefore committed under resources/fonts/ and declared by hand
             * in resources/css/app.css. See the note there.
             *
             * Newsreader carries the display role because that role is not only
             * used for display: service titles, article titles and the figures
             * in the stats band all sit between 20px and 34px, and a true
             * display serif's hairlines fall apart down there. Newsreader was
             * drawn to work from caption size upward.
             */
            fonts: [
                /*
                 * Italics matter here: the articles use emphasis for Bangla
                 * words written in Latin script ("they say <em>bhar lage</em>"),
                 * and without a real italic the browser slants the roman
                 * itself, which looks like a rendering bug at reading size.
                 */
                bunny('Source Sans 3', {
                    weights: [400, 500, 600],
                    styles: ['normal', 'italic'],
                }),
                /*
                 * One weight only. Every display element on the site sets 400
                 * — headings, card titles, the stat figures — and each extra
                 * weight here is another file in the preload list that a
                 * visitor on mobile data pays for and never sees.
                 */
                bunny('Newsreader', {
                    weights: [400],
                    styles: ['normal', 'italic'],
                }),
            ],
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
