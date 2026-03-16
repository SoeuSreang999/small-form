import './bootstrap';
import Alpine from 'alpinejs';
import dragula from 'dragula';
import 'dragula/dist/dragula.css';

window.Alpine = Alpine;
Alpine.start();

// Make Dragula available to inline Blade scripts
window.dragula = dragula;

// Vue 3
// import { createApp } from 'vue';
// import ListComponent from './components/Form/List.vue';
// createApp(ListComponent).mount('#content');

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
