# Laravel Blog Platform

A blogging platform built with Laravel, featuring scheduled posts, commenting, liking, and real-time notifications.

### Features

* **User Authentication & Authorization:** Secure user registration, login, and logout. Includes an admin role for managing all aspects of the platform.
* **Admin Privileges:** Administrative users have elevated permissions, including:
    * Viewing all posts, including unpublished ones.
    * Exporting all blog posts to an Excel file.
    * Accessing and downloading generated weekly blog post reports.
    * Access to specific admin-only routes enforced by middleware.
* **Post Management:**
    * Create, view, update, and delete blog posts.
    * Rich text content support for post bodies.
    * Categorization of posts using tags.
    * Search functionality for posts by title, body, and author.
* **Comments System:**
    * Users can comment on posts.
    * Support for nested comments (replies).
* **Liking System:** Users can like/unlike posts and comments polymorphically.
* **Scheduled Publishing:** Authors can schedule posts to be published at a future date using Laravel Queues.
* **Notifications:** Users are notified when a new post is published.
* **Role-Based Access Control (RBAC):** Implemented using Laravel Policies to manage permissions for viewing, updating, and deleting posts and comments based on user roles (authenticated, author, admin, unauthenticated).
* **Middleware:** Custom middleware for admin access and rate limiting on specific actions.
* **Weekly Reports:** A console command to generate weekly post reports as Excel files, which can be stored locally and downloaded.
* **External API Integration:** Fetches and formats announcement data from an external API (e.g., Sokan Academy announcements).
* **Database Seeding & Factories:** Includes factories for generating realistic dummy data (users, posts, tags) to facilitate development and testing.
