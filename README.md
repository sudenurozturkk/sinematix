# 🎬 Sinematix - Cinema Ticket Booking System

A modern, professional cinema ticket booking system built with PHP, MySQL, and clean architecture principles.

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

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx) or PHP built-in server

## 🛠️ Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd sinematix
   ```

2. **Configure environment**
   ```bash
   cp config/.env.example .env
   ```
   
   Edit `.env` with your database credentials:
   ```env
   DB_HOST=localhost
   DB_NAME=sinematix
   DB_USER=root
   DB_PASS=your_password
   ```

3. **Import database**
   ```bash
   mysql -u root -p < database/database.sql
   ```
   
   Or the database will be created automatically on first run.

4. **Set permissions**
   ```bash
   chmod 755 logs/
   ```

5. **Run the application**
   
   Using PHP built-in server:
   ```bash
   php -S localhost:8000
   ```
   
   Then visit: `http://localhost:8000`

## 📁 Project Structure

```
sinematix/
├── config/              # Configuration files
│   ├── Config.php       # Configuration manager
│   ├── .env.example     # Environment template
│   └── database.php     # Legacy database config
├── src/                 # Source code (new structure)
│   ├── Exceptions/      # Custom exceptions
│   ├── Helpers/         # Helper classes
│   └── ...
├── models/              # Data models
├── views/               # View templates
│   ├── layouts/         # Header, footer
│   ├── errors/          # Error pages (404, 500)
│   └── ...
├── assets/              # Static assets
│   ├── css/             # Stylesheets
│   ├── js/              # JavaScript
│   └── images/          # Images
├── api/                 # API endpoints
├── database/            # Database schema
├── logs/                # Application logs
├── bootstrap.php        # Application bootstrap
├── autoload.php         # PSR-4 autoloader
└── index.php            # Entry point
```

## 🎨 Code Quality

This project follows modern PHP best practices:

- **Clean Code** - Meaningful names, small functions, proper comments
- **SOLID Principles** - Maintainable and extensible code
- **Error Handling** - Comprehensive exception handling
- **Security First** - Input validation, CSRF protection, secure sessions
- ** Type Safety** - Type hints and return types
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
