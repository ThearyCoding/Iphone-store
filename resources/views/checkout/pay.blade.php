@extends('layouts.minimal')
@section('title', 'Pay with KHQR')

@section('content')
<div class="max-w-xl mx-auto px-6 py-16"
     id="khqr-root"
     data-qr="{{ $order->khqr_qr }}"
     data-md5="{{ $order->khqr_md5 }}"
     data-order-id="{{ $order->id }}"
     data-check-url="{{ route('bakong.check') }}"
     data-confirm-url="{{ route('checkout.confirmPaid', $order) }}"
     data-csrf="{{ csrf_token() }}"
>
    <h1 class="text-3xl font-bold mb-4">Pay with Bakong KHQR</h1>
    <p class="text-gray-600 mb-8">
        Scan the QR code using your Bakong / bank app to complete payment.
    </p>

    <div class="bg-white rounded-2xl shadow p-8 text-center">
        <div class="flex justify-center mb-6">
            <div id="qrcode" style="width:260px;height:260px;border:1px dashed #ddd;"></div>
        </div>

        <div class="text-lg font-semibold">
            Total: ${{ number_format($order->total, 2) }}
        </div>

        <div class="text-sm text-gray-500 mt-2">
            Order #{{ $order->id }} • MD5: {{ $order->khqr_md5 }}
        </div>

        <div id="status" class="mt-6 text-base font-semibold text-yellow-600">
            Waiting for payment...
        </div>

        <div id="debug" class="mt-4 text-xs text-gray-400 break-all"></div>
    </div>

    <div class="mt-8">
        <a href="{{ route('checkout.index') }}" rel="nofollow" class="text-indigo-600 font-semibold">
            ← Back to checkout
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/qrcode.min.js') }}"></script>


<script>
document.addEventListener('DOMContentLoaded', () => {
  const root = document.getElementById('khqr-root');
  const qrText = root.dataset.qr || '';
  const md5 = root.dataset.md5 || '';
  const orderId = root.dataset.orderId || '';
  const checkUrl = root.dataset.checkUrl || '';
  const confirmUrl = root.dataset.confirmUrl || '';
  const csrf = root.dataset.csrf || '';

  const statusEl = document.getElementById('status');
  const debugEl  = document.getElementById('debug');
  const qrEl     = document.getElementById('qrcode');

  debugEl.innerText = `qrLength=${qrText.length} | md5=${md5} | orderId=${orderId} | qrcodejs=${typeof window.QRCode}`;

  // Validate
  if (!qrText || qrText.length < 20) {
    statusEl.className = "mt-6 text-base font-semibold text-red-600";
    statusEl.innerText = "QR data missing. Please refresh.";
    return;
  }

  if (typeof window.QRCode === "undefined") {
    statusEl.className = "mt-6 text-base font-semibold text-red-600";
    statusEl.innerText = "QR library failed to load (CDN blocked).";
    return;
  }

  // Render QR
  qrEl.innerHTML = "";
  new QRCode(qrEl, { text: qrText, width: 260, height: 260 });

  async function checkPaid() {
    try {
      const res = await fetch(`${checkUrl}?md5=${encodeURIComponent(md5)}`, {
        headers: { "Accept": "application/json" }
      });
      const data = await res.json();

      if (data.paid) {
        statusEl.className = "mt-6 text-base font-semibold text-green-600";
        statusEl.innerText = "Payment received ✅ Finalizing order...";

        const confirm = await fetch(confirmUrl, {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": csrf,
            "Accept": "application/json"
          },
          body: JSON.stringify({ md5 })
        });

        const confirmJson = await confirm.json();

        if (confirmJson.ok && confirmJson.redirect) {
          window.location.href = confirmJson.redirect;
          return;
        }

        statusEl.className = "mt-6 text-base font-semibold text-red-600";
        statusEl.innerText = confirmJson.message || "Could not finalize order.";
        return;
      }

      if (data.failed) {
        statusEl.className = "mt-6 text-base font-semibold text-red-600";
        statusEl.innerText = "Payment failed ❌ Please try again.";
        return;
      }

      statusEl.className = "mt-6 text-base font-semibold text-yellow-600";
      statusEl.innerText = "Waiting for payment...";
    } catch (e) {
      statusEl.className = "mt-6 text-base font-semibold text-gray-600";
      statusEl.innerText = "Checking payment...";
    }
  }

  setInterval(checkPaid, 3000);
  checkPaid();
});
</script>
@endpush
