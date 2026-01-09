import { createInertiaApp } from '@inertiajs/vue3';
import createServer from '@inertiajs/vue3/server';
import { renderToString } from '@vue/server-renderer';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createSSRApp, h, type DefineComponent } from 'vue';
import { Config, route as ziggyRoute } from 'ziggy-js';
import { ZorahSSR, trans } from 'zorah-js';
import { Zorah } from './zorah.js';

const appName = import.meta.env.VITE_APP_NAME || 'Druid Starter Kit';

createServer((page) =>
  createInertiaApp({
    page,
    render: renderToString,
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./pages/${name}.vue`, import.meta.glob('./pages/**/*.vue')) as Promise<DefineComponent>,
    setup({ App, props, plugin }) {
      const app = createSSRApp({ render: () => h(App, props) });

      // Configure Ziggy for SSR...
      const ziggyConfig: Config = {
        ...(page.props.ziggy as Config),
        location: new URL((page.props.ziggy as { location: string }).location),
      };

      // Create route function with config...
      const route: {
        (): Config;
        (name: string, params?: any, absolute?: boolean): string;
      } = ((name?: string, params?: any, absolute?: boolean) => {
        if (name === undefined) return ziggyConfig;
        return ziggyRoute(name, params, absolute, ziggyConfig);
      }) as any;

      // Make route function available globally...
      app.config.globalProperties.route = route;

      // Make translation functions available globally...
      app.config.globalProperties.__ = (key: string, replace?: Record<string, any>) => trans(key, replace, Zorah as any);
      app.config.globalProperties.trans = (key: string, replace?: Record<string, any>) => trans(key, replace, Zorah as any);

      // Make route function available globally for SSR...
      if (typeof window === 'undefined') {
        (globalThis as any).route = route;
        (globalThis as any).__ = (key: string, replace?: Record<string, any>) => trans(key, replace, Zorah as any);
        (globalThis as any).trans = (key: string, replace?: Record<string, any>) => trans(key, replace, Zorah as any);
      }

      app.use(plugin);
      app.use(ZorahSSR, Zorah);

      return app;
    },
  }),
);
