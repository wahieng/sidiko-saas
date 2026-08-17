/**
 * SIDIKO Core
 *
 * Entry point seluruh JavaScript Core SIDIKO.
 */

import Ajax from './ajax';
import Modal from './modal';
import Table from './table';
import Helper from './helper';
import Notification from './notification';

const SIDIKO = {

    Ajax,

    Modal,

    Table,

    Helper,

    Notification,

    init() {

        this.Modal.init();

        console.log('SIDIKO Core initialized.');

    },

};

window.SIDIKO = SIDIKO;

document.addEventListener('DOMContentLoaded', () => {

    SIDIKO.init();

});

export default SIDIKO;