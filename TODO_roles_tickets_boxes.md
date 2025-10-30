# TODO: Role Rename + Ticket/Chat + Box Data

## Roles & Permissions
- [x] Rename roles: Client → CS, Developer → Produksi, Manager → Finance
- [x] Update seeders to create new roles and assign users
- [x] Update controller queries using role filters (Project/Invoice/Delivery/Register)
- [x] Update rates-per-role config (Finance/Produksi/CS)
- [x] Update tests to use new role names
- [x] Adjust email copy that says "Client" -> "CS" where appropriate

Files: `database/seeders/RoleSeeder.php`, `database/seeders/UserSeeder.php`, `database/seeders/PermissionsSeeder.php`, `app/Http/Controllers/*`, `config/time.php`

## Ticketing & Chat (per Project, with Queue)
- [x] Migrations: `tickets`, `ticket_messages`
- [x] Models: `Ticket`, `TicketMessage`
- [x] Controller: create/show/edit/close with queue logic
- [x] Message controller: post message + file upload
- [x] Routes: tickets resource + close + message store
- [x] Minimal views: index/create/show/edit
- [x] Switch routes/controllers to tickets.* permissions
- [x] Policies for tickets (optional granular per-record)
- [x] Add notifications (optional) on new message/ticket
- [x] Add per-project auto sequence number on tickets

Files: `database/migrations/2025_10_30_100100_create_tickets_table.php`, `database/migrations/2025_10_30_100110_create_ticket_messages_table.php`, `app/Models/Ticket.php`, `app/Models/TicketMessage.php`, `app/Http/Controllers/TicketController.php`, `app/Http/Controllers/TicketMessageController.php`, `routes/web.php`, `resources/views/tickets/*`

## Project Box Data
- [x] Migration: `box_types`
- [x] Migration: `project_boxes` (size, shape, mockup_path)
- [x] Models: `BoxType`, `ProjectBox`
- [x] Add relations on `Project`
- [x] CRUD UI for BoxType
- [x] CRUD UI for ProjectBox
- [x] Integrate Box UI into Project show/edit pages

Files: `database/migrations/2025_10_30_100120_create_box_types_table.php`, `database/migrations/2025_10_30_100130_create_project_boxes_table.php`, `app/Models/BoxType.php`, `app/Models/ProjectBox.php`, `app/Models/Project.php`

## Deployment/Run Steps
- [ ] php artisan migrate
- [ ] php artisan db:seed --class=DatabaseSeeder
- [ ] php artisan storage:link (for ticket attachments)

## Feature Flags & Sidebar
- [x] Disable BOMs via feature flag (routes + sidebar hidden)
- [x] Disable Work Stations via feature flag (routes + sidebar hidden)
- [x] Disable Suppliers via feature flag (routes + sidebar hidden)

## Notes
- Tests still reference old roles (Client/Developer/Manager). Update to CS/Produksi/Finance.
- Email templates show "Client" in some places; switch to "CS" if desired.



