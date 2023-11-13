<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RobotsController
{
    /**
     * @Route("/robots.txt", name="robots_txt", defaults={"_format"="txt"})
     */
    public function robotsTxt(): Response
    {
        // Générez le contenu du fichier robots.txt ici
        // $content = "User-agent: *\nDisallow: /chemin-vers-une-page-interdite/\n";

        // Créez une réponse avec le contenu du fichier robots.txt
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/plain');

        return $response;
    }
}
