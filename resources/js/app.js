import './bootstrap';

import Swal from 'sweetalert2';
import axios from 'axios';


import 'remixicon/fonts/remixicon.css';
import Alpine from 'alpinejs';

window.axios = axios;
window.Alpine = Alpine;
window.Swal = Swal;
Alpine.start();
