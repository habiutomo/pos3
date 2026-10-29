<div id="cartItems" class="space-y-2 max-h-64 overflow-y-auto mb-4">
    <p id="emptyCart" class="text-center text-gray-400 py-8">Keranjang kosong</p>
</div>

<div class="border-t pt-4 space-y-2">
    <div class="flex justify-between">
        <span class="font-medium">Subtotal</span>
        <span id="subtotal" class="font-bold">Rp 0</span>
    </div>
</div>

<div class="mt-4 space-y-3">
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Bayar</label>
        <input type="text" id="bayar" inputmode="numeric" placeholder="0"
            class="js-rupiah w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg"
            oninput="hitungKembalian()">
    </div>
    <div class="flex justify-between text-lg">
        <span class="font-medium">Kembalian</span>
        <span id="kembalian" class="font-bold text-green-600">Rp 0</span>
    </div>
    <button id="btnCheckout" onclick="checkout()" disabled
        class="w-full bg-green-600 text-white py-3 rounded-lg font-bold text-lg hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
        <i class="fas fa-check-circle mr-2"></i>Bayar
    </button>
</div>
<?php /**PATH D:\pos3.elnusaintegrated.cloud\resources\views/kasir/_cart-content.blade.php ENDPATH**/ ?>