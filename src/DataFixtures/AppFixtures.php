<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Media;
use App\Entity\User;
use App\Repository\CategoryRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use PhpParser\Node\Expr\New_;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create("fr_FR");

        // Create fake users
        $users = array();
        for ($i=0; $i<=random_int(6, 12); $i++){
            $users[$i] = new User();
            $users[$i]->setPseudo($faker->firstNameMale)
                      ->setMailAddress($faker->freeEmail)
                      ->setPassword($faker->password )
                      ->setCreationDate($faker->dateTimeBetween("-30 days"))
                      ->setIsValid(true);
            $manager->persist($users[$i]);
        }

        // Create fake categories
        $categories = array();
        for ($i=0; $i<=random_int(3, 8); $i++) {
            $categories[$i] = new Category();
            $categories[$i]->setName(join($faker->words, ' '));
        }


        foreach ($categories as $category){
            // Create figure in category
            for ($i = 0; $i < random_int(3, 5); $i++){
                $figure = new Figure();
                $figure->setAuthor($users[array_rand($users)])
                       ->setName(join($faker->words, ' '))
                       ->setDescription(join($faker->paragraphs, ' '))
                       ->setCreationDate($faker->dateTimeBetween("-30 days"))
                       ->setCategory($category);
                // Adding medias to figure (pictures)
                for ($a=0; $a < rand(3, 6); $a++){
                    $media = new Media();
                    $media->setUrl($faker->imageUrl())
                          ->setFigure($figure)
                          ->setType(1);
                    $manager->persist($media);
                }
                // Adding medias to figure (videos)
                for ($a=0; $a < rand(3, 6); $a++){
                    $media = new Media();
                    $media->setUrl($faker->imageUrl())
                        ->setFigure($figure)
                        ->setType(2);
                    $manager->persist($media);
                }
                // Adding comments to figure
                for ($j = 0; $j < rand(5,10); $j++){
                    $comment = new Comment();
                    $comment->setAuthor($users[array_rand($users)])
                            ->setFigure($figure)
                            ->setCreationDate($faker->dateTimeBetween('-'.$figure->getCreationDate()->diff(new \DateTime())->days.' days'))
                            ->setContent(join($faker->paragraphs, ' '));
                    $manager->persist($comment);
                }
                $manager->persist($figure);
            }
            $manager->persist($category);
        }
        $manager->flush();
    }
}
