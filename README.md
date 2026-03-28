# Mi'mar Platform  
## Real Estate Services and Smart Estimation System

## Project Overview
Mi'mar is a digital platform developed to organize and improve the real estate services sector by connecting service providers with clients inside a structured and supervised environment.  
The project aims to replace traditional and unorganized communication methods with a reliable system that supports account verification, business account approval, service review, order management, ratings, favorites, reports, notifications, and direct communication between users and service providers. The system description and functional requirements also specify these core workflows, including business account review, service approval, browsing and filtering, ordering, ratings, favorites, reports, and administrative control. fileciteturn0file0L1-L17 fileciteturn0file0L56-L71

In addition to the core marketplace functions, the project includes an advanced proposed feature: a **quantity and cost estimation module** for small construction and finishing projects. This feature transforms the platform from a digital marketplace into a decision-support tool that helps users estimate materials, costs, and related services before التنفيذ. The uploaded project document describes this as a key practical enhancement to support planning and reduce randomness in cost estimation. fileciteturn0file0L1-L17

---

## Problem Statement
The real estate services field often depends on direct personal communication and informal coordination, which leads to:
- Difficulty finding suitable service providers
- Weak organization of requests and offers
- Limited transparency and trust
- No structured review or approval process
- No reliable method for ratings, reporting, and service quality control

Mi'mar addresses these issues through a managed platform with role-based administration and clear workflows for users, business accounts, and services. fileciteturn0file0L1-L17

---

## Objectives
The main objectives of the project are:
- Build a trusted digital environment for real estate services
- Allow users to create and manage business accounts
- Allow service providers to publish services after approval
- Enable clients to browse, filter, request, rate, and favorite services
- Provide administrators with complete control over categories, cities, reports, business accounts, and services
- Add a smart estimation module for quantities and costs in small construction projects

---

## System Components
The platform consists of three main parts:
1. **Mobile / API side for users**
2. **Administrative dashboard**
3. **Backend system** for business logic, permissions, validation, review, and workflow control

The project document explicitly describes the platform as a complete system composed of a user application, an admin dashboard, and a backend responsible for operations, verification, and permissions. fileciteturn0file0L1-L17

---

## Main Features

### 1. Authentication
- User registration
- OTP-based verification
- Login and logout
- Profile management

### 2. Business Accounts
- Create business account
- Attach business information and location
- Submit for review
- Admin approval or rejection
- Support for more than one business account per user

The document states that a user cannot add or request a service without having an approved business account, and that accounts pass through a review cycle before activation. fileciteturn0file0L17-L33

### 3. Service Management
- Add service through approved business account
- Assign category and subcategory
- Set names, description, price, and location
- Support for review workflow
- Edit and delete services
- Admin approval or rejection of services

The uploaded specification describes the service lifecycle and its approval states as pending, approved, and rejected. fileciteturn0file0L33-L48

### 4. Dynamic Fields
- Dynamic custom fields linked to categories and subcategories
- Support for flexible service attributes
- Used to adapt service forms according to service type

The project requirements include dedicated functional requirements for creating, updating, and deleting dynamic custom fields. fileciteturn0file0L113-L134

### 5. Browsing and Filtering
- Browse categories and subcategories
- Search by name
- Filter by category, subcategory, type, location, and price
- View service details
- View ratings
- Add to favorites
- Report inappropriate services

The project document specifically mentions service browsing, filtering, favorites, reporting, and ratings in the service details flow. fileciteturn0file0L48-L63

### 6. Orders
- Send order for a service
- View sent and received orders
- Accept, reject, or cancel orders
- Track request flow between client and provider

The requirements section includes sending, receiving, accepting, rejecting, and deleting service orders. fileciteturn0file0L113-L134

### 7. Ratings and Reviews
- Users can rate services after valid order flow
- Ratings include score and comments
- Display average rating and user comments

The document states that rating is only allowed after a prior order and acceptance by the service provider. fileciteturn0file0L63-L66

### 8. Favorites
- Add service to favorites
- Remove service from favorites
- View favorite services list

### 9. Reports
- Report inappropriate services
- Admin reviews and resolves reports

### 10. Notifications
- Unread and read notifications
- Mark notification as read
- Mark all as read
- Device token registration for push notifications

### 11. Chat
- Direct conversation between client and service provider
- Text messaging
- Read status
- Conversation linked to a service

The document also lists direct messaging with providers as an additional feature available from the service details page. fileciteturn0file0L66-L71

### 12. Sliders and Static Content
- Advertising slider management
- Privacy Policy page
- Terms of Use page

The functional requirements include management of the advertising slider and cities, while the general system description mentions privacy policy and terms of use in settings. fileciteturn0file0L66-L71 fileciteturn0file0L113-L134

### 13. Estimation Module
A smart module for estimating quantities and costs for small construction or finishing works.  
This feature allows the user to:
- Select the type of work
- Enter dimensions
- Calculate required quantities
- Estimate material cost
- Link results with available services and materials in the platform

This directly supports the proposed academic enhancement described for the project. fileciteturn0file0L1-L17

---

## Functional Requirements Summary
The uploaded project document defines the system through a detailed functional requirements list covering:
- User account creation and profile updates
- Business account workflows
- Service CRUD and approval
- Categories and subcategories management
- Dynamic custom fields
- Orders
- Ratings
- Favorites
- Reports
- Slider management
- Cities
- Roles and permissions
- Admin creation and authorization management fileciteturn0file0L113-L134

---

## Proposed Feature: Quantity and Cost Estimation Module
### Academic Description
To enhance the platform and move beyond its role as only a marketplace for real estate services, a smart module was proposed to support users during the pre-execution phase of small construction projects. This module helps users estimate project quantities and approximate costs before requesting services.

### Purpose
The module transforms the platform into a **Decision Support System** by allowing users to:
- Estimate quantities of required materials
- Approximate project costs
- Improve planning before execution
- Connect estimation results with service providers already available inside the platform

### Inputs
The user can provide:
- Work type
- Length
- Width
- Height when needed
- City

### Outputs
The system can calculate:
- Area or volume
- Estimated material quantities
- Estimated project cost
- Matching available services or providers

### Technical Value
This feature relies on:
- Mathematical formulas
- City-based material pricing
- Database queries
- Organized business logic

It does not require complex artificial intelligence, but it provides strong practical value and future scalability.

---

## Technology Stack
- **Laravel**
- **PHP**
- **MySQL**
- **Laravel Sanctum**
- **Spatie Laravel Permission**
- **Blade**
- **REST API**
- **Bootstrap / Admin UI** depending on current implementation
- **Notifications system**
- **File upload and storage handling**

---

## Project Structure
```text
app/
  Http/
    Controllers/
    Requests/
    Resources/
  Models/
  Services/

database/
  migrations/
  seeders/

routes/
  api.php
  web.php

resources/
  views/