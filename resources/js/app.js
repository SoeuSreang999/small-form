import './bootstrap';
import Alpine from 'alpinejs';
import { createApp } from 'vue';
import dragula from 'dragula';
import 'dragula/dist/dragula.css';
import ListComponent from './components/Form/List.vue';

window.Alpine = Alpine;
Alpine.start();

// Make Dragula available to inline Blade scripts
window.dragula = dragula;

const formListElement = document.querySelector('[data-vue-form-list]');

if (formListElement) {
    const propsElement = formListElement.querySelector('[data-vue-form-list-props]');

    const parseJson = (name, value, fallback) => {
        if (!value) {
            return fallback;
        }

        try {
            return JSON.parse(value);
        } catch (error) {
            console.error(`Unable to parse Vue form list ${name} payload.`, error);
            return fallback;
        }
    };

    const props = parseJson('props', propsElement?.textContent, {});

    createApp(ListComponent, {
        initialItems: Array.isArray(props.initialItems) ? props.initialItems : [],
        createUrl: formListElement.dataset.createUrl ?? '',
        editBaseUrl: formListElement.dataset.editBaseUrl ?? '',
        labels: props.labels ?? {},
    }).mount(formListElement);
}

// window.Echo.channel('subjects')
//     .listen('.create', (data) => {
//         notyf.open({
//             type: 'success',
//             message: `
//             <h5>Success</h5>
//             <p>Reverb success</p>
//             `
//         });
//     });
