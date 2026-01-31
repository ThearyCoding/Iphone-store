

# Final Project – iPhone Store (SSR E-Commerce Website)

## 📌 Project Overview
This project is a **Server-Side Rendered (SSR) E-Commerce Website** built using **Laravel (Blade SSR)** with **Livewire**, **Vite**, and **TailwindCSS**.  
Customers can browse products, view details, add items to cart, checkout, pay using **Bakong KHQR**, and the system automatically sends **Telegram notifications** to the shop owner.

---

## ✅ Implemented Features

### 🏠 Home Page
- Server-side rendered product listing
- Display product image, name, and price
- Click product to view details

---

### 📦 Product / Service Detail Page
- Full product information
- Product image preview
- Price and description
- Add to cart
- Wishlist toggle
- **Share buttons**
  - Facebook
  - Telegram
  - Copy product link

---

### 🛒 Cart System
- Add product to cart
- Increase quantity
- Decrease quantity
- Remove item
- Delete items from cart
- Select / unselect items for checkout
- Bulk cart actions supported
- Auto calculate total price

---

### ❤️ Wishlist
- Add or remove product from wishlist
- Wishlist page to view saved products

---

### ✅ Checkout System
- Checkout page shows selected cart items
- Customer information form:
  - Full name
  - Phone number
  - Address
- Confirm order
- Automatically create:
  - `orders`
  - `order_items`

---

### 💳 Payment Integration (Bakong KHQR)
- Generate KHQR for each order
- Display KHQR payment page
- Check transaction status using MD5 via Bakong API

---

### 📩 Telegram Notification
- Automatically send order notification to shop owner
- Order details sent:
  - Customer information
  - Ordered items
  - Total amount

---

### 📜 Order History
- My Orders page
- View order details
- Payment status tracking

---

### 🔐 Authentication
- User registration
- Login
- Logout
- Protected routes for cart, checkout, wishlist, and orders

---

## 📄 Pages / Routes

| Route | Description |
|------|------------|
| `/` | Home page |
| `/product/{product}` | Product detail |
| `/cart` | Cart page (auth required) |
| `/checkout` | Checkout page (auth required) |
| `/checkout/{order}/pay` | KHQR payment page |
| `/checkout/{order}/success` | Payment success page |
| `/wishlist` | Wishlist page |
| `/my-orders` | Order history |
| `/my-orders/{order}` | Order detail |
| `/login` | Login |
| `/register` | Register |
| `/logout` | Logout |

---

## 🛠️ Technology Stack
- **Backend & SSR:** Laravel (Blade)
- **Interactive SSR:** Livewire
- **Frontend Build Tool:** Vite
- **Styling:** TailwindCSS
- **Database:** MySQL (recommended) / SQLite (development)
- **Payment Gateway:** Bakong KHQR
- **Notification:** Telegram Bot API
- **Hosting:** VPS (DP Data Center)

---

## 📂 Project Setup (Local Environment)

### 1️⃣ Requirements
- PHP **8.2+**
- Composer
- Node.js & npm
- MySQL

---

### 2️⃣ Installation
```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
````

---

### 3️⃣ Database Configuration

Edit `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=iphone_store
DB_USERNAME=root
DB_PASSWORD=
```

Run migrations:

```bash
php artisan migrate
```

---

### 4️⃣ Telegram & Bakong KHQR Configuration

Add these environment variables to `.env`:

```env
# Telegram
TELEGRAM_BOT_TOKEN=
TELEGRAM_ADMIN_CHAT_ID=

# Bakong KHQR
BAKONG_TOKEN=
BAKONG_ACCOUNT_ID=
BAKONG_MERCHANT_NAME=
BAKONG_MERCHANT_CITY=
BAKONG_CURRENCY=KHR
```

---

### 5️⃣ Run the Project

Terminal 1:

```bash
php artisan serve
```

Terminal 2:

```bash
npm run dev
```

Access the website:

```
http://127.0.0.1:8000
```

---

## 🌐 Hosting Information

* Hosted on **DP Data Center (VPS)**
* Hosting Provider:
  [https://dpdatacenter.com/en](https://dpdatacenter.com/en)

---

## 📌 Source Code

* Source code pushed to **GitHub**
* Repository link:
  (Add your GitHub repository link here)

---

## 🎥 Video Demonstration

The demo video includes:

* Home page browsing
* Product detail view
* Add to cart
* Cart item selection
* Checkout process
* KHQR payment page
* Telegram order notification
* Order history

---

## 👥 Team Members

| Name           | Role      |
| -------------- | --------- |
| Sen Abdulfarit | Developer |
| Cheam Huyyim   | Presenter |
| Cheam Limang   | Developer |
| Por Vouchngim  | Developer |
| Chorn Theary   | Developer |

---# Iphone-store
