<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Room;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Room::create([
            'id'            => 1,
            'name'          => 'Ruang rapat Kompeten 1 (2.A)',
            'description'   => 'Ruang meeting Lantai 2A',
            'capacity'      => 20,
        ]);

        Room::create([
            'id'            => 2,
            'name'          => 'Ruang Harmonis 1  (3.A.1)',
            'description'   => 'Ruang meeting Lantai 3A',
            'capacity'      => 15,
        ]);

        Room::create([
            'id'            => 3,
            'name'          => 'Ruang Harmonis 2  (3.A.2)',
            'description'   => 'Ruang meeting Lantai 3A',
            'capacity'      => 15,
        ]);

        Room::create([
            'id'            => 4,
            'name'          => 'Ruang Loyal 1 (4.A.1)',
            'description'   => 'Ruang meeting Lantai 4A',
            'capacity'      => 15,
        ]);
        Room::create([
            'id'            => 5,
            'name'          => 'Ruang Loyal 2 (4.A.1)',
            'description'   => 'Ruang meeting Lantai 4A',
            'capacity'      => 15,
        ]);
        Room::create([
            'id'            => 6,
            'name'          => 'Ruang Adaptif 1 (6.A.1)',
            'description'   => 'Ruang meeting Lantai 6A',
            'capacity'      => 15,
        ]);
        Room::create([
            'id'            => 7,
            'name'          => 'Ruang Adaptif 2 (6.A.1)',
            'description'   => 'Ruang meeting Lantai 6A',
            'capacity'      => 15,
        ]);
        Room::create([
            'id'            => 8,
            'name'          => 'Ruang Selat Malaka',
            'description'   => 'Ruang meeting Lantai 7A',
            'capacity'      => 15,
        ]);
        Room::create([
            'id'            => 9,
            'name'          => 'Ruang Selat Malaka 1',
            'description'   => 'Ruang meeting Lantai 7A',
            'capacity'      => 15,
        ]);
        Room::create([
            'id'            => 10,
            'name'          => 'Ruang Selat Malaka 2',
            'description'   => 'Ruang meeting Lantai 7A',
            'capacity'      => 15,
        ]);
        Room::create([
            'id'            => 11,
            'name'          => 'Ruang Selat Malaka 3',
            'description'   => 'Ruang meeting Lantai 7A',
            'capacity'      => 15,
        ]);
    }
}
