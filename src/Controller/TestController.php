<?php

namespace App\Controller;

use App\Entity\SchoolYear;
use App\Entity\Tag;
use App\Entity\Student;
use DateTime;
use Doctrine\ORM\Repository\RepositoryFactory;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/test')]
class TestController extends AbstractController
{
    #[Route('/tag', name: 'app_test_tag')]
    public function tag(ManagerRegistry $doctrine): Response
    {
        //appelle l'entityManager puis recup le repo Tags
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Tag::class);
        $studentRepository = $em->getRepository(Student::class);


        //création nouvel objet
        $foo = new Tag();
        $foo->setName('Foo');
        $foo->setDescription('Foo Bar Baz');
        $em->persist($foo);
        
        try {
            $em->flush();
        } catch (Exception $e) {
            //gerer l'erreur
            dump($e->getMessage());
        }
        
        //recup objet id = 1
        $tag = $repository->find(1);

        

        //recup objet id = 4
        $tag4 = $repository->find(4);
        //modif d'un objet existant
        $tag4->setName('Python');
        $tag4->setDescription(null);
        //pas la peine de persist, necessaire uniquement pour création.
        $em->flush();

        // recuperation du student dont l'id est 1
        $student = $studentRepository->find(1);
        //association du tag4 au student 1
        $student->addTag($tag4);
        $em->flush();

        //recup pour suppresion objet id 15
        $tag15 = $repository->find(15);
        //si $tag15 existe
        if ($tag15) {
            $em->remove($tag15);
            $em->flush();
        }

        //recuperation d'un tag dont le nom est css
        //permet de ne trouver qu'un seul objet
        $cssTag = $repository->findOneBy([
            //critère de recherche
            'name' => 'CSS',
        ]);

        //recuperation de tous les tags dont la description est nul
        $nullDescriptionTags = $repository->findBy([
            //critère de recherche
            'description' => null,
        ], [
            //critére de tri
            'name' => 'ASC'
        ]);
        //OU BIEN : avec la fonction créé dans tagRepository
        // $NullDescriptionTags = $repository->findByNullDescription();


        //recuperatin de tous les tags avec description
        //grace a fonction créée dans TagRepository
        $notNullDescriptionTags = $repository->findByNotNullDescription();



        //recuepere liste de tous les tags
        $tags = $repository->findAll();

        //recuperation des tags qui contiennent certains mots clés
        $keywordTags1 = $repository->findByKeyword('HTML');
        $keywordTags2 = $repository->findByKeyword('Dolorem');

        //recuperation de tags à partir de schoolyear
        $schoolYearRepository = $em->getRepository(SchoolYear::class);
        $schoolYear = $schoolYearRepository->find(4);
        $schoolYearTags = $repository->findBySchoolYear($schoolYear);

        //maj des relations d'un tag
        //ajout à student 2 du tag 1(html)
        $student = $studentRepository->find(2);
        $htmlTag = $repository->find(1);
        $htmlTag->addStudent($student);
        $em->flush();

        $title = 'Test des tags';

        return $this->render('test/tag.html.twig', [
            'title' => $title,
            'tags' => $tags,
            'tag' => $tag,
            'cssTag' => $cssTag,
            'nullDescriptionTags' => $nullDescriptionTags,
            'notNullDescriptionTags' => $notNullDescriptionTags,
            'keywordTags1' => $keywordTags1,
            'keywordTags2' => $keywordTags2,
            'schoolYearTags' => $schoolYearTags,
            'htmlTag' => $htmlTag,
        ]);
    }

    #[Route('/school-year', name: 'app_test_schoolyear')]
    public function schoolYear(ManagerRegistry $doctrine): Response
    {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(SchoolYear::class);

        //creation nouvelle promo
        $bill = new SchoolYear();
        $bill->setName('Bill Gates');
        $bill->setDescription('Microsoft Cie');
        $bill->setStartDate(new DateTime('2022-09-01'));
        $em->persist($bill);
        $em->flush();

        //suppresion promo id 15
        $promo15 = $repository->find(15);

        if ($promo15) {
            $em->remove($promo15);
            $em->flush();
        }

        //modif promo existante
        $promo4 = $repository->find(4);
        $promo4->SetName('Babar');
        $em->flush();

        //liste complete
        $schoolYears = $repository->findAll();

        //selec schoolyear id = 1
        $schoolYear1 = $repository->find(1); 


        $title = 'Test des School Years';

        return $this->render('test/school-year.html.twig', [
            'title' => $title,
            'schoolYear1' => $schoolYear1,
            'schoolYears' => $schoolYears,
        ]);
    }

    
}
