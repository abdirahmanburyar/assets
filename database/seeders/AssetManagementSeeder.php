<?php

namespace Database\Seeders;

use App\Models\Assignee;
use App\Models\Category;
use App\Models\Location;
use App\Models\Region;
use App\Models\SubLocation;
use App\Models\FundedSource;
use Illuminate\Database\Seeder;

class AssetManagementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Regions
        $regions = [
            'US East Coast',
            'US West Coast',
            'Europe Central',
            'Asia Pacific',
        ];

        foreach ($regions as $regionName) {
            Region::create(['name' => $regionName]);
        }

        // Create Categories
        $categories = [
            [
                'name' => 'Computers & IT',
                'description' => 'Computer equipment, servers, and IT infrastructure',
                'color' => '#3B82F6',
                'is_active' => true,
            ],
            [
                'name' => 'Office Furniture',
                'description' => 'Desks, chairs, cabinets, and office furnishings',
                'color' => '#10B981',
                'is_active' => true,
            ],
            [
                'name' => 'Vehicles',
                'description' => 'Company vehicles and transportation equipment',
                'color' => '#F59E0B',
                'is_active' => true,
            ],
            [
                'name' => 'Machinery',
                'description' => 'Industrial machinery and production equipment',
                'color' => '#EF4444',
                'is_active' => true,
            ],
            [
                'name' => 'Electronics',
                'description' => 'Electronic devices and communication equipment',
                'color' => '#8B5CF6',
                'is_active' => true,
            ],
            [
                'name' => 'Tools & Equipment',
                'description' => 'Hand tools, power tools, and maintenance equipment',
                'color' => '#06B6D4',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            Category::create($categoryData);
        }

        // Create Locations
        $locations = [
            [
                'region_id' => Region::where('name', 'US East Coast')->first()->id,
                'name' => 'New York Headquarters',
            ],
            [
                'region_id' => Region::where('name', 'US East Coast')->first()->id,
                'name' => 'Boston Office',
            ],
            [
                'region_id' => Region::where('name', 'US West Coast')->first()->id,
                'name' => 'San Francisco Office',
            ],
            [
                'region_id' => Region::where('name', 'Europe Central')->first()->id,
                'name' => 'Berlin Office',
            ],
        ];

        foreach ($locations as $locationData) {
            Location::create($locationData);
        }

        // Create Sub-Locations
        $subLocations = [
            [
                'location_id' => Location::where('name', 'New York Headquarters')->first()->id,
                'name' => 'Main Building',
            ],
            [
                'location_id' => Location::where('name', 'New York Headquarters')->first()->id,
                'name' => 'IT Department',
            ],
            [
                'location_id' => Location::where('name', 'New York Headquarters')->first()->id,
                'name' => 'Server Room',
            ],
            [
                'location_id' => Location::where('name', 'San Francisco Office')->first()->id,
                'name' => 'Development Lab',
            ],
        ];

        foreach ($subLocations as $subLocationData) {
            SubLocation::create($subLocationData);
        }

        // Create Funded Sources
        $fundedSources = [
            [
                'name' => 'Federal Grant Program',
                'code' => 'FED-GRANT',
                'is_active' => true,
            ],
            [
                'name' => 'State Department of Technology',
                'code' => 'STATE-TECH',
                'is_active' => true,
            ],
            [
                'name' => 'Local Government Fund',
                'code' => 'LOCAL-GOV',
                'is_active' => true,
            ],
            [
                'name' => 'Private Foundation Grant',
                'code' => 'PRIVATE-FND',
                'is_active' => true,
            ],
        ];

        foreach ($fundedSources as $fundedSourceData) {
            FundedSource::create($fundedSourceData);
        }

        // Create Assignees
        $assignees = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice.johnson@company.com',
                'phone' => '+1-555-0101',
                'department' => 'IT',
                'employee_id' => 'EMP001',
                'notes' => 'IT Manager',
                'is_active' => true,
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob.smith@company.com',
                'phone' => '+1-555-0102',
                'department' => 'Operations',
                'employee_id' => 'EMP002',
                'notes' => 'Operations Director',
                'is_active' => true,
            ],
            [
                'name' => 'Carol Davis',
                'email' => 'carol.davis@company.com',
                'phone' => '+1-555-0103',
                'department' => 'Finance',
                'employee_id' => 'EMP003',
                'notes' => 'Finance Manager',
                'is_active' => true,
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david.wilson@company.com',
                'phone' => '+1-555-0104',
                'department' => 'HR',
                'employee_id' => 'EMP004',
                'notes' => 'HR Director',
                'is_active' => true,
            ],
        ];

        foreach ($assignees as $assigneeData) {
            Assignee::create($assigneeData);
        }

        $this->command->info('Asset Management data seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- ' . Region::count() . ' Regions');
        $this->command->info('- ' . Category::count() . ' Categories');
        $this->command->info('- ' . Location::count() . ' Locations');
        $this->command->info('- ' . SubLocation::count() . ' Sub-Locations');
        $this->command->info('- ' . FundedSource::count() . ' Funded Sources');
        $this->command->info('- ' . Assignee::count() . ' Assignees');
    }
}
