/**
 * Bluetooth Thermal Printer Module
 * Menggunakan Web Bluetooth API untuk koneksi ke printer thermal via Bluetooth.
 *
 * Service UUID: 00001101-0000-1000-8000-00805f9b34fb (Serial Port Profile)
 * Kompatibel dengan: POS-58, POS-80, ESC/POS thermal printer Bluetooth.
 */
const BluetoothPrinter = (() => {
    const SPP_SERVICE_UUID = '00001101-0000-1000-8000-00805f9b34fb';
    const SPP_CHARACTERISTIC_UUID = '00001101-0000-1000-8000-00805f9b34fb';
    const STORAGE_KEY = 'bt_printer_last_device';
    let device = null;
    let characteristic = null;

    function isSupported() {
        return typeof navigator !== 'undefined' && navigator.bluetooth !== undefined;
    }

    function getSavedDeviceName() {
        try { return localStorage.getItem(STORAGE_KEY); } catch { return null; }
    }

    function saveDeviceName(name) {
        try { localStorage.setItem(STORAGE_KEY, name); } catch {}
    }

    function clearSavedDevice() {
        try { localStorage.removeItem(STORAGE_KEY); } catch {}
    }

    async function connect() {
        if (!isSupported()) {
            throw new Error('UNSUPPORTED: Browser tidak mendukung Web Bluetooth. Gunakan Chrome, Edge, atau Opera.');
        }

        const options = {
            filters: [{ services: [SPP_SERVICE_UUID] }],
            optionalServices: [SPP_SERVICE_UUID],
        };

        try {
            device = await navigator.bluetooth.requestDevice(options);
        } catch (e) {
            if (e.name === 'NotFoundError') {
                throw new Error('CANCELLED: Pencarian printer dibatalkan oleh pengguna.');
            }
            throw e;
        }

        saveDeviceName(device.name || 'Unknown Printer');

        device.addEventListener('gattserverdisconnected', () => {
            characteristic = null;
        });

        const server = await device.gatt.connect();
        const service = await server.getPrimaryService(SPP_SERVICE_UUID);
        characteristic = await service.getCharacteristic(SPP_CHARACTERISTIC_UUID);

        return device.name;
    }

    async function reconnect() {
        if (!device || !device.gatt.connected) {
            return await connect();
        }
        return device.name;
    }

    function isConnected() {
        return device !== null && device.gatt && device.gatt.connected && characteristic !== null;
    }

    function getDeviceName() {
        return device ? device.name : null;
    }

    function disconnect() {
        if (device && device.gatt.connected) {
            device.gatt.disconnect();
        }
        characteristic = null;
    }

    async function sendData(data) {
        if (!isConnected()) {
            throw new Error('NOT_CONNECTED: Tidak ada koneksi ke printer. Silakan hubungkan ulang.');
        }

        const chunkSize = 512;
        const buffer = data instanceof Uint8Array ? data : new TextEncoder().encode(data);

        for (let offset = 0; offset < buffer.length; offset += chunkSize) {
            const chunk = buffer.slice(offset, offset + chunkSize);
            await characteristic.writeValueWithoutResponse(chunk);
        }
    }

    async function printBinUrl(url) {
        if (!isConnected()) {
            await connect();
        }

        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('FETCH_FAILED: Gagal mengambil data struk dari server.');
        }

        const blob = await response.blob();
        const buffer = await blob.arrayBuffer();
        const data = new Uint8Array(buffer);

        await sendData(data);
    }

    async function printBinData(base64Data) {
        if (!isConnected()) {
            await connect();
        }

        const binaryString = atob(base64Data);
        const bytes = new Uint8Array(binaryString.length);
        for (let i = 0; i < binaryString.length; i++) {
            bytes[i] = binaryString.charCodeAt(i);
        }

        await sendData(bytes);
    }

    return {
        isSupported,
        connect,
        reconnect,
        isConnected,
        getDeviceName,
        getSavedDeviceName,
        disconnect,
        sendData,
        printBinUrl,
        printBinData,
        clearSavedDevice,
    };
})();

/**
 * Fungsi global untuk cetak struk via Bluetooth
 */
async function cetakBluetooth(transaksiId) {
    const btn = event.currentTarget || document.querySelector('[data-bt-print]');
    const originalHtml = btn ? btn.innerHTML : '';

    function setStatus(text, color) {
        if (!btn) return;
        btn.innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i>${text}`;
        btn.disabled = true;
        btn.classList.remove('bg-purple-600', 'hover:bg-purple-700', 'bg-green-600', 'bg-red-600');
        btn.classList.add(color);
    }

    function finishSuccess() {
        if (!btn) return;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Tercetak!';
        btn.classList.remove('bg-blue-600', 'bg-yellow-500', 'bg-red-600');
        btn.classList.add('bg-green-600');
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            btn.classList.remove('bg-green-600');
            btn.classList.add('bg-purple-600', 'hover:bg-purple-700');
        }, 2000);
    }

    function finishError(msg) {
        if (!btn) return;
        btn.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Gagal';
        btn.classList.remove('bg-blue-600', 'bg-yellow-500', 'bg-green-600');
        btn.classList.add('bg-red-600');
        setTimeout(() => {
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            btn.classList.remove('bg-red-600');
            btn.classList.add('bg-purple-600', 'hover:bg-purple-700');
        }, 3000);
        alert('Cetak Bluetooth gagal:\n' + msg);
    }

    if (!BluetoothPrinter.isSupported()) {
        alert('Browser tidak mendukung Web Bluetooth.\n\nGunakan Chrome, Edge, atau Opera di desktop atau Android.');
        return;
    }

    try {
        if (!BluetoothPrinter.isConnected()) {
            setStatus('Memilih printer...', 'bg-blue-600');
            await BluetoothPrinter.connect();
        }

        setStatus('Mengirim ke printer...', 'bg-yellow-500');
        const binUrl = `/kasir/struk/${transaksiId}/bin`;
        await BluetoothPrinter.printBinUrl(binUrl);

        finishSuccess();
    } catch (e) {
        finishError(e.message || String(e));
    }
}

/**
 * Auto-print via Bluetooth setelah checkout (dipanggil dari halaman struk)
 */
async function autoPrintBluetooth(transaksiId) {
    if (!BluetoothPrinter.isSupported()) return false;
    if (!BluetoothPrinter.getSavedDeviceName()) return false;

    try {
        await BluetoothPrinter.connect();
        const binUrl = `/kasir/struk/${transaksiId}/bin`;
        await BluetoothPrinter.printBinUrl(binUrl);
        return true;
    } catch {
        return false;
    }
}
