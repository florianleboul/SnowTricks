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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class FigureController extends AbstractController
{
    /**
     * @Route("/figure/add", name="figure_add")
     * @param Request $request
     * @param ObjectManager $manager
     * @param FileUploader $fileUploader
     * @return Response
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
        return $this->render("snowtricks/addFigure.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/figure/{id}-{slug}/edit", name="figure_edit")
     * @param Request $request
     * @param ObjectManager $manager
     * @param Figure $figure
     * @param FileUploader $fileUploader
     * @return Response
     */
    public function edit(Request $request, ObjectManager $manager, Figure $figure, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(FigureType::class, $figure);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // For each picture form in form['pictures'], get uploaded image file,
            // copy it to public folder and set url attribute value
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
        return $this->render("snowtricks/editFigure.html.twig", ["form" => $form->createView()]);
    }

    /**
     * @Route("/figure/{id}-{slug}/delete", name="figure_delete")
     * @param ObjectManager $manager
     * @param Figure $figure
     * @return Response
     */
    public function delete(ObjectManager $manager, Figure $figure): Response
    {
        $manager->remove($figure);
        $manager->flush();
    }

    /**
     * @Route("/figure/{id}-{slug}", name="figure_show")
     * @param Figure $figure
     * @param DBQueries $DBQueries
     * @return Response
     */
    public function showOne(Figure $figure, DBQueries $DBQueries): Response
    {
        return $this->render("snowtricks/figure.html.twig", [
            "figure" => $figure,
            'comments' => $DBQueries->getLastCommentsForFigure($figure)
        ]);
    }


}
