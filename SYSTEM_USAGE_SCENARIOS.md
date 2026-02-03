# Okaro & Associates Property Management System - User Scenarios

This document narrates the typical usage scenarios for each user role within the Okaro & Associates Property Management System.

---

## 1. The Administrator (Executive View)

**Persona:** The CEO or Head of Operations who needs complete oversight and control.

### Scenario: System Initialization & Governance
1.  **System Setup:** The Administrator logs in to the **Dashboard** to view high-level metrics (Total Revenue, Occupancy Rates).
2.  **Property Configuration:** They navigate to **Buildings** to add new property complexes and define their capacity. Within each building, they configure **Units** (apartments/offices), setting their initial status to "Available".
3.  **Staff Onboarding:** The Admin goes to **Users** -> **Add New User** to create accounts for Property Managers.
    *   *Security Step:* If a Manager registers themselves via the public registration page, the Admin receives a notification (or checks the Inactive Users list) and toggles their status to **Active** to grant them access.
4.  **Audit & Oversight:** The Admin reviews the **Payments** and **Maintenance** logs to ensure Managers are keeping records up to date. They can see exactly who created each record via the "Created By" column.

---

## 2. The Property Manager (Operational View)

**Persona:** The day-to-day operator responsible for filling units and collecting rent.

### Scenario: Tenant Onboarding & Lease Management
1.  **Tenant Acquisition:** A prospective tenant agrees to rent a unit. The Manager logs in and navigates to **Tenants** -> **Add New Tenant**.
    *   They enter the tenant's personal details and upload a profile photo.
    *   They assign the tenant to a specific **Building** and **Unit** (manually entering the unit number, e.g., "B204").
    *   *Validation:* The system ensures the unit exists in that building before proceeding.
2.  **Lease Creation:** Once the tenant profile is created, the Manager goes to **Rentals** -> **Create Agreement**.
    *   They select the Tenant and the Unit.
    *   They define the lease terms (Start Date, End Date, Annual Rent).
    *   They upload the signed physical contract (PDF/Image) for digital archiving.
3.  **User Approval:** The tenant registers their own account on the website to access the portal. The Manager sees this new account in the **Users** section (filtered to show only Tenants) and clicks **Activate** to grant them login access.
4.  **Daily Operations:**
    *   **Rent Collection:** When rent is paid, the Manager goes to **Payments** -> **Record Payment**, selecting the specific Rental Agreement and entering the amount/method.
    *   **Maintenance:** When a tenant reports an issue, the Manager views the **Maintenance** section to track the ticket, update its status (e.g., from "Pending" to "In Progress"), and log resolution notes.

---

## 3. The Tenant (End-User View)

**Persona:** A resident or business owner renting a unit.

### Scenario: Living & Reporting
1.  **Registration:** The Tenant visits the website and clicks **Register**. They fill in their details and select "Tenant" as their role.
    *   *Wait Period:* They see a message: "Registration successful. Please wait for admin approval."
2.  **Access:** Once approved by their Manager, they log in to their personal **Dashboard**.
    *   **Lease Info:** They can immediately see their current building, unit number, and lease validity dates.
    *   **Payment History:** They can view a ledger of all their past rent payments to ensure everything is recorded correctly.
3.  **Maintenance Requests:**
    *   The kitchen sink is leaking. The Tenant navigates to **Request Maintenance**.
    *   They fill out a simple form describing the issue ("Leaking tap in kitchen") and set the priority (e.g., "Medium").
    *   They can check back later to see if the Manager has scheduled a repair (Status changed to "In Progress").
