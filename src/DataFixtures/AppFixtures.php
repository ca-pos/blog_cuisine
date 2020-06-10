<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        for($i = 1; $i <= 30; $i++ ) {
            $recipe = new Recipe();

            $name = $faker->sentence(5);
            $image = $faker->imageUrl(300,300);
            $ingredients = '<p>' . join(' ', $faker->sentences(3)) . '</p>';
            $method = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
            $notes = '<p>' . join('</p><p>', $faker->paragraphs(2)) . '</p>';
            $author = $faker->randomElement($array = array ('Marguerite','Aurélien'));
            $summary = '<p>' . $faker->sentences($nb = 3, $asText = true) . '</p>';

            $recipe->setName($name)
                    ->setImage($image)
                    ->setIngredients($ingredients)
                    ->setMethod($method)
                    ->setNotes($notes)
                    ->setAuthor($author)
                    ->setSummary($summary);
            
            $manager->persist($recipe);    
        }

        $manager->flush();
    }
}
