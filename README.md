# 🌱 ReSEED — Restoring Hope, Reseeding Life

ReSEED is a South Sudan–based social impact initiative focused on **restoring livelihoods, regenerating ecosystems, and rebuilding communities** through climate-resilient agriculture, youth empowerment, and community-led innovation.

This repository contains the full source code for the **ReSEED web platform**, structured for scalability, transparency, and future deployment.

---

## 🌍 Project Vision

ReSEED exists to address:

- Climate shocks (flooding, land degradation)
- Food insecurity
- Youth unemployment
- Limited access to sustainable agricultural innovation

The platform serves as:

- A **public-facing website** for storytelling, projects, and impact
- An **admin system** for managing content (posts, projects, gallery)
- A foundation for future features such as **donations** and **community engagement**

---

## 🧱 Repository Structure

```text
reseed/
├── frontend/              # Public-facing website (UI)
│   ├── assets/            # CSS, JavaScript, images
│   ├── templates/         # Reusable UI components
│   ├── views/             # Static / landing views
│   └── *.php              # Frontend pages
│
├── backend/               # Server-side logic
│   ├── admin/             # Admin dashboard & CMS
│   ├── api/               # API endpoints
│   ├── includes/          # Shared backend utilities
│   ├── uploads/           # User uploads (ignored by Git)
│   └── database/          # Database schema
│
├── .gitignore
├── README.md
└── structure.txt
```

---

## 🛠 Tech Stack

### Frontend
- HTML5
- CSS3
- JavaScript
- PHP (templated pages)

### Backend
- PHP (procedural / modular)
- MySQL / MariaDB
- REST-style APIs

### Development Tools
- XAMPP (Apache, MySQL, PHP)
- Git & GitHub

### Planned Deployment
- **Frontend:** Vercel
- **Backend:** Render
- **Database:** Render (Managed)

---

## ⚙️ Local Development Setup

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/Hon-Tap/reseed.git
cd reseed
```

### 2️⃣ Place in XAMPP

Move the project into:

```text
C:\xampp\htdocs\reseed
```

### 3️⃣ Start Services
- Start **Apache**
- Start **MySQL**

---

## 🗄 Database Setup

### 1️⃣ Create the Database

Using phpMyAdmin:

```sql
CREATE DATABASE reseed_db;
```

### 2️⃣ Import the Schema

Import the file:

```text
backend/database/schema.sql
```

> ⚠️ This schema contains **structure only**  
> No users, no content, no sensitive data.

### 3️⃣ Database Configuration

Create the local configuration file:

```text
backend/includes/config.php
```

Example:

```php
<?php
$pdo = new PDO(
    "mysql:host=localhost;dbname=reseed_db;charset=utf8mb4",
    "root",
    "",
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]
);
```

> This file is intentionally ignored by Git for security reasons.

---

## 🔐 Admin Access

Admin users are **not seeded automatically**.

You may create admin accounts by:
- Manually inserting into the `users` table (with hashed passwords), or
- Using the provided admin creation script

Passwords must **always** be stored using secure hashing.

---

## 🔌 API Endpoints

### Current Endpoints

| Endpoint | Method | Description |
|--------|--------|-------------|
| `/api/get-posts.php` | GET | Fetch published blog posts |
| `/api/get-projects.php` | GET | Fetch projects |
| `/api/contact-handler.php` | POST | Handle contact form submissions |

### Planned Endpoints
- `/api/donate` — Donation & payment handling
- `/api/auth` — Authentication & role management

---

## 💳 Donations (Planned)

The donation feature is **intentionally not integrated yet**.

Planned approach:
- Backend-only payment handling
- Secure provider integration

Providers under consideration:
- PayPal
- Flutterwave
- Stripe (region permitting)

This ensures:
- Security
- Compliance
- Flexibility for future growth

---

## 🚀 Deployment Strategy (Planned)

| Layer | Platform |
|------|----------|
| Frontend | Vercel |
| Backend | Render |
| Database | Render (Managed) |

All secrets and credentials will be managed using **environment variables**.

---

## 🤝 Contribution Guidelines

This project is currently maintained by the **ReSEED core team**.

Future contributions may include:
- UI/UX improvements
- Translations & localization
- Accessibility enhancements
- Documentation
- Performance optimization

Please open an issue before submitting major changes.

---

## 📜 License

This project is released under the **MIT License**.

You are free to use, modify, and distribute this project with proper attribution.

---

## 🌱 Final Note

ReSEED is more than a website — it is a **platform for resilience**, built with care, intention, and long-term impact in mind.

---

## ✅ Roadmap

- [ ] Donation API integration
- [ ] Frontend deployment (Vercel)
- [ ] Backend deployment (Render)
- [ ] Demo seed data
- [ ] Multi-language support
- [ ] Accessibility improvements
