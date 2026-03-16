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
    const parseJson = (value, fallback) => {
        if (!value) {
            return fallback;
        }

        try {
            return JSON.parse(value);
        } catch (error) {
            console.error('Unable to parse Vue form list payload.', error);
            return fallback;
        }
    };

    createApp(ListComponent, {
        initialItems: parseJson(formListElement.dataset.forms, []),
        createUrl: formListElement.dataset.createUrl ?? '',
        editBaseUrl: formListElement.dataset.editBaseUrl ?? '',
        labels: parseJson(formListElement.dataset.labels, {}),
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
