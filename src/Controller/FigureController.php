<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Figure;
use App\Entity\Media;
use App\Entity\Picture;
use App\Entity\User;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\FigureType;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\FigureRepository;
use App\Service\DBQueries;
use App\Service\FileUploader;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\Persistence\ObjectManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureController extends AbstractController
{
    /**
     * @Route("/figure/add", name="figure_add", methods={"GET"})
     * @param Request $request
     * @param ObjectManager $manager
     * @param FileUploader $fileUploader
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request, ObjectManager $manager, FileUploader $fileUploader): Response
    {
        $figure = new Figure();
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $fileUploader->uploadPictures($form->get('pictures')->getData());

            $figure = $form->getData();

            $manager->persist($figure);
            $manager->flush();


            return $this->redirectToRoute('figure_show', ['id' => $figure->getId(), 'slug' => $figure->getSlug()]);
        }
        return $this->render("figure/addFigure.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/figure/{id}-{slug}/edit", name="figure_edit", methods={"GET"})
     * @param Request $request
     * @param ObjectManager $manager
     * @param Figure $figure
     * @param FileUploader $fileUploader
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, ObjectManager $manager, Figure $figure, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('pictures')->getData() as $picture) {
                $pictureToUpload = $picture->getImage();
                if ($pictureToUpload) {
                    $picture->setUrl($fileUploader->uploadPicture($pictureToUpload));
                }
            }
            $manager->persist($figure);
            $manager->flush();
            return $this->redirectToRoute('figure_show', ['id' => $figure->getId(), 'slug' => $figure->getSlug()]);
        }
        return $this->render("figure/editFigure.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/figure/{id}-{slug}/delete", name="figure_delete", methods={"GET"})
     * @param ObjectManager $manager
     * @param Figure $figure
     * @return Response
     * @IsGranted("ROLE_USER")
     */
    public function delete(ObjectManager $manager, Figure $figure): Response
    {
        $manager->remove($figure);
        $manager->flush();
    }

    /**
     * @Route("/figure/{id}-{slug}", name="figure_show", methods={"GET"})
     * @param Figure $figure
     * @param DBQueries $DBQueries
     * @return Response
     */
    public function showOne(Figure $figure, DBQueries $DBQueries): Response
    {
        return $this->render("figure/figure.html.twig", [
            "figure" => $figure,
            'comments' => $DBQueries->getLastCommentsForFigure($figure)
        ]);
    }


}
