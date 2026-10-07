<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Database\Seeder;

class ItemSeeder extends Seeder
{
    public function run(): void
    {
        $electronics = Category::where('name', 'Electronics')->first();
        $furniture = Category::where('name', 'Furniture')->first();
        $stationery = Category::where('name', 'Stationery')->first();

        Item::create([
            'category_id' => $electronics->id,
            'name' => 'LCD Projector EPSON',
            'code' => 'ELEC-001',
            'stock' => 5,
            'condition' => 'Good',
        ]);

        Item::create([
            'category_id' => $electronics->id,
            'name' => 'Laptop Dell Latitude',
            'code' => 'ELEC-002',
            'stock' => 8,
            'condition' => 'Good',
        ]);

        Item::create([
            'category_id' => $electronics->id,
            'name' => 'Microphone Wireless',
            'code' => 'ELEC-003',
            'stock' => 4,
            'condition' => 'Fair',
        ]);

        Item::create([
            'category_id' => $electronics->id,
            'name' => 'Digital Camera Sony',
            'code' => 'ELEC-004',
            'stock' => 3,
            'condition' => 'Good',
        ]);

        Item::create([
            'category_id' => $furniture->id,
            'name' => 'Executive Desk',
            'code' => 'FURN-001',
            'stock' => 10,
            'condition' => 'Good',
        ]);

        Item::create([
            'category_id' => $furniture->id,
            'name' => 'Ergonomic Office Chair',
            'code' => 'FURN-002',
            'stock' => 15,
            'condition' => 'Good',
        ]);

        Item::create([
            'category_id' => $furniture->id,
            'name' => 'Mobile Whiteboard',
            'code' => 'FURN-003',
            'stock' => 6,
            'condition' => 'Fair',
        ]);

        Item::create([
            'category_id' => $electronics->id,
            'name' => 'Document Scanner Canon',
            'code' => 'ELEC-005',
            'stock' => 2,
            'condition' => 'Good',
        ]);

        Item::create([
            'category_id' => $electronics->id,
            'name' => 'Portable Bluetooth Speaker',
            'code' => 'ELEC-006',
            'stock' => 5,
            'condition' => 'Good',
        ]);

        Item::create([
            'category_id' => $stationery->id,
            'name' => 'Heavy Duty Paper Shredder',
            'code' => 'STAT-001',
            'stock' => 3,
            'condition' => 'Good',
        ]);
    }
}
