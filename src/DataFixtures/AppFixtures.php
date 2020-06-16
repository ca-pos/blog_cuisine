<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr-FR');

        // gestion des utilisateurs
        $users = [];
        for($i = 1; $i<=10; $i++) {
            $user = new User();

            $url = 'https://randomuser.me/api/portraits/';
            $genre = $faker->randomElement(['men/','women/']);
            $picId = $faker->numberBetween(1, 99) . '.jpg';
            $pic = $url . $genre . $picId;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setPseudo($faker->lastname)
                 ->setEmail($faker->email)
                 ->setHash($hash)
                 ->setIntroduction('<p>' . $faker->sentences($nb = 2, $asText = true) . '</p>')
                 ->setAvatar($pic);

            $manager->persist($user);
            $users[] = $user;

        }
        // gestion des recettes
        for($i = 1; $i <= 30; $i++ ) {
            $recipe = new Recipe();

            $user = $users[mt_rand(1, count($users)-1 )];

            $name = $faker->sentence(5);
            $image = $faker->imageUrl(300,300);
            $ingredients = '<p>' . join(' ', $faker->sentences(3)) . '</p>';
            $method = '<p>' . join('</p><p>', $faker->paragraphs(5)) . '</p>';
            $notes = '<p>' . join('</p><p>', $faker->paragraphs(2)) . '</p>';
/*             
            $author = $faker->randomElement($array = array ('Marguerite','Aurélien','Marguerite','Aurélien'));
 */            
            $summary = '<p>' . $faker->sentences($nb = 3, $asText = true) . '</p>';


            $recipe->setName($name)
                    ->setImage($image)
                    ->setIngredients($ingredients)
                    ->setMethod($method)
                    ->setNotes($notes)
                    ->setAuthor($user)
                    ->setSummary($summary);
            
            $manager->persist($recipe);    
        }

        $manager->flush();
    }
}
