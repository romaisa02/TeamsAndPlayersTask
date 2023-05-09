<?php

namespace App\Controller;

use App\Entity\Players;
use App\Repository\PlayersRepository;
use App\Repository\TeamsRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use App\Form\AllTeamsForm;

class PlayersController extends AbstractController
{

    /**
     * @Route("/players", name="app_players")
     */
    public function index(): Response
    {
        return $this->render('players/index.html.twig', [
            'controller_name' => 'PlayersController',
        ]);
    }
    /**
     * @Route("/player/{id}", name="player_show")
     */
    public function show(PlayersRepository $playerRepository, $id): Response
    {
        $player = $playerRepository->find($id);

        return $this->render('players/show.html.twig', [
            'player' => $playerRepository->find($id),
            'team'=>['name'=>$player->getTeamId()->getName()]
        ]);
    }

    /**
     * @Route("/player/sell/{id}", name="player_sell")
     */
    public function sell(Request $request,TeamsRepository $teamsRepository, PlayersRepository $playerRepository,$id): Response
    {
        $allTeams = $teamsRepository->findAll();
        $player = $playerRepository->find($id);
        $defaultData = ['message' => 'Player sell/buy'];

        $form = $this->createFormBuilder($defaultData)
            ->add('id',EntityType::class,array(
                'class'=>'App\Entity\Teams',
                'label'=>'Team Name',
                'choices'=>array($allTeams),
                'choice_label' => function($allTeams, $key, $index) {
                    return strtoupper($allTeams->getName().'-'.$allTeams->getBalance());
                },
            ))->add('price',TextType::class,array('required'=>true))
            ->getForm();
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $team = $form->getData()['id'];
            if($form->getData()['price'] <= $team->getBalance()) {
                $player->setTeamId($form->getData()['id']);
                $newBalance = $team->getBalance()-$form->getData()['price'];
                $team->setBalance($newBalance);
                $entityManager->persist($player);
                $entityManager->persist($team);

                $entityManager->flush();
            }
            return $this->redirectToRoute('team_showPlayer', ['id'=>$team->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('players/sell.html.twig', [
            'form' => $form,
            'player' => $player
        ]);

    }

}
