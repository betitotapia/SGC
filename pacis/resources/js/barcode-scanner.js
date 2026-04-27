import { BrowserMultiFormatReader } from '@zxing/browser';

/**
 * Envoltorio reutilizable sobre ZXing para leer códigos de barras
 * desde la webcam. Se llama desde Alpine/Livewire en:
 *   - products.edit (alta de producto)
 *   - remissions.create (captura de productos en remisión)
 */
export class BarcodeScanner {
    constructor() {
        this.reader = null;
        this.controls = null;
    }

    async start(videoEl, onDecoded) {
        if (!navigator.mediaDevices?.getUserMedia) {
            alert('Tu navegador no soporta cámara.');
            return;
        }

        this.reader = new BrowserMultiFormatReader();
        this.controls = await this.reader.decodeFromVideoDevice(
            undefined,
            videoEl,
            (result, error, controls) => {
                if (result) {
                    onDecoded(result.getText());
                    controls.stop();
                    this.controls = null;
                }
            }
        );
    }

    stop() {
        if (this.controls) {
            this.controls.stop();
            this.controls = null;
        }
    }
}
