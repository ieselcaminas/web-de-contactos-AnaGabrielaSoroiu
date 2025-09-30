<?php

namespace App\Controller;

use App\Entity\Contacto;
use App\Repository\ContactoRepository;
use Doctrine\Persistence\ManagerRegistry;
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
   
     #[Route('/contacto/insertar', name: 'insertar')]
    public function insertar(ManagerRegistry $doctrine) {
        $entityManager = $doctrine->getManager();
        foreach($this->contactos as $c){
            $contacto = new Contacto();
            $contacto->setNombre($c["nombre"]);
            $contacto->setTelefono($c["telefono"]);
            $contacto->setEmail($c["email"]);
            $entityManager->persist($contacto);
        } 
        try {
            $entityManager->flush();
            return new Response("Contactos insertados");
        } catch (\Exception $e) {
            return new Response("Error insertando objetos");
        }
    }

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

    #[Route('/contacto/{nombre?Juan Pérez}', name: 'ficha contacto')]
    public function ficha(ManagerRegistry $doctrine, $nombre): Response {
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
        return new Response("<html><body>Contacto $codigo no encontrado</body>");
        $resultado = ($this->contactos[$codigo] ?? null);
        return $this->render('ficha_contacto.html.twig', ['contacto' => $resultado]);*/

        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($nombre);

        return $this->render('ficha_contacto.html.twig', 
        ['contacto' => $contacto]);
    }

    #[Route('/contacto/buscar/{texto}', name: 'buscar contacto')]
    public function buscar(ContactoRepository $repositorio, $texto): Response{
        $contactos = $repositorio->findByName($texto);

        return $this->render('lista_contacto.html.twig', 
        ['contactos' => $contactos]);
    }

    #[Route('/contacto/update/{telefono}/{nombre}', name: 'modificar contacto')]
    public function update(ManagerRegistry $doctrine, $nombre, $telefono): Response {
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($nombre); 
        if($contacto) {
            $contacto->setTelefono($telefono);   
            try {
                $entityManager->flush();
                return $this->render(('ficha_contacto.html.twig'), 
                    ['contacto' => $contacto]);
            } catch (\Exception $e) {
                return new Response("Error insertando objetos");
            }  
        } else {
            return $this->render('ficha_contacto.html.twig', 
            ['contacto' => null]);
        }
     
    }

    #[Route('/contacto/delete/{nombre}', name: 'eliminar contacto')]
    public function delete(ManagerRegistry $doctrine, $nombre): Response{
        $entityManager = $doctrine->getManager();
        $repositorio = $doctrine->getRepository(Contacto::class);
        $contacto = $repositorio->find($nombre);
        if($contacto){
            try{
                $entityManager->remove($contacto);
                $entityManager->flush();
                return new Response("Contacto eliminado");
            } catch (\Exception $e) {
                return new Response("Error eliminando objeto");
            } 
        } else {
            return $this->render('ficha_contacto.html.twig', 
            ['contacto' => null]);
        }
    }
}
 

