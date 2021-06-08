<?php

namespace Database\Factories;

use App\Models\Categories;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoriesFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Categories::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $response = Http::withBasicAuth('multishop@4wimax', '3hQ&ue1x')->get('https://online.moysklad.ru/api/remap/1.2/entity/processingplan');
        $data = $response->json();
        $categories = [];
        for($i=0; $i<count($data['rows']); $i++) {
            $categories[] = $data['rows'][$i]['pathName'];
        }
        $categories = array_unique($categories);

        foreach ($categories as $category) {
            return [
                'name' => $this->faker->name()
            ];
        }
    }
}
