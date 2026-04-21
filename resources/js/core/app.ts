import '../../css/app.css';
import './bootstrap';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createPinia } from 'pinia';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../../vendor/tightenco/ziggy';
import { applyInitialLocaleAttribute, i18n } from './i18n';
import { applyInitialTheme } from './theme';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `../pages/${name}.vue`,
            import.meta.glob<DefineComponent>('../pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .use(createPinia())
            .mount(el);

        applyInitialLocaleAttribute();
        applyInitialTheme();
    },
    progress: {
        color: '#4B5563',
    },
});
