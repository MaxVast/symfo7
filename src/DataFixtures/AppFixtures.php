<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Event;
use App\Entity\Registration;
use App\Entity\User;
use App\Enum\EventStatus;
use App\Enum\RegistrationStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Polyfill\Intl\Normalizer\Normalizer;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $this->user($manager, 'Max', 'maxencevast@gmail.com', 'azerty', ['ROLE_USER','ROLE_ADMIN']);
        $org = $this->user($manager, 'organizer', 'organizer@eventhub.test', 'azerty', ['ROLE_ORGANIZER']);
        $this->user($manager, 'admin', 'admin@eventhub.test', 'azerty', ['ROLE_ADMIN']);
        $user = $this->user($manager, 'user', 'user@eventhub.test', 'azerty', ['ROLE_USER']);


        $cats = [];
        foreach (['Conférence', 'Atelier', 'Concert', 'Sport', 'Communauté', 'Tech'] as $name) {
            $normalise = normalizer_normalize($name, Normalizer::FORM_D);
            $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', iconv('UTF-8', 'ASCII//IGNORE', $normalise)), '-'));

            $category = (new Category())
                ->setName($name)
                ->setSlug($slug);

            $manager->persist($category);
            $cats[] = $category;
        }

        $events = [];
        for ($i = 1; $i <= 8; $i++) {
            $start = new \DateTimeImmutable('+' . ($i + 1) . ' days 18:00');
            $statuses = EventStatus::cases();
            $randomStatus = $statuses[array_rand($statuses)];

            $event = (new Event())
                ->setTitle('EventHub #' . $i)
                ->setSlug('eventhub-' . $i)
                ->setDescription('Un événement de démonstration EventHub.')
                ->setStartAt($start)
                ->setEndAt($start->modify('+5 hours'))
                ->setCapacity(20 + $i * 5)
                ->setStatus($randomStatus)
                ->setOrganizer($org)
                ->setCategory($cats[$i % count($cats)]);

            $manager->persist($event);
            $events[] = $event;
        }

        foreach ($events as $event) {
            if ($event->getStatus() ===  EventStatus::Published) {
                $registration = new Registration();
                $registration->setEvent($event);
                $registration->setUser($user);

                $statuses = RegistrationStatus::cases();
                $randomStatus = $statuses[array_rand($statuses)];
                $registration->setStatus($randomStatus);

                $manager->persist($registration);
            }
        }

        $manager->flush();
    }

    private function user(ObjectManager $manager, string $username, string $email, string $password, array $roles): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $user->setRoles($roles);
        $user->setPassword($this->hasher->hashPassword($user, $password));
        $manager->persist($user);
        return $user;
    }
}
