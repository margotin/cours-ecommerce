<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Product;
use Liior\Faker\Prices;
use App\Entity\Category;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Entity\User;
use Bezhanov\Faker\Provider\Commerce;
use Bluemmb\Faker\PicsumPhotosProvider;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    protected $slugger;
    protected $encoder;

    public function __construct(SluggerInterface $slugger, UserPasswordEncoderInterface $encoder)
    {
        $this->slugger = $slugger;
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Prices($faker));
        $faker->addProvider(new Commerce($faker));
        $faker->addProvider(new PicsumPhotosProvider($faker));

        $admin = new User();
        $admin->setEmail("admin@gmail.com")
            ->setFullName("Admin")
            ->setPassword($this->encoder->encodePassword($admin, "password"))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($admin);

        $users = [];
        for ($u = 0; $u < 5; $u++) {
            $user = new User();
            $user->setEmail("user$u@gmail.com")
                ->setFullName($faker->name())
                ->setPassword($this->encoder->encodePassword($user, "password"));

            $users[] = $user;
            $manager->persist($user);
        }


        $products = [];
        for ($c = 0; $c < 3; $c++) {
            $category = new Category();
            $category
                ->setName($faker->department)
                ->setSlug(strtolower($this->slugger->slug($category->getName())))
                ->setOwner($faker->randomElement($users));

            $manager->persist($category);

            for ($p = 0; $p < mt_rand(15, 20); $p++) {
                $product = new Product();
                $product
                    ->setName($faker->productName)
                    ->setPrice($faker->price(4000, 2000))
                    ->setSlug(strtolower($this->slugger->slug($product->getName())))
                    ->setShortDescription($faker->paragraph())
                    ->setMainPicture($faker->imageUrl(200, 200, true))
                    ->setCategory($category);

                $products[] = $product;
                $manager->persist($product);
            }
        }

        for ($p = 0; $p < mt_rand(20, 40); $p++) {
            $purchase = new Purchase();
            $purchase->setFullName($faker->name)
                ->setAddress($faker->streetAddress)
                ->setPostalCode($faker->postcode)
                ->setCity($faker->city)
                ->setUser($faker->randomElement($users))
                ->setTotal(mt_rand(2000, 30000))
                ->setPurchasedAt($faker->dateTimeBetween('-6 months'));

            $selectedProducts = $faker->randomElements($products, mt_rand(3, 5));
            foreach ($selectedProducts as $product) {
                $purchaseItem = new PurchaseItem();
                $purchaseItem->setProduct($product)
                    ->setProductName($product->getName())
                    ->setProductPrice($product->getPrice())
                    ->setQuantity(mt_rand(1, 3))
                    ->setTotal($purchaseItem->getProductPrice() * $purchaseItem->getQuantity())
                    ->setPurchase($purchase);

                $manager->persist($purchaseItem);
            }

            if ($faker->boolean(90)) {
                $purchase->setStatus(Purchase::STATUS_PAID);
            }

            $manager->persist($purchase);
        }

        $manager->flush();
    }
}
