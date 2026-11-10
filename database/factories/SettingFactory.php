<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Setting>
 */
class SettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'bname' => 'Oro Construction Soil and Rock',
            'email' => 'admin@orotrack.local',
            'phone' => '',
            'address' => 'Tagum City, Davao del Norte',
            'currency' => 'PHP',
            'meta_title' => 'OroTrack: A Construction Soil and Rock Inventory and Material Management System',
            'meta_description' => 'Record, monitor, and manage construction soil and rock inventory for Oro Construction Soil and Rock, Tagum City.',
        ];
    }
}
