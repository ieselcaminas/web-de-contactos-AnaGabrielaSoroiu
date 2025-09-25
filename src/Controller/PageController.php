<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PageController extends AbstractController {
    private $contactos = [
        1 => ["nombre" => "Juan Pérez", "telefono" => "524142432", "email" => "juanp@ieselcaminas.org"],
        2 => ["nombre" => "Ana López", "telefono" => "58958448", "email" => "anita@ieselcaminas.org"],
        5 => ["nombre" => "Mario Montero", "telefono" => "5326824", "email" => "mario.mont@ieselcaminas.org"],
        7 => ["nombre" => "Laura Martínez", "telefono" => "42898966", "email" => "lm2000@ieselcaminas.org"],
        9 => ["nombre" => "Nora Jover", "telefono" => "54565859", "email" => "norajover@ieselcaminas.org"]
    ];

    #[Route('/page', name: 'app_page')]
    public function index(): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/PageController.php',
        ]);
    }

    #[Route('/', name: 'inicio')]
    public function inicio(): Response {
        return $this->render('inicio.html.twig');
    }

    #[Route('/contacto/{codigo?1}', name: 'ficha contacto')]
    public function ficha($codigo): Response {
        //return new Response("Datos del contacto con código $codigo");
        /*$resultado = ($this->contactos[$codigo] ?? null);

        if($resultado) {
            $html = "<ul>";
                $html .= "<li>$codigo</li>";
                $html .= "<li>" . $resultado['nombre'] . "</li>";
                $html .= "<li>" . $resultado['telefono'] . "</li>";
                $html .= "<li>" . $resultado['email'] . "</li>";
            $html .= "</ul>";
            return new Response("<html><body>$html</body>");
        }
        return new Response("<html><body>Contacto $codigo no encontrado</body>");*/

        $resultado = ($this->contactos[$codigo] ?? null);

        return $this->render('ficha_contacto.html.twig', ['contacto' => $resultado]);
    }
}
 

