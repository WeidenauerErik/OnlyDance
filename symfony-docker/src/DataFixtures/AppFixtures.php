<?php

namespace App\DataFixtures;

use App\Entity\Badge;
use App\Entity\Checklist;
use App\Entity\Dance;
use App\Entity\Step;
use App\Entity\Stepsequence;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Finder\Finder;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}


    public function load(ObjectManager $manager): void
    {

        $dances = [
            ['name' => 'Wiener Walzer', 'tact' => '3/4', 'difficulty' => 5, 'defaultBPM' => 60],
            ['name' => 'Langsamer Walzer', 'tact' => '3/4', 'difficulty' => 3, 'defaultBPM' => 30],
            ['name' => 'Cha Cha', 'tact' => '4/4', 'difficulty' => 2, 'defaultBPM' => 30],
            ['name' => 'Boogie', 'tact' => '4/4', 'difficulty' => 1, 'defaultBPM' => 40],
            ['name' => 'Quickstep', 'tact' => '4/4', 'difficulty' => 2, 'defaultBPM' => 50],
            ['name' => 'Tango', 'tact' => '4/8', 'difficulty' => 3, 'defaultBPM' => 31],
            ['name' => 'Rumba', 'tact' => '4/4', 'difficulty' => 3, 'defaultBPM' => 30],
            ['name' => 'Samba', 'tact' => '2/4', 'difficulty' => 4, 'defaultBPM' => 50],
            ['name' => 'Paso Doble', 'tact' => '2/4', 'difficulty' => 3, 'defaultBPM' => 60],
            ['name' => 'Jive', 'tact' => '4/4', 'difficulty' => 4, 'defaultBPM' => 40],
        ];

        foreach ($dances as $data) {
            $dance = new Dance();
            $dance->setName($data['name']);
            $dance->setTact($data['tact']);
            $dance->setDifficulty($data['difficulty']);
            $dance->setDefaultBPM($data['defaultBPM']);
            $manager->persist($dance);
        }

        $badgeNames = ['Bronze', 'Silber', 'Gold', 'Gold Star'];

        foreach ($badgeNames as $name) {
            $badge = new Badge();
            $badge->setName($name);
            $manager->persist($badge);
        }

        $manager->flush();

        $dataDir = __DIR__ . '/data/stepsequences'; // Pfad zum JSON-Ordner
        $finder = new Finder();
        if (!is_dir($dataDir)) {
            throw new \RuntimeException("Directory not found: " . $dataDir);
        }
        $finder->files()->in($dataDir)->name('*.json');

        if ($finder->count() === 0) {
            throw new \RuntimeException("No JSON files found in: " . $dataDir);
        }

        $index = 0;
        foreach ($finder as $file) {
            $content = $file->getContents();
            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException("JSON decode error in file " . $file->getFilename() . ": " . json_last_error_msg());
            }

            if (!isset($data['dance_id'], $data['badge_id'], $data['name'], $data['steps'], $data["difficulty"])) {
                continue;
            }

            $dance = $manager->getRepository(Dance::class)->find($data['dance_id']);
            $badge = $manager->getRepository(Badge::class)->find($data['badge_id']);

            if (!$dance || !$badge) {
                throw new \RuntimeException("Dance not found with ID: " . $data['dance_id']."or Badge not found with ID:" . $data['badge_id']);

            }

            $stepsequence = new Stepsequence();
            $stepsequence->setDance($dance);
            $stepsequence->setBadge($badge);
            $stepsequence->setName($data["name"]);
            $stepsequence->setDifficulty($data["difficulty"]);

            foreach ($data['steps'] as $stepData) {
                $step = new Step();

                $step->setHowquick($stepData['howquick']);

                $step->setM1X($stepData['m1_x']);
                $step->setM1Y($stepData['m1_y']);
                $step->setM1Rotate($stepData['m1_rotate']);
                $step->setM1Heel($stepData['m1_heel']);
                $step->setM1Toe($stepData['m1_toe']);

                $step->setM2X($stepData['m2_x']);
                $step->setM2Y($stepData['m2_y']);
                $step->setM2Rotate($stepData['m2_rotate']);
                $step->setM2Heel($stepData['m2_heel']);
                $step->setM2Toe($stepData['m2_toe']);

                $step->setW1X($stepData['w1_x']);
                $step->setW1Y($stepData['w1_y']);
                $step->setW1Rotate($stepData['w1_rotate']);
                $step->setW1Heel($stepData['w1_heel']);
                $step->setW1Toe($stepData['w1_toe']);

                $step->setW2X($stepData['w2_x']);
                $step->setW2Y($stepData['w2_y']);
                $step->setW2Rotate($stepData['w2_rotate']);
                $step->setW2Heel($stepData['w2_heel']);
                $step->setW2Toe($stepData['w2_toe']);

                $stepsequence->addHowquick($step);
                $manager->persist($step);
            }

            $manager->persist($stepsequence);
            $this->addReference('stepsequence_' . $index, $stepsequence);
            $index++;
        }


        $faker = Factory::create();

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->email());
            $user->setName($faker->name());
            $user->setRoles(['ROLE_USER']);

            $hashedPassword = $this->passwordHasher->hashPassword($user, 'test1234');
            $user->setPassword($hashedPassword);

            $this->addReference('user_' . $i, $user);

            $manager->persist($user);
        }


        for ($i = 0; $i < 20; $i++) {
            $checklist = new Checklist();
            $checklist->setName($faker->sentence(3));

            // Zufälligen User referenzieren
            $randomUser = $this->getReference('user_' . rand(0, 19),User::class);
            $checklist->setUserId($randomUser);

            $checklist->addStepsequence($this->getReference('stepsequence_' . 1,Stepsequence::class));
            $checklist->addStepsequence($this->getReference('stepsequence_' . rand(2, 4),Stepsequence::class));
            $checklist->addStepsequence($this->getReference('stepsequence_' . rand(5, 7),Stepsequence::class));

            $manager->persist($checklist);
        }








        $manager->flush();
    }
}
