# Administration Plugin

WordPress plugin for managing program administration and user synchronization with Ultimate Member.

## Description
This plugin provides functionality for:
- User synchronization with Ultimate Member
- Program management
- Role management
- Check-in system

## Installation
1. Upload the plugin files to `/wp-content/plugins/administration`
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Configure the plugin settings

## Requirements
- WordPress 5.0 or higher
- Ultimate Member plugin
- PHP 7.4 or higher

## Changelog
### 1.0.0
- Initial release
- Basic functionality
- User synchronization
- Program management

1. Directory Structure

administration/
│
├── administration.php                # Main plugin bootstrap file
├── includes/                         # Core PHP classes (logic, data, API, admin, etc.)
│   ├── activator/                    # Activation/deactivation logic
│   ├── admin/                        # Admin dashboard modules (each feature in its own file)
│   ├── api/                          # REST API endpoints
│   ├── database/                     # DB schema, migrations, and data access
│   ├── public/                       # Public-facing shortcodes, endpoints, and logic
│   ├── sync/                         # User sync logic (e.g., Ultimate Member integration)
│   └── ajax/                         # AJAX handlers
│
├── assets/
│   ├── css/                          # Centralized stylesheets (admin, public, shared)
│   └── js/                           # Centralized JavaScript (admin, public, shared)
│
├── templates/                        # PHP templates for rendering UI (admin, public, partials)
│   ├── admin/                        # Admin-specific templates
│   ├── public/                       # Public-facing templates
│   └── partials/                     # Reusable template parts
│
├── languages/                        # Translation files (.pot, .po, .mo)
│
└── README.md                         # This file