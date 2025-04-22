import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';  // Inertia para Vue 3
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';  // Resolver componentes dinámicamente
import { ZiggyVue } from '../../vendor/tightenco/ziggy';  // Ziggy para gestionar rutas de Laravel
import { Link } from '@inertiajs/vue3';  // InertiaLink para navegación
import '../css/app.css';  // Importar estilos CSS globales
import '../js/bootstrap';  // Importar bootstrap para funcionalidades JS
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';  // Usar nombre de aplicación desde VITE

createInertiaApp({
    title: (title) => `${title} - ${appName}`,  // Título dinámico
    resolve: (name) => {
        // Resolver el componente dinámicamente desde la carpeta Pages
        const page = resolvePageComponent(
            `./Pages/${name}.vue`,  // Ruta para resolver componentes Vue
            import.meta.glob('./Pages/**/*.vue')  // Cargar dinámicamente todos los archivos .vue de Pages
        );
        return page;  // Retornar la promesa con el componente
    },
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });  // Crear aplicación Vue

        app.component('Link', Link);  // Registrar Link como componente global

        app.use(plugin)  // Usar plugin de Inertia
            .use(ZiggyVue)  // Usar ZiggyVue para las rutas
            .mount(el);  // Montar la aplicación en el DOM
    },
    progress: {
        color: '#4B5563',  // Establecer el color de la barra de progreso
    },
});
