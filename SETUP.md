# 📋 KURULUM NOTLARI

## ⚠️ ÖNEMLİ: İlk Kurulum Adımları

### 1. `.env` Dosyası Oluştur

GitHub'dan indirdikten sonra **MUTLAKA** şunu yap:

```bash
copy config\.env.example .env
```

### 2. `.env` Dosyasını Düzenle

Root klasördeki `.env` dosyasını aç ve MySQL bilgilerini gir:

```env
DB_HOST=localhost
DB_NAME=sinematix
DB_USER=root
DB_PASS=senin_mysql_sifren    # ← BURAYA ŞİFRENİ YAZ
```

### 3. Çalıştır

```bash
php -S localhost:8000
```

Tarayıcıda: `http://localhost:8000`

---

## 🔐 Neden `.env` Dosyası GitHub'da YOK?

`.env` dosyası **GÜVENLİK NEDENİYLE** `.gitignore`'da.

**Neden?**
- Veritabanı şifresi içerir
- Her bilgisayarda farklı ayarlar olabilir
- Hassas bilgiler GitHub'a gitMEMELİ

**Her geliştirici kendi `.env` dosyasını `.env.example`'dan oluşturur.**

---

## ✅ Kontrol Listesi

- [ ] `.env` dosyası ROOT klasörde oluşturuldu mu?
- [ ] `.env` içinde MySQL şifresi yazıldı mı?
- [ ] MySQL servisi çalışıyor mu?
- [ ] `php -S localhost:8000` komutu çalıştırıldı mı?

---

## 🆘 Sorun mu var?

README.md dosyasındaki "Troubleshooting" bölümüne bak!
