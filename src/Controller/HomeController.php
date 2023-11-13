<?php

namespace App\Controller;


use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DistributedTicketsRepository;
use App\Repository\TicketsRepository;
use App\Repository\GameRepository;
use App\Repository\WinnersRepository;
use App\Entity\Winners;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;




class HomeController extends AbstractController
{         
    protected $em;
    protected $distributedTicketsRepository;
    protected $ticketsRepository;
    protected $gameRepository;
    protected $winnersRepository;

    public function __construct(EntityManagerInterface $em,WinnersRepository $winnersRepository, DistributedTicketsRepository $distributedTicketsRepository, TicketsRepository $ticketsRepository, GameRepository $gameRepository){
        $this->em = $em;
        $this->distributedTicketsRepository = $distributedTicketsRepository;
        $this->ticketsRepository = $ticketsRepository;
        $this->gameRepository = $gameRepository;
        $this->winnersRepository = $winnersRepository;
    }

    /**
     * @Route("/", name="app_home")
     */
    public function index(Request $request): Response
    {
        return $this->render('home/Homepage.html.twig');
    }
    /**
     * @Route("/about", name="about")
     */
    public function about(Request $request): Response
    {
        return $this->render('home/about.html.twig');
    }
    /**
     * @Route("/The", name="nosthe")
     */
    public function The(Request $request): Response
    {
        return $this->render('home/The.html.twig');
    }
    /**
     * @Route("/produit", name="Produit")
     */
    public function Produuit(Request $request): Response
    {
        return $this->render('home/produit.html.twig');
    }



    public function index1(Request $request): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login', []);  
        }
        else{
            if($user->getRoles()[0] == "ROLE_EMPLOYE"){
                return $this->redirectToRoute('app_employe', []);
            }
            else if($user->getRoles()[0] == "ROLE_ADMIN"){
                return $this->redirectToRoute('app_admin', []);
            }
            else{
                return $this->redirectToRoute('app_bienvenu', []);
            }
        }
    }

    /**
     * @Route("/bienvenu", name="app_bienvenu")
     */
    public function bienvenu(Request $request): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login', []);  
        }
        $ticketsOwnByUser = $this->winnersRepository->findBy(['user' => $user]);
       
        
        return $this->render('home/index.html.twig', [
          
            'ticketsCount' => count($ticketsOwnByUser),
            'ticketsOwnByUser' => $ticketsOwnByUser
        ]);
    }


    /**
     * @Route("/profil", name="profil")
     */
    public function profilAction(Request $request): Response
    {
        $user = $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app_login', []);  
        }
        $ticketsOwnByUser = $this->winnersRepository->findBy(['user' => $user]);
       
        
        return $this->render('home/profil.html.twig');
    }
    /**
     * @Route("/mentions", name="mentions")
     */
    public function montionAction(Request $request): Response
    {
        
        return $this->render('home/cgv.html.twig');
    }        
    
    /**
     * @Route("/codecadeau/check", name="app_code_cadeau_check")
     */
    public function codecadeauCheck(Request $request): Response
    {
        $user = $this->getUser();
        $games = $this->gameRepository->findAll();
        $today = !empty($games) ? $games[0] : null;

        if ($today && $today->getClosingDate() >= new \DateTime('-30 days')) {
            
            $codeCadeau = $request->request->get('codeCadeau');
            $isCodeExist = $this->distributedTicketsRepository->findOneBy(['numero' => $codeCadeau]);

            if ($isCodeExist) {
                $values = [1, 2, 3, 4, 5];
                $probas = [4, 6, 10, 20, 60];
                $result = $this->pickWeightedRandomValue($values, $probas);

                $lot1 = $this->ticketsRepository->find(1)->getTicketsLeft();
                $lot2 = $this->ticketsRepository->find(2)->getTicketsLeft();
                $lot3 = $this->ticketsRepository->find(3)->getTicketsLeft();
                $lot4 = $this->ticketsRepository->find(4)->getTicketsLeft();
                $lot5 = $this->ticketsRepository->find(5)->getTicketsLeft();

                $winningLot = $this->checkvalue($result, $lot1, $lot2, $lot3, $lot4, $lot5);
                $dataTicket = $this->ticketsRepository->find($winningLot);
            
                $winner = new Winners();
                $winner->setUser($user)
                    ->setTicket($dataTicket)
                    ->setSend(false);
                $this->em->persist($winner);

                $dataTicket->setTicketsLeft($dataTicket->getTicketsLeft() - 1);
                $this->em->persist($dataTicket);
                
                $this->em->remove($isCodeExist); // Supprimer l'objet et non l'array
                $this->em->flush();

                return new JsonResponse($dataTicket->getTitle(), 200);
            }
            return new JsonResponse(0, 200);
        }
        return new JsonResponse(1, 200);

            
    }

    function pickWeightedRandomValue($values, $weights) {
        $rand = rand(0,100);
        for($i=0; $i<count($weights); $i++) {
        if($rand <= $weights[$i]) return $values[$i];
        $rand -= $weights[$i];
        }
    }


    function checkvalue($value, $lot1, $lot2, $lot3, $lot4, $lot5){

        if($value === 1){
        if($lot1 > 0){return 1;}
        else if($lot1 <= 0 && $lot2 > 0){return 2;} 
        else if($lot1 <= 0 && $lot2 <= 0 && $lot3 > 0){return 3;}
        else if($lot1 <= 0 && $lot2 <= 0 && $lot3 <= 0 && $lot4 > 0){return 4;}
        else if($lot1 <= 0 && $lot2 <= 0 && $lot3 <= 0 && $lot4 <= 0 && $lot5 > 0){return 5;}
        }
        else if($value === 2){
        if($lot2 > 0){return 2;}
        else if($lot2 <= 0 && $lot3 > 0){return 3;}
        else if($lot2 <= 0 && $lot3 <= 0 && $lot4 > 0){return 4;}
        else if($lot2 <= 0 && $lot3 <= 0 && $lot4 <= 0 && $lot5 > 0){return 5;}
        else if($lot2 <= 0 && $lot3 <= 0 && $lot4 <= 0 && $lot5 <= 0 && $lot1 > 0){return 1;}
        }
        else if($value === 3){
        if($lot3 > 0){return 3;}
        else if($lot3 <= 0 && $lot4 > 0){return 4;}
        else if($lot3 <= 0 && $lot4 <= 0 && $lot5 > 0){return 5;}
        else if($lot3 <= 0 && $lot4 <= 0 && $lot5 <= 0 && $lot2 > 0){return 2;}
        else if($lot3 <= 0 && $lot4 <= 0 && $lot5 <= 0 && $lot2 <= 0 && $lot1 > 0){return 1;}
        }
        else if($value === 4){
        if($lot4 > 0){return 4;}
        else if($lot4 <= 0 && $lot5 > 0){return 5;}
        else if($lot4 <= 0 && $lot5 <= 0 && $lot3 > 0){return 3;}
        else if($lot4 <= 0 && $lot5 <= 0 && $lot3 <= 0 && $lot2 > 0){return 2;}
        else if($lot4 <= 0 && $lot5 <= 0 && $lot3 <= 0 && $lot2 <= 0 && $lot1 > 0){return 1;}
        }
        else if($value === 5){
        if($lot5 > 0){return 5;}
        else if($lot5 <= 0 && $lot4 > 0){return 4;}
        else if($lot5 <= 0 && $lot4 <= 0 && $lot3 > 0){return 3;}
        else if($lot5 <= 0 && $lot4 <= 0 && $lot3 <= 0 && $lot2 > 0){return 2;}
        else if($lot5 <= 0 && $lot4 <= 0 && $lot3 <= 0 && $lot2 <= 0 && $lot1 > 0){return 1;}
        }
    } 
}

// Vous devez également déplacer vos fonctions `pickWeightedRandomValue` et `checkvalue` dans la classe, et les préfixer avec `$this->` lors de l'appel.

