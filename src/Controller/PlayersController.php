<?php

namespace App\Controller;

use App\Entity\Players;
use App\Repository\PlayersRepository;
use App\Repository\TeamsRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        return $this->render('players/show.html.twig', [
            'player' => $playerRepository->find($id),
            'team'=>['name'=>'dddd']
        ]);
    }

    /**
     * @Route("/players/add", name="app_players_add")
     */
    public function add(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $player = new Players();
        $player->setName('David');
        $player->setSurname("Allan");

        // tell Doctrine you want to (eventually) save the Team (no queries yet)
        $entityManager->persist($player);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new Response('Saved new player with id '.$player->getId());
    }
    /**
     * @Route("/player/sell/{id}", name="player_sell")
     */
    public function sell(PlayersRepository $playerRepository, TeamsRepository $teamsRepository, $id): Response
    {

        $player = $playerRepository->find($id);
        $team = $teamsRepository->find(4);
        if (!$player) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        $player->setTeamId($team);
        $this->getDoctrine()->getManager()->flush();

    }

}
