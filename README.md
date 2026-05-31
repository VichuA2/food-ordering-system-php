# FoodiEats - Enterprise Standard Online Food Delivery System

FoodiEats is a highly scalable, real-world standard Online Food Delivery platform (similar to Zomato or Swiggy) built using **Laravel**. The architecture follows industry-best practices, decoupling heavy operations into background queues, enforcing data integrity via transactional safety layers, and isolating security parameters using enterprise-level Role-Based Access Control (RBAC).

## 🚀 Key Architectural Features

### 🔐 1. Multi-Role RBAC (Role-Based Access Control)
The entire backend ecosystem is protected and governed by 5 strict operational user roles managed via the `Spatie Roles & Permissions` package:
- **Admin:** Master coordinator tracking dashboard financial metrics (total orders, revenues), onboarding vendors/riders for safety, and managing global food categories.
- **Employee:** Handles customer care, interacts with support tickets, and manages user logs.
- **Hotel Owner (Vendor):** Manages specific restaurant branch inventories, configures real-time menus, tracks items sold, and reviews transaction breakdowns.
- **Delivery Person:** Accepts/updates live orders, routes delivery paths, and logs tracking states (Pending, Accepted, Picked Up, Delivered).
- **Customer:** Browses hotels, dynamically customizes food cart structures, tracks multi-vendor split-orders, and logs support tickets.

### 🛒 2. Advanced E-Commerce Core & Price Locking Engine
- **Persistent DB-Based Cart:** Engineered with `carts` and `cart_items` schemas to allow persistent customer sessions across multiple devices.
- **Transactional Price Locking:** When an order is checked out, snapshots of item costs are locked down into the transactional logs (`order_items`). If a hotel updates menu prices later, historical financial audit records remain untouched.
- **Multi-Vendor Split Checkout:** Capability to process mixed shopping carts seamlessly by splitting orders based on vendor/hotel boundaries.

### 📨 3. Intelligent Event & Communication Layer
To guarantee top performance, heavy system operations are completely decoupled from the main request-response lifecycle using background worker queues.
- **Automated HTML Email Workflows:** Dispatches professional notification templates step-by-step:
  - *Order Placed* to Customer.
  - *New Order Notification* to Vendor.
  - *New Order Delivery Request* to Delivery Execs.
  - *Live State Updates* to Customer.
- **Support & Incident Automation:** Built-in ticketing systems (`queries`) to track user feedback and customer queries assigned to employees.

---

## 🛠️ Tech Stack & Dev Tools

- **Backend Framework:** PHP Laravel (v13.x / v11.x depending on your version) & Eloquent ORM
- **Database:** MySQL (Relational mappings, Pivot links, and SoftDeletes enabled)
- **Security & RBAC:** Spatie Roles and Permissions
- **Validation Engine:** Segregated Form Requests (`StoreProductRequest`, `UpdateProductRequest`) to isolate core business rules from controllers.
- **Optimization:** Global View Composers for persistent state data rendering (e.g., universal dynamic cart counts).
- **Diagnostics Tools:** Laravel Telescope & Laravel Debugbar integrated for profiling database queries (resolving N+1 issues) and auditing background entries.
- **Frontend Template:** Bootstrap-driven responsive UI integration (Yummy Blade Templates layout system).

---

## 🗄️ Database Blueprints & Models

The architecture relies on high-integrity data mappings:
- **Core Entities:** `User`, `Category`, `Hotel`, `Product` (with image storage paths), `Cart`, `CartItem`, `Order`, `OrderItem`, `Payment` (supporting COD vs Online state tracking), and `Query`.
- **Relational Integrity:** Fully defined Eloquent relationships (`belongsTo`, `hasMany`, and polymorphic pivot linkages for dynamic food mapping).
