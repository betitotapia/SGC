import './bootstrap';
import Alpine from 'alpinejs';
import { BarcodeScanner } from './barcode-scanner';

window.Alpine = Alpine;
window.PacisBarcode = new BarcodeScanner();

Alpine.start();
