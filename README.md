# 🎬 Sinematix - Cinema Ticket Booking System

A modern, professional cinema ticket booking system built with PHP, MySQL, and clean architecture principles.

## ⚡ Quick Start (Yeni Bilgisayarda)

**GitHub'dan indirdikten sonra yapılacaklar:**

### 1️⃣ `.env` Dosyası Oluştur

```bash
# Windows (PowerShell/CMD)
copy config\.env.example .env

# Linux/Mac
cp config/.env.example .env
```

### 2️⃣ `.env` Dosyasını Düzenle

Root klasördeki `.env` dosyasını aç ve MySQL bilgilerini gir:

```env
DB_HOST=localhost
DB_NAME=sinematix
DB_USER=root
DB_PASS=senin_mysql_şifren    # ← BURAYA ŞİFRENİ YAZ
```

### 3️⃣ Veritabanını Oluştur

**Otomatik Yol** (önerilen):
```bash
php -S localhost:8000
```
Tarayıcıda `http://localhost:8000` aç - veritabanı otomatik oluşacak!

**Manuel Yol**:
```bash
mysql -u root -p < database/database.sql
```

### 4️⃣ Hazır! 🎉

Tarayıcıda: `http://localhost:8000`

**Demo Hesap:**
- Email: `demo@sinematix.com`
- Şifre: `password`

---

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

## 🛠️ Detaylı Kurulum

<details>
<summary>Adım adım kurulum (tıkla)</summary>

### 1. Repository'yi Clone'la

```bash
git clone https://github.com/sudenurozturkk/sinematix.git
cd sinematix
```

### 2. Environment Yapılandırması

```bash
# .env.example dosyasını kopyala
copy config\.env.example .env
```

`.env` dosyasını düzenle:
```env
DB_HOST=localhost
DB_NAME=sinematix
DB_USER=root
DB_PASS=your_mysql_password    # MySQL şifreni gir
DB_CHARSET=utf8mb4

APP_ENV=development
APP_DEBUG=true
APP_NAME=Sinematix

SESSION_LIFETIME=7200
SESSION_SECURE=false
SESSION_HTTPONLY=true

CACHE_ENABLED=true
CACHE_TTL=900
```

### 3. Veritabanı Kurulumu

**Seçenek 1 - Otomatik (Önerilen):**
```bash
php -S localhost:8000
```
İlk çalıştırmada Database.php otomatik olarak veritabanını oluşturacak.

**Seçenek 2 - Manuel:**
```bash
# MySQL'e bağlan
mysql -u root -p

# Veritabanını import et
mysql -u root -p < database/database.sql
```

### 4. Logs Klasörü

Logs klasörü otomatik oluşacak, ancak manuel oluşturmak isterseniz:
```bash
mkdir logs
```

### 5. Uygulamayı Çalıştır

```bash
php -S localhost:8000
```

Tarayıcıda aç: `http://localhost:8000`

</details>

---

## ✨ Features

- 🎫 **Movie Browsing** - Browse now showing and upcoming movies
- 🪑 **Seat Selection** - Interactive seat selection with real-time availability
- 💳 **Reservations** - Secure ticket booking with unique reservation codes
- 👤 **User Accounts** - Registration, login, and profile management
- 📱 **Responsive Design** - Modern UI with mobile-friendly interface
- ⚡ **Performance** - Optimized database queries and caching
- 🔒 **Security** - CSRF protection, input validation, and secure sessions

## 🚀 Recent Refactoring (v2.0)

This project was recently refactored to follow clean code principles:

- ✅ **SOLID Principles** - Single Responsibility, Dependency Inversion
- ✅ **DRY** - Eliminated code duplication
- ✅ **Separation of Concerns** - Views, Models, Helpers properly separated
- ✅ **Error Handling** - Custom exceptions, proper logging
- ✅ **Configuration Management** - Environment-based config with `.env`
- ✅ **PSR-4 Autoloading** - Automatic class loading
- ✅ **Transaction Safety** - Database transactions with proper rollback
- ✅ **Security** - CSRF tokens, input validation, rate limiting ready

## 📁 Project Structure

```
sinematix/
├── config/
│   ├── Config.php           # Configuration manager
│   ├── .env.example         # Environment template (COPY THIS!)
│   └── database.php         # Legacy database config
├── src/
│   ├── Exceptions/          # Custom exceptions
│   ├── Helpers/             # Helper classes (ViewHelper)
│   └── ...
├── models/                  # Data models
├── views/                   # View templates
│   ├── layouts/             # Header, footer
│   ├── errors/              # Error pages (404, 500)
│   └── ...
├── assets/                  # Static assets (CSS, JS, images)
├── api/                     # API endpoints
├── database/
│   └── database.sql         # Database schema + sample data
├── logs/                    # Application logs (auto-created)
├── .env                     # YOUR CONFIG (create from .env.example)
├── bootstrap.php            # Application bootstrap
├── autoload.php             # PSR-4 autoloader
├── index.php                # Entry point
└── README.md                # This file
```

## 🎨 Code Quality

This project follows modern PHP best practices:

- **Clean Code** - Meaningful names, small functions, proper comments
- **SOLID Principles** - Maintainable and extensible code
- **Error Handling** - Comprehensive exception handling
- **Security First** - Input validation, CSRF protection, secure sessions
- **Type Safety** - Type hints and return types
- **Documentation** - PHPDoc comments on all public methods

## 🔧 Development

### Debug Mode

Enable debug mode in `.env`:
```env
APP_DEBUG=true
APP_ENV=development
```

### Logs

Application logs are stored in `logs/error.log`

### Database

Database connection uses:
- PDO with prepared statements
- Connection retry logic (3 attempts)
- Transaction support with automatic rollback
- Singleton pattern for efficient connection pooling

## 🌟 Key Technologies

- **Backend**: PHP 7.4+, PDO
- **Database**: MySQL with InnoDB engine
- **Frontend**: Vanilla JavaScript, CSS3
- **Architecture**: MVC-inspired with service layer
- **Security**: Password hashing (bcrypt), CSRF tokens, XSS protection

## 📝 Demo Credentials

After importing the database:
- **Email**: demo@sinematix.com
- **Password**: password

## ❓ Troubleshooting

### "Veritabanı bağlantısı başarısız"
- ✅ `.env` dosyası root klasörde mi? (`config/` değil!)
- ✅ MySQL çalışıyor mu? (`mysql -u root -p` ile test et)
- ✅ `.env` dosyasında şifre doğru mu?

### "Page not found"
- ✅ PHP sunucu çalışıyor mu? (`php -S localhost:8000`)
- ✅ Doğru klasörde misin? (`cd sinematix`)

### "Class not found"
- ✅ `bootstrap.php` ve `autoload.php` var mı?
- ✅ `src/` klasörü var mı?

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is open source and available under the MIT License.

## 👨‍💻 Author

Developed as part of a clean code refactoring initiative.

## 🎯 Future Enhancements

- [ ] Repository pattern for all models
- [ ] Service layer for business logic
- [ ] API controllers with middleware
- [ ] Caching layer (Redis/File-based)
- [ ] Email notifications
- [ ] Payment integration
- [ ] Admin panel
- [ ] REST API for mobile apps

---

**Note**: This is v2.0 after a comprehensive refactoring that removed 30MB of redundant files and applied clean code principles throughout the codebase.

## 📞 Support

Sorun yaşıyorsanız [Issues](https://github.com/sudenurozturkk/sinematix/issues) sayfasından bildirebilirsiniz.
