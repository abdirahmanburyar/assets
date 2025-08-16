# Asset Management System

A comprehensive Laravel 12.3.0 + Filament v4 based Asset Management System with advanced features for tracking, managing, and maintaining organizational assets.

## 🚀 Features

### Core Asset Management
- **Asset Tracking**: Complete asset lifecycle management from acquisition to retirement
- **Location Hierarchy**: Region → Location → Sub-Location structure for precise asset placement
- **Category Management**: Organized asset categorization with color coding
- **Assignment Tracking**: Assign assets to employees/departments
- **Vendor Management**: Track suppliers and purchase information

### Advanced Functionality
- **Depreciation Calculation**: Automatic current value calculation based on depreciation rates
- **Maintenance Scheduling**: Configurable maintenance intervals with automatic date calculation
- **Warranty Tracking**: Monitor warranty expiration with alerts
- **Status Management**: Multi-status workflow (pending, in_use, maintenance, retired, rejected)
- **Approval Workflow**: Built-in approval system for new assets

### Document Management
- **File Uploads**: Support for multiple file types (invoices, warranties, manuals, etc.)
- **Document Organization**: Categorized document storage with metadata
- **File Preview**: Image and document preview capabilities
- **Download Management**: Secure file download system

### History & Auditing
- **Complete Audit Trail**: Track all asset changes and modifications
- **Status History**: Monitor asset status changes over time
- **Location Transfers**: Track asset movements between locations
- **Assignment Changes**: Record asset reassignments

### User Interface
- **Filament v4 Admin Panel**: Modern, responsive admin interface
- **Advanced Filtering**: Multi-criteria asset filtering and search
- **Bulk Operations**: Mass approve, reject, and manage assets
- **Real-time Updates**: Livewire-powered dynamic updates
- **Responsive Design**: Mobile-friendly interface

## 🛠️ Technical Stack

- **Backend**: Laravel 12.3.0, PHP 8.3+
- **Admin Panel**: Filament v4
- **Frontend**: Livewire v3, Tailwind CSS
- **Database**: MySQL/PostgreSQL/SQLite
- **File Storage**: Local storage (S3-ready)
- **Excel Import**: maatwebsite/excel
- **Real-time**: Laravel Reverb (Pusher-ready)

## 📋 Requirements

- PHP 8.3+
- Composer
- Node.js & NPM
- Database (MySQL/PostgreSQL/SQLite)
- Web server (Apache/Nginx)

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd assets
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database configuration**
   ```bash
   # Update .env with your database credentials
   php artisan migrate
   php artisan db:seed --class=AssetManagementSeeder
   php artisan db:seed --class=UserSeeder
   ```

6. **Build assets**
   ```bash
   npm run build
   ```

7. **Start the server**
   ```bash
   php artisan serve
   ```

## 🔐 Default Login Credentials

- **Admin User**: admin@assets.com / password
- **Test Users**: 
  - john@assets.com / password
  - jane@assets.com / password

## 📊 Database Structure

### Core Tables
- `regions` - Geographic regions
- `locations` - Physical locations within regions
- `sub_locations` - Specific areas within locations (buildings, floors, rooms)
- `categories` - Asset categories with color coding
- `vendors` - Supplier information
- `assignees` - Employee/department assignments
- `assets` - Main asset records
- `asset_documents` - File attachments
- `asset_histories` - Audit trail

### Key Relationships
- **Region** → **Location** → **Sub-Location** → **Asset**
- **Category** → **Asset**
- **Vendor** → **Asset**
- **Assignee** → **Asset**
- **Asset** → **AssetDocument** (one-to-many)
- **Asset** → **AssetHistory** (one-to-many)

## 🎯 Usage Guide

### Managing Assets

1. **Create New Asset**
   - Navigate to Assets → Create
   - Fill in basic information (tag, description, category)
   - Select location hierarchy (Region → Location → Sub-Location)
   - Set financial details (value, depreciation)
   - Configure maintenance schedule
   - Upload relevant documents

2. **Asset Approval Workflow**
   - New assets default to 'pending' status
   - Admins can approve or reject assets
   - Bulk approval/rejection available
   - Complete audit trail maintained

3. **Asset Maintenance**
   - Mark assets for maintenance
   - Track maintenance completion
   - Automatic next maintenance date calculation
   - Maintenance history logging

4. **Location Management**
   - Create hierarchical location structure
   - Transfer assets between locations
   - Track asset movements
   - Geographic coordinates support

### Document Management

1. **Upload Documents**
   - Support for multiple file types
   - Categorized document storage
   - Metadata tracking (uploader, date, description)
   - File size and type validation

2. **Document Organization**
   - File type categorization (invoice, warranty, manual, other)
   - Search and filter capabilities
   - Preview support for images and PDFs
   - Secure download system

### Reporting & Analytics

1. **Asset Overview**
   - Total asset count and value
   - Category distribution
   - Location-based asset counts
   - Status distribution

2. **Maintenance Tracking**
   - Upcoming maintenance schedules
   - Overdue maintenance alerts
   - Maintenance completion rates

3. **Financial Reporting**
   - Asset depreciation tracking
   - Total asset value over time
   - Category-based value analysis

## 🔧 Configuration

### File Storage
Configure file storage in `config/filesystems.php`:
```php
'asset_documents' => [
    'driver' => 'local',
    'root' => storage_path('app/public/asset-documents'),
    'url' => env('APP_URL').'/storage/asset-documents',
    'visibility' => 'public',
],
```

### Email Notifications
Configure email settings in `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Real-time Updates
Configure broadcasting in `config/broadcasting.php`:
```php
'default' => env('BROADCAST_DRIVER', 'reverb'),
```

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 📝 API Endpoints

The system provides RESTful API endpoints for:
- Asset CRUD operations
- Document management
- Location hierarchy
- Reporting and analytics

## 🔒 Security Features

- **Authentication**: Laravel's built-in authentication system
- **Authorization**: Role-based access control
- **File Security**: Secure file upload and download
- **Audit Logging**: Complete change tracking
- **Input Validation**: Comprehensive form validation
- **CSRF Protection**: Built-in CSRF token protection

## 🚀 Deployment

1. **Production Environment**
   ```bash
   composer install --optimize-autoloader --no-dev
   npm run build
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

2. **Queue Workers**
   ```bash
   php artisan queue:work
   ```

3. **Scheduled Tasks**
   ```bash
   # Add to crontab
   * * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
   ```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## 📄 License

This project is licensed under the MIT License.

## 🆘 Support

For support and questions:
- Create an issue in the repository
- Contact the development team
- Check the documentation

## 🔮 Future Enhancements

- **Mobile App**: Native mobile application
- **Barcode/QR Integration**: Asset scanning capabilities
- **Advanced Analytics**: Business intelligence dashboard
- **Integration APIs**: Third-party system integration
- **Multi-tenant Support**: SaaS deployment ready
- **Advanced Workflows**: Custom approval processes
- **Asset Lifecycle**: End-to-end lifecycle management
- **Predictive Maintenance**: AI-powered maintenance scheduling

---

**Built with ❤️ using Laravel 12.3.0 + Filament v4**
