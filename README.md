<div align="center">

# Sprintly

**Enterprise-Grade Project Management & Team Collaboration**

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel&logoColor=white)](https://laravel.com)
[![Livewire](https://img.shields.io/badge/Livewire-3.x-4E56A6?style=flat-square&logo=livewire&logoColor=white)](https://livewire.laravel.com)
[![Tailwind CSS](https://img.shields.io/badge/Tailwind-3.x-38B2AC?style=flat-square&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-blue?style=flat-square)](LICENSE)

[Features](#key-features) • [Tech Stack](#technology-stack) • [Installation](#getting-started) • [Architecture](#system-architecture) • [Contributing](#contributing)

</div>

---

## Overview

**Sprintly** is a robust, monolithic project management solution designed to bridge the gap between simple to-do lists and complex enterprise tools. Built on the **Laravel ecosystem**, it leverages **Livewire** for dynamic, SPA-like interactivity without the complexity of a separate frontend codebase.

Designed for modern teams, Sprintly integrates task management, real-time communication, and performance reporting into a single, cohesive platform.

## Key Features

### ⚡ Operational Efficiency
- **Dynamic Dashboard**: Real-time overview of pending tasks, project velocity, and personal workload.
- **Advanced Task Management**: Support for repetitive tasks, sub-tasks, priority levels, and granular status tracking.
- **Smart Calendar**: Integrated `FullCalendar` view for visual resource planning and deadline tracking.

### 🤝 Collaboration & Communication
- **Real-Time Project Chat**: WebSockets-powered (Pusher/Reverb) messaging contextually embedded within projects.
- **Activity Streams**: Granular audit logs for task updates, status changes, and comments.
- **Team Topology**: Department-based user organization with role-based access control (RBAC).

### � Reporting & Analytics
- **Daily Standup Reports**: Structured daily reporting workflow linking completed tasks to user performance.
- **Notification System**: Omni-channel alerts (Database & Email) for critical updates and reminders.

## Technology Stack

Sprintly is built on a modern, opinionated stack optimized for rapid development and maintainability.

| Layer | Technology | Description |
| :--- | :--- | :--- |
| **Framework** | **Laravel 12** | Core application logic, routing, and DI container. |
| **Interaction** | **Livewire 3** | Server-driven reactive components for dynamic UI. |
| **Frontend** | **Alpine.js** | Lightweight JavaScript for client-side state management. |
| **Styling** | **Tailwind CSS** | Utility-first CSS framework for bespoke design. |
| **UI Kit** | **WireUI** | Pre-built accessible components. |
| **Database** | **MySQL / MariaDB** | Relational data persistence. |
| **Real-Time** | **Pusher** | WebSockets for live chat and notifications. |

## System Architecture

### Domain Entities
The application is structured around several core domain aggregates:
- **Organization**: `Users`, `Departments`, `Roles`
- **Work Management**: `Projects`, `Tasks`, `RepetitiveTasks`, `TaskAssignments`
- **Communication**: `ProjectsChatMessage`, `TaskComments`, `Notifications`
- **Accountability**: `DailyReports`, `ReportTasks`

## Getting Started

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- MySQL 8.0+

### Installation

1. **Clone the Repository**
   ```bash
   git clone https://github.com/yourusername/sprintly.git
   cd sprintly
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Update `.env` with your database and Pusher credentials.*

4. **Database Setup**
   ```bash
   php artisan migrate --seed
   ```

5. **Build Assets & Run**
   ```bash
   # Terminal A
   npm run dev

   # Terminal B
   php artisan serve
   ```

## Testing

Run the full test suite to ensure system integrity.

```bash
php artisan test
```

## Contributing

We enforce a standard code style and commit convention. Please review our definition of done before submitting PRs.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'feat: Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## License

Distributed under the MIT License. See `LICENSE` for more information.

---

<div align="center">
    <sub>Built with ❤️ by 7xmohamed & AnassA7</sub>
</div>
