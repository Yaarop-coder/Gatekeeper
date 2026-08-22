# Gatekeeper v3.5 🛡️

Gatekeeper v3.5 is a premium, high-performance Task Management Dashboard built with the **TALL Stack**. It combines minimalist design with powerful features like full RTL/Bilingual support and a robust API.

---

## 🌍 Advanced RTL & Localization
Gatekeeper isn't just translated; it's **localized** for global use.
- **Full RTL Support:** A complete layout flip for Arabic users using Tailwind's RTL engine.
- **Contextual Translation:** Activity logs dynamically translate complex actions (e.g., "قام بنقل المهمة إلى...").
- **Language Persistence:** Middleware-driven locale switching that remembers user preferences.
- **Localized Dates:** Date formats and month names automatically adapt to the selected language (e.g., "28 Apr" ↔ "28 أبريل").

## ✨ Key Features
- **Interactive Kanban & List Views:** Manage tasks with a fluid, reactive UI.
- **Authentication:** Full guest/user flow including password resets.
- **Task Drawer:** A slide-out management panel for editing, deleting, and commenting.
- **Live Productivity Analytics:** Real-time stats and progress tracking.
- **Confetti Celebrations:** Visual feedback upon task completion.
- **PDF Export:** Generate professional project reports with a single click.

## 🔌 API Integration
Built for the modern web, Gatekeeper provides a structured API layer:
- **RESTful Endpoints:** Full CRUD capabilities for tasks and projects.
- **Secure Auth:** Powered by Laravel Sanctum for token-based authentication.
- **Developer Ready:** Optimized JSON responses designed for mobile or third-party integrations.

## 🧪 Testing
The test suite is configured for a multi-tenant environment using an in-memory SQLite database.

## 🛠️ Tech Stack
- **Backend:** Laravel 11
- **Frontend:** Livewire 3 & Alpine.js
- **Styling:** Tailwind CSS (Custom RTL Theme)
- **Database:** PostgreSQL / MySQL

---

## 🚀 Installation & Setup

Follow these steps to get your local environment running:

##############################

1. Clone the Repository
bash
git clone [https://github.com/Yaarop-coder/gatekeeper.git]
    
2. Install Dependencies
Bash
    cd gatekeeper
    composer install
    npm install && npm run build

3.Environment Configuration
Bash
    cp .env.example .env
    php artisan key:generate
Note: Make sure to update your .env with your database credentials.

4. Database & Launch
Bash
    php artisan migrate
    php artisan serve

##############################

📝 License
The MIT License (MIT). Please see License File for more information.

Built with ❤️ by Yaarop Mohammed