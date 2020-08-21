<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Role;
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
        $faker = Factory::create('fr_FR');

        // création d'un utilisateur ADMIN
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);

        $adminUser = new User();
        $adminUser->setPseudo('admin')
                  ->setFirstName('Jean')
                  ->setLastName('Poss')
                  ->setEmail('admin@blog-cuisine.net')
                  ->setHash($this->encoder->encodePassword($adminUser, 'aaaaaaaa'))
                  ->setIntroduction('C\'est moi l\'administrateur de ce site')
                  ->setAvatar('https://randomuser.me/api/portraits/men/18.jpg')
                  ->addUserRole($adminRole);
        $manager->persist($adminUser);

        // gestion des utilisateurs
        $users = [];
        for($i = 1; $i<=10; $i++) {
            $user = new User();

            $url = 'https://randomuser.me/api/portraits/';
            $genre = $faker->randomElement(['men/','women/']);
            $picId = $faker->numberBetween(1, 99) . '.jpg';
            $pic = $url . $genre . $picId;
            $prenom = ($genre === 'men/')?$faker->firstNameMale:$faker->firstNameFemale;

            $hash = $this->encoder->encodePassword($user, 'password');

            $user->setPseudo($faker->unique()->word)
                 ->setfirstName($prenom)
                 ->setlastName($faker->lastName)
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
