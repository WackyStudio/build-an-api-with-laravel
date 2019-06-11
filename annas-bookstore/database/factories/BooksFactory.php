<?php

use Faker\Generator as Faker;

$factory->define(App\Book::class, function (Faker $faker) {
    return [
        'title' => $faker->name,
        'description' => $faker->sentence,
        'publication_year' => (string)$faker->year
    ];
});
