<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\BakongController;
// ===== GUEST & PUBLIC ROUTES =====
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('product.show');

// ===== AUTHENTICATION =====
Route::middleware('guest')->group(function () {
    Route::get('/login', fn() => view('auth.login'))->name('login');
    Route::post('/login', function (Request $request) {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }
        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    });

    Route::get('/register', fn() => view('auth.register'))->name('register');
    Route::post('/register', function (Request $request) {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = \App\Models\User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => bcrypt($data['password']),
        ]);
        Auth::login($user);
        return redirect()->route('home')->with('success', 'Welcome!');
    });
});

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/')->with('success', 'See you soon!');
})->name('logout')->middleware('auth');

// ===== CART =====
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/bulk', [CartController::class, 'bulkAction'])->name('cart.bulkAction');

    Route::patch('/cart/{cart}/qty', [CartController::class, 'updateQty'])->name('cart.updateQty');
    Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');

    Route::post('/wishlist/{product}', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist.index');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/place', [CheckoutController::class, 'placeOrder'])->name('checkout.place');

    Route::get('/checkout/{order}/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');
    Route::post('/checkout/{order}/confirm-paid', [CheckoutController::class, 'confirmPaid'])->name('checkout.confirmPaid');
    Route::get('/checkout/{order}/success', [CheckoutController::class, 'success'])->name('checkout.success');

    Route::get('/bakong/check', [BakongController::class, 'check'])->name('bakong.check');

    Route::get('/my-orders', [OrderController::class, 'myOrders'])->name('orders.index');
Route::get('/my-orders/{order}', [OrderController::class, 'myOrderShow'])->name('orders.show');

});


// ===== CATCH-ALL =====
Route::fallback(fn() => redirect()->route('home'));
