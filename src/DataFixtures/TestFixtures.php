<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Project;
use App\Entity\SchoolYear;
use App\Entity\Student;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as FakerFactory;
use SebastianBergmann\Type\VoidType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class TestFixtures extends Fixture implements FixtureGroupInterface
{
    private $faker;
    private $hasher;
    private $manager;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->faker = FakerFactory::create('fr_FR');
        $this->hasher = $hasher;
    }

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadTags();
        $this->loadSchoolYears();
        $this->loadProjects();
        $this->loadStudents();
    }

    public function loadProjects(): void
    {
        //recup de la liste complete des tags
        $repository = $this->manager->getRepository(Tag::class);
        $tags = $repository->findAll();

        //recuperation d'un tag à partir de son id
        //on recupere un objet de type tag
        $htmlTag = $repository->find(1);
        $cssTag = $repository->find(2);
        //recuperation du 3e (=index 2) el de la liste complete = tag js
        $jsTag = $tags[2];

        //données statics
        $datas = [
            [
                'name' => 'site vitrine 1',
                'description' => null,
                'clientName' => 'Alice',
                'startDate' => new DateTime('2022-10-01'),
                'checkpointDate' => new DateTime('2022-11-01'),
                'deliveryDate' => new DateTime('2022-12-01'),
                'tags' => [$htmlTag, $cssTag],
            ],
            [
                'name' => 'wordpress',
                'description' => null,
                'clientName' => 'Bob',
                'startDate' => new DateTime('2022-02-01'),
                'checkpointDate' => new DateTime('2022-03-01'),
                'deliveryDate' => new DateTime('2022-04-01'),
                'tags' => [$jsTag, $cssTag],
            ],
            [
                'name' => 'API Rest',
                'description' => null,
                'clientName' => 'Charlie',
                'startDate' => new DateTime('2022-05-01'),
                'checkpointDate' => new DateTime('2022-06-01'),
                'deliveryDate' => new DateTime('2022-07-01'),
                'tags' => [$jsTag],
            ],
        ];

        foreach ($datas as $data) {
            $project = new Project();
            $project->setName($data['name']);
            $project->setDescription($data['description']);
            $project->setClientName($data['clientName']);
            $project->setStartDate($data['startDate']);
            $project->setCheckpointDate($data['checkpointDate']);
            $project->setDeliveryDate($data['deliveryDate']);

            foreach ($data['tags'] as $tag) {
                $project->addTag($tag);
            }

            $this->manager->persist($project);
        }

        $this->manager->flush();

        //données dyn
        for ($i = 0; $i < 30; $i++) {
            $project = new Project();
            $words = random_int(3, 5);
            $project->setName($this->faker->sentence($words));

            $words = random_int(5, 15);
            $project->setDescription($this->faker->optional(0.7)->sentence($words));

            $project->setClientName($this->faker->name());

            $project->setStartDate($this->faker->dateTimeBetween('-12 months', '-10 months'));
            $project->setCheckpointDate($this->faker->dateTimeBetween('-10 months', '-8 months'));
            $project->setDeliveryDate($this->faker->dateTimeBetween('-8 months', '-6 months'));

            $tagsCount = random_int(1, 4);
            //depuis la liste de tousles tags donne moi un 1-4 tags
            $shortList = $this->faker->randomElements($tags, $tagsCount);

            foreach ($shortList as $tag) {
                $project->addTag($tag);
            }

            $this->manager->persist($project);
        }

        $this->manager->flush();
    }

    public function loadTags(): void
    {
        //données statiques
        $datas = [
            [
                'name' => 'HTML',
                'description' => null,
            ],
            [
                'name' => 'CSS',
                'description' => null,
            ],
            [
                'name' => 'JS',
                'description' => null,
            ],
        ];

        foreach ($datas as $data) {
            $tag = new Tag();
            $tag->setName($data['name']);
            $tag->setDescription($data['description']);

            $this->manager->persist($tag);
        }

        $this->manager->flush();

        //données dyn
        for ($i = 0; $i < 10; $i++) {
            $tag = new Tag();
            $words = random_int(1, 3);
            $tag->setName($this->faker->unique()->sentence($words));
            $words = random_int(6, 15);
            $tag->setDescription($this->faker->sentence($words));

            $this->manager->persist($tag);
        }

        $this->manager->flush();
    }

    public function loadSchoolYears(): void
    {
        //données static
        $datas = [
            [
                'name' => 'Alan Turing',
                'description' => null,
                'startDate' => new DateTime('2022-01-01'),
                'endDate' => new DateTime('2022-12-31'),
            ],
            [
                'name' => 'John van Neuman',
                'description' => null,
                'startDate' => new DateTime('2022-06-01'),
                'endDate' => new DateTime('2023-05-31'),
            ],
            [
                'name' => 'Brendan Eich',
                'description' => null,
                'startDate' => null,
                'endDate' => null,
            ],
        ];

        foreach ($datas as $data) {
            $schoolYear = new SchoolYear();
            $schoolYear->setName($data['name']);
            $schoolYear->setDescription($data['description']);
            $schoolYear->setStartDate($data['startDate']);
            $schoolYear->setEndDate($data['endDate']);

            $this->manager->persist($schoolYear);
        }

        $this->manager->flush();

        //données dyn
        for ($i = 0; $i < 10; $i++) {
            $schoolYear = new SchoolYear();

            $words = random_int(2, 4);
            $schoolYear->setName($this->faker->unique()->sentence($words));

            $words = random_int(8, 15);
            $schoolYear->setDescription($this->faker->optional(0.7)->sentence($words));

            $startDate = $this->faker->dateTimeBetween('-1 year', '-6 months');
            $schoolYear->setStartDate($startDate);

            $endDate = $this->faker->dateTimeBetween('-6 months', 'now');
            $schoolYear->setEndDate($endDate);

            $this->manager->persist($schoolYear);
        }

        $this->manager->flush();
    }



    public function loadStudents(): void
    {
        //recupere la liste des schoolYear
        $repository = $this->manager->getRepository(SchoolYear::class);
        $schoolYears = $repository->findAll();
        $allanTuring = $repository->find(1);
        $johnVanNeuman = $repository->find(2);
        $brendanEich = $schoolYears[2];

        $repository = $this->manager->getRepository(Tag::class);
        $tags = $repository->findAll();
        $htmlTag = $repository->find(1);
        $cssTag = $repository->find(2);
        $jsTag = $repository->find(3);

        $repository = $this->manager->getRepository(Project::class);
        $projects = $repository->findAll();
        $siteVitrine = $repository->find(1);
        $wordpress = $repository->find(2);
        $apiRest = $repository->find(3);

        //données statiques
        $datas = [
            [
                'email' => 'foo@exemple.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstName' => 'Foo',
                'lastName' => 'Example',
                'schoolYear' => $allanTuring,
                'projects' => [$siteVitrine],
                'tags' => [$htmlTag],
            ],
            [
                'email' => 'bar@exemple.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstName' => 'Bar',
                'lastName' => 'Example',
                'schoolYear' => $johnVanNeuman,
                'projects' => [$wordpress],
                'tags' => [$cssTag],
            ],
            [
                'email' => 'baz@exemple.com',
                'password' => '123',
                'roles' => ['ROLE_USER'],
                'firstName' => 'Baz',
                'lastName' => 'Example',
                'schoolYear' => $brendanEich,
                'projects' => [$apiRest],
                'tags' => [$jsTag],
            ],
        ];

        foreach ($datas as $data) {
            $user = new User();
            $user->setEmail($data['email']);
            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);
            $user->setRoles($data['roles']);

            $this->manager->persist($user);

            $student = new Student();
            $student->setFirstName($data['firstName']);
            $student->setLastName($data['lastName']);
            $student->setSchoolYear($data['schoolYear']);
            //fait le lien entre le student et le user créé juste avant

            //recupération du premier projet de la liste du student
            $project = $data['projects'][0];
            $student->addProject($project);

            //si un seul tags par student
            $tag = $data['tags'][0];
            $student->addTag($tag);

            // //si pls tags par student
            // foreach ($data['tags'] as $tag) {
            //     $student->addTag(($tag));
            // }

            $student->setUser($user);

            $this->manager->persist($student);
        }

        $this->manager->flush();

        //données dynamiques

        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($this->faker->unique()->safeEmail());
            $password = $this->hasher->hashPassword($user, '123');
            $user->setPassword($password);
            $user->setRoles(['ROLE_USER']);
            //un premier persist pour creer le user
            $this->manager->persist($user);

            $student = new Student();
            $student->setFirstName($this->faker->firstName());
            $student->setLastName($this->faker->lastName());
            //selec une promo au hasard ds la liste de toutes les promos
            $schoolYear = $this->faker->randomElement($schoolYears);
            $student->setSchoolYear($schoolYear);
            //selec un projet au hasard ds la liste de tous les projets
            $project = $this->faker->randomElement($projects);
            $student->addProject($project);
            //selec entre 1 et 4 tag au hasard ds la liste de tous les tags
            $tagsCount = random_int(1, 4);
            $shortList = $this->faker->randomElements($tags, $tagsCount);
            foreach ($shortList as $tag) {
                $student->addTag($tag);
            }
            //fait le lien entre le student et le user créé juste avant
            $student->setUser($user);

            //un deuxieme persist pour le student. Doit etre fait apres les users car on associe user à student.
            $this->manager->persist($student);
        }
        $this->manager->flush();
    }
}
