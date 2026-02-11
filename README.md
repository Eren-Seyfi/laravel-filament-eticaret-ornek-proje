<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="360" alt="Laravel Logo">
  </a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-red" alt="Laravel 12">
  <img src="https://img.shields.io/badge/Filament-Admin%20Panel-blueviolet" alt="Filament">
  <img src="https://img.shields.io/badge/PHP-8.3-blue" alt="PHP 8.3">
  <img src="https://img.shields.io/badge/Status-Example%20Project-informational" alt="Status">
</p>

# Laravel Filament E-Ticaret Örnek Proje

> 🎬 **Demo Video (proje içi):** `example.mp4`  
> 👉 İzlemek için: **[example.mp4](./example.mp4)**

Bu proje, **Laravel 12** ve **Filament Admin Panel** kullanılarak geliştirilmiş
örnek bir **e-ticaret uygulamasıdır**.

Amaç; Filament ile admin panel ağırlıklı, modern ve genişletilebilir
bir e-ticaret yapısının nasıl kurgulanabileceğini göstermek ve
geliştiriciler için **öğretici bir referans proje** sunmaktır.

> ⚠️ Bu proje **canlı ortam (production)** için hazır bir mağaza değildir.  
> Eğitim, demo ve başlangıç (starter) amacıyla hazırlanmıştır.

---

## 📽️ Demo Video (Ön İzleme)

[![Demo Video](https://img.shields.io/badge/▶%20Demo%20Videoyu%20İzle-blue?style=for-the-badge)](./example.mp4)

Bu videoda:
- Filament admin panel arayüzü
- Ürün ve kategori yönetimi
- Dashboard ve istatistik ekranları
- Proje genel yapısı

kısa bir demo ile gösterilmektedir.

---

## 🚀 Özellikler

- Laravel 12 altyapısı
- Filament Admin Panel
- Ürün ve kategori yönetimi
- Sipariş yapısına uygun mimari
- Dashboard ve istatistik örnekleri
- Modüler ve genişletilebilir yapı
- Temiz ve okunabilir proje mimarisi

---

## 🧠 Projenin Amacı

Bu repository;

- Laravel + Filament öğrenmek isteyenler
- Admin panel odaklı e-ticaret yapıları incelemek isteyenler
- Kendi e-ticaret veya yönetim paneli projesine temel arayanlar

için hazırlanmış **örnek bir çalışmadır**.

---

## 🛠️ Kurulum

```bash
git clone https://github.com/Eren-Seyfi/laravel-filament-eticaret-ornek-proje.git
cd laravel-filament-eticaret-ornek-proje

composer install

cp .env.example .env
php artisan key:generate

# .env dosyasında DB ayarlarını yap (DB_DATABASE, DB_USERNAME, DB_PASSWORD)

php artisan migrate --seed

npm install
npm run dev

php artisan serve
```

---

## 🔐 Admin Panel

```txt
URL: /admin
```

> Demo kullanıcı bilgileri seed dosyalarında yer alabilir veya manuel olarak oluşturulabilir.

---

## 📚 Kullanılan Teknolojiler

- Laravel 12
- Filament Admin Panel
- PHP 8.3
- Livewire
- MySQL / SQLite

---

## 📄 Lisans

Bu proje **MIT Lisansı** ile lisanslanmıştır.  
Laravel ve Filament kendi lisans koşullarına tabidir.
