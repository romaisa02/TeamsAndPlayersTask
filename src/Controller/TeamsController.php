<?php

namespace App\Controller;


use App\Entity\Players;
use App\Entity\Teams;
use App\Form\PlayerForm;
use App\Form\TeamForm;
use App\Repository\TeamsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;

class TeamsController extends AbstractController
{
    /**
     * @Route("/teams", name="teams_index")
     */
    public function index(TeamsRepository $teamsRepository): Response
    {

        return $this->render('teams/show.html.twig', [
            'teams' => $teamsRepository->findAll(),
        ]);
    }
    /**
     * @Route("/teams/add", name="teams_add")
     */
    public function addTeam(Request $request): Response
    {
        $team = new Teams();
        $form = $this->createForm(TeamForm::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($team);
            $entityManager->flush();

            return $this->redirectToRoute('teams_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('teams/add.html.twig', [
            'project' => $team,
            'form' => $form,
        ]);
    }
    /**
     * @Route("/teams/players/add/{id}", name="team_players_add")
     */
    public function addPlayer(Request $request,$id,TeamsRepository $teamsRepository): Response
    {
        $player = new Players();
        $team = $teamsRepository->find($id);
        $form = $this->createForm(PlayerForm::class, $player);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $player->setTeamId($team);
            $entityManager->persist($player);
            $entityManager->flush();
            return $this->redirectToRoute('team_showPlayer', ['id'=>$id], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('players/add.html.twig', [
            'player' => $player,
            'form' => $form,
        ]);
        /* $entityManager = $doctrine->getManager();

         $team = new Teams();
         $team->setName('Team B');
         $team->setCountry("USA");
         $team->setBalance(100);

         // tell Doctrine you want to (eventually) save the Team (no queries yet)
         $entityManager->persist($team);

         // actually executes the queries (i.e. the INSERT query)
         $entityManager->flush();

         return new Response('Saved new team with id '.$team->getId());*/
    }
    /**
     * @Route("/teams/{id}", name="team_showPlayer", methods={"GET"})
     */
   public function showPlayers(TeamsRepository $teamsRepository, $id): Response
    {
        $team = $teamsRepository->find($id);

        return $this->render('teams/show_players.html.twig', [
            'players' => $team->getPlayers(),
            'count'=>   count($team->getPlayers()),
            'team'=>$team
        ]);
    }
}
