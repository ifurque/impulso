// Cliente HTTP axios disponible globalmente para el resto de los scripts.
import axios from 'axios';
window.axios = axios;

// Marca las peticiones como AJAX para que Laravel las reconozca (expectsJson/ajax()).
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
